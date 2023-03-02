<?php
    header("Content-Type: application/json") ;
    require_once "./db.php";

    // istediğimiz listenin elementlerini json
    

    if ( $_SERVER["REQUEST_METHOD"] == "GET") {
        $response = getWorks($_GET["listId"]);
    }

    if ( $_SERVER["REQUEST_METHOD"] == "POST") {
        $response = addWork($_POST["task"],$_POST["listId"]);
        }


    echo json_encode($response) ;

    function addWork($workName,$listId) {
        global $db ;
        $stmt = $db->prepare("insert into work (workName,belongTo) values (?,?)") ;
        $stmt->execute([$workName,$listId]) ;
        return ["valid" => true] ; 
    }

    function getWorks($listId) {
        global $db; // $db is a global variable.
        $stmt = $db->prepare("select * from work where belongTo = ?") ;
        $stmt->execute([$listId]);
        $all = $stmt->fetchAll(PDO::FETCH_ASSOC) ;
        $data = [] ;
        foreach( $all as $work) {
            $row = [
                "workId" => $work["workId"],
                "workName" => filter_var($work["workName"],FILTER_SANITIZE_STRING),
                "important" => $work["important"],
                "completed" => $work["completed"]
            ] ;
            array_push($data, $row) ;
        }
        return $data ;
    }

    