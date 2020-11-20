<?php

$noHeader = "";$noNavbar = "";

require_once 'init.php';

 //get lastest chapter number
if(isset($_GET['manga_id']) && !empty($_GET['manga_id'])){
      $manga_id = $_GET['manga_id'];
      if(empty($crud->getAllFrom('Number','chapters',"where Manga_ID = $manga_id","Number","Desc",1,"",True)['Number']))
         $chapter_number = 0;
      else
         $chapter_number = $crud->getAllFrom('Number','chapters',"where Manga_ID = $manga_id","Number","Desc",1,"",True)['Number'];
        
      $chapter_number = $chapter_number+1  ;
      echo $chapter_number;
}

        




?>