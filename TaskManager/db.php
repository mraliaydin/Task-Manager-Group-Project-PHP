<?php

$dsn = "mysql:host=localhost;port=3306;dbname=toDo" ;
$user = "root" ;
$pass = "" ;

try {
  $db = new PDO($dsn, $user, $pass) ;
  $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION) ;
} catch( PDOException $ex) {
    echo "<p>Connection Error:</p>" ;
    echo "<p>", $ex->getMessage(), "</p>" ;
    exit ;
}
