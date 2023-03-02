<?php


// if the user has already authenticated, go to main.php directly
if ( isset($_SESSION["user"]) ) {
    header("Location: main.php");
    exit ;
}