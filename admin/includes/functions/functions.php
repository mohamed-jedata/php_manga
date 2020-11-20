<?php



//redirect method
function redirect($url){
    header("Location: $url");
}

function showMessages($sessionName="msg"){
    if(isset($_SESSION[$sessionName])):
        $msg = $_SESSION[$sessionName];
        $_SESSION[$sessionName]=NULL;
        if(is_array($msg)){
            foreach($msg as $m){
                echo ' <div class="alert alert-danger mb-0 alert-dismissible fade show" role="alert">';
                echo     $m;
                echo '   <button type="button" class="close" data-dismiss="alert" aria-label="Close">';
                echo '     <span aria-hidden="true">&times;</span>';
                echo '   </button>';
                echo ' </div>';
            }
        }else{
                echo ' <div class="alert alert-success alert-dismissible fade show" role="alert">';
                echo     $msg;
                echo '   <button type="button" class="close" data-dismiss="alert" aria-label="Close">';
                echo '     <span aria-hidden="true">&times;</span>';
                echo '   </button>';
                echo ' </div>';
        } 
     endif;
}

//get status by associated numbers

function getStatus($num){

    $genre="";

    if($num == 1){
        $genre= "Ongoing";
    }elseif($num == 2){
        $genre= "Stopped";
    }elseif($num == 3){
        $genre= "Ended";
    }

    return $genre;
}






?>