<?php
    session_start();
    header("Content-Type: application/json") ;
    require_once "./db.php";

    // istediğimiz listenin elementlerini json
    

    if ( $_SERVER["REQUEST_METHOD"] == "GET") {
        $response = getImportantWorks();
    }


    echo json_encode($response) ;

    function getImportantWorks() {
        global $db; // $db is a global variable.
        $stmt = $db->prepare("select * from work where important = ? AND completed = ?" ) ;
        $stmt->execute(["yes","no"]);
        $all = $stmt->fetchAll(PDO::FETCH_ASSOC) ;

        $data = [] ;
        foreach( $all as $work) {
            $row = [
                "workId" => $work["workId"],
                "workName" => filter_var($work["workName"],FILTER_SANITIZE_STRING),
                "belongTo" => $work["belongTo"]
            ] ;
            array_push($data, $row) ;
        }
        return $data ;
    }
