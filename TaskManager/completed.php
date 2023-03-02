<?php

require_once "./db.php" ;

$workId= $_POST["workId"];


try {
    $sql = "update work set completed=:completed where workId = :workId" ;
    $rs = $db->prepare($sql) ;
    
    global $db; // $db is a global variable.
    $stmt = $db->prepare("select * from work where workId = ?") ;
    $stmt->execute([$_POST["workId"]]);
    $all = $stmt->fetchAll(PDO::FETCH_ASSOC) ;
    foreach( $all as $work) {
        if($work["completed"] === "yes"){
            $rs->execute(["completed" => "no", "workId" => $workId ]) ;  
            
        }else{
            $rs->execute(["completed" => "yes", "workId" => $workId ]) ;  
        }
    }
}catch (PDOException $ex) {}




