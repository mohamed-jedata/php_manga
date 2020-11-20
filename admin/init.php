<?php


$css = "layout/css/";   //css directory
$js = "layout/js/";   //css directory
$temp = "includes/templates/" ; //templates directory
$func = "includes/functions/" ; //functions directory

if(!isset($pageTitle)){
    $pageTitle = "Manga Website | Read online";
}
 
include_once $func."dbconnection.php";  //connection to mysql
include_once $func."crud.php";          //create insert delete select funcrions
include_once $func."functions.php";     //import functions.php

$crud = new Crud($conn);

if(!isset($noHeader))
include_once $temp."header.php";
if(!isset($noNavbar))
include_once $temp."navbar.php";


?>