<?php
    require_once "./db.php";




$workId = $_POST['workId'];
//echo $music_number;
global $db ;
$stmt = $db->prepare("delete from work where workId = ?") ;
$stmt->execute([$workId]) ;





?>


