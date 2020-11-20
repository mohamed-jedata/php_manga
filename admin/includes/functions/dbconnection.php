<?php


define('SERVERNAME','localhost');
define('USERNAME', 'root');
define('PASSWORD','');
define('DATABASE','manga');

try{

    $dsn = "mysql:host=".SERVERNAME.";dbname=".DATABASE;

    $conn = new PDO($dsn,USERNAME,PASSWORD);

    $conn->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
 
  

}catch(PDOException $e){

    echo "connection failed : $e";
    die();

}








?>