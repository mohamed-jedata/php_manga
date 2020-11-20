<?php
ob_start();
session_start();
require_once 'init.php';

$pageTitle = "Manage Manga";

$action = isset($_GET['action']) ? rawurlencode($_GET['action']) : "Manage";

if($action == "Manage"){
?>
    <!-- Start Manage manga page -->

<div class="container manage-manga">

    <h1 class="text-center" >Manage Manga</h1>

    <?php echo showMessages() ?>   
 
     <div class="row">
        <?php
            $mangas = $crud->getAllFrom("*","manga","","ID","DESC");
           foreach($mangas as $manga){ 
        ?>
       <div class="col-md-3 ">
            <div class="card" >
                    <span class="modify">
                        <a class="btn btn-success" href="?action=Edit&id=<?php echo $manga['ID']; ?>"><i class="fas fa-pencil-alt"></i> Edit</a> 
                        <a class="btn btn-danger confirm" href="?action=Delete&id=<?php echo $manga['ID']; ?>"><i class="fas fa-times"></i> Delete</a>           
                    </span>
                    <img  src="<?php echo "uploads/".$manga['Cover_img'] ?>"  class="card-img-top" alt="...">
                    <div class="card-body">
                        <h4 class="card-title"><?php echo $manga['Title']; ?></h4>
                        <p class="card-text">
                            <ul class="list-unstyled">
                                <li><span>Author</span> : <?php echo $manga['Author']; ?></li>
                                <li><span>Chapters</span> : -</li>
                                <li><span>Genres</span> : <?php echo $manga['Genres']; ?></h4></li>
                                <li><span>Status</span> : <?php echo getStatus($manga['Status']); ?></li>
                                <li ><span>Story</span> :<p class="desc"> <?php echo $manga['Description']; ?></p></li>                               
                            </ul>
                        </p> 
                    </div>
            </div>
       </div>
        <?php }?>   
     </div>
    

<a class="btn btn-light mb-5" href="?action=Add" style="border:3px solid #e3f2fd"><i class="fas fa-plus"></i> Add new Manga</a>  


</div>

<!-- End  Manage manga page -->

<?php
}elseif($action == "Add"){
?>
<!-- Start add manga page -->

<div class="container add-manga">

    <h1 class="text-center" >Add a Manga</h1>

    <div class="row">
       <div class="offset-md-3 col-md-6">

       <!-- Show form errors -->
        <?php 
         showMessages();
        ?>
        <form class="mt-3" method="POST" action="?action=Add" enctype="multipart/form-data">


            <div class="form-group">
                <label for="title">Title :</label>
                <input type="text" pattern=".{3,24}" title="Title must be between more than 3 and less than 25" name="Title" class="form-control" id="title" required>
            </div>

            <div class="form-group">
                <label for="status">Status :</label>
                <select name="Status" id="status" class="form-control" required>
                    <option value="">Choose...</option>
                    <option value="1">Ongoing</option>
                    <option value="2">Stopped</option>
                    <option value="3">Ended</option>
                </select>     
            </div>

            <div class="form-group">
                <label for="Genres">Genres : </label>
                <input type="text" name="Genres" placeholder="Separated with coma (,)" class="form-control" id="Genres" required>
            </div>

            <div class="form-group">
                <label for="Author">Author : </label>
                <input type="text" name="Author" placeholder="Separated with coma (,)" class="form-control" id="Author" >
            </div>

            <div class="form-group">
                <label for="desc">Description : </label>
                <textarea name="Description"  class="form-control" rows="5" id="desc" required></textarea>
            </div>

            <div class="form-group">
                <label for="Upload">Upload Manga Cover :</label>
                <input type="file" accept="image/*" name="Cover_img" class="form-control btn-light" id="Upload" required>
            </div>
           
            <button type="submit" name="AddManga" class="btn btn-primary">Add Manga</button>
        </form>

      </div>
    </div>


</div>


<!-- End add manga page -->

<!-- Start insert manga  -->
<?php
if($_SERVER["REQUEST_METHOD"] == "POST"){

    $errors = array();

    $Title = filter_var($_POST['Title'],FILTER_SANITIZE_STRING);
    $Status = $_POST['Status'];
    $Genres = $_POST['Genres'];
    $Description = $_POST['Description'];
    $Author = $_POST['Author'];

    //validate uploaded image
    $img_Name  = $_FILES['Cover_img']['name'] ;
    $img_Size  = $_FILES['Cover_img']['size'];
    $img_Tmp   = $_FILES['Cover_img']['tmp_name']; 
    $img_ext='';
    if(empty($img_Name)){
        $errors[] = "Cover image should be uploaded !!";
    }else{
        if($img_Size >= 4000000){     //check size
            $errors[] = "Cover image is larger than 4MB !!";
        }
        //check extenshien
        $allowedExts = array('png','jpg','jpge','gif','svg');
        $img_ext = explode('.',$img_Name);
        $img_ext = end($img_ext);
        if(!in_array($img_ext,$allowedExts)){
            $errors[] = "Cover Image is not Valid !!";
        }
    }


    //validate Title
    if(empty(trim($Title))){
        $errors[] = "Title cannot be empty !!";
    }elseif(strlen($Title) >= 25){
        $errors[] = "Title cannot be larger than 25 Characters !!";
    }elseif(strlen($Title) <= 2){
        $errors[] = "Title is too Short !!";
    }

    //validate Title
    if(empty(trim($Description))){
        $errors[] = "Description cannot be empty !!";
    }elseif(strlen($Description) < 5){
        $errors[] = "Description is too Short !!";
    }


    //validate Status
    if($Status == 0){
        $errors[] = "Status cannot be empty !!";
    }

    //validate Genres if it is not it empty
    if( empty($Genres) ){
        $errors[] = "Genres cannot be empty !!";
    }

    //check if title is already exict
    if(empty($errors)){
        $checkTitle = $crud->getAllFrom("ID","manga","where Title = '$Title'","","","","",True);
        if(!empty($checkTitle)){
            $errors[] = "Sorry, This Title is Already Taken !!";
        }
    }
    
    if(empty($errors)){
        //insert into mangas
      
        $Cover_img = rand(1000,100000000000).'_'.rand(0,102354058).'.'.$img_ext;   

        move_uploaded_file($img_Tmp,"uploads/$Cover_img");
        
        $crud->InsertInto
        ("manga",
             "Title,Status,Genres,Description,Author,Cover_img",
             array("$Title","$Status","$Genres","$Description","$Author","$Cover_img")
        );

        $_SESSION['msg'] = "Manga Added Succefully !!";
        // redirect("manga.php");
        header("Refresh:0");
               
    }else{
        $_SESSION['msg'] = $errors;
        header("Refresh:0");
    }


}

//<!-- End insert info  -->

}elseif($action == "Edit"){

     
//------- Start Edit manga  

//check if edit id is set

    $id = isset($_GET['id']) && is_numeric($_GET['id']) ? intval($_GET['id']) : 0;

    $mangaInfo = $crud->getAllFrom("*","manga","where ID=$id","","","","",TRUE);
    if(!empty($mangaInfo)){
?>

<!---Start edit manga--->


<div class="container add-manga">

    <h1 class="text-center" >Edit Manga</h1>

    <div class="row">
       <div class="offset-md-3 col-md-6">

       <!-- Show form errors -->
        <?php 
         showMessages();
        ?>
        <form class="mt-3" method="POST" action="?action=Edit&id=<?php echo $id ?>" enctype="multipart/form-data">


            <div class="form-group">
                <label for="title">Title :</label>
                <input type="text" value="<?php echo $mangaInfo['Title'] ?>" pattern=".{3,24}" title="Title must be between more than 3 and less than 25" name="Title" class="form-control" id="title" required>
            </div>

            <div class="form-group">
                <label for="status">Status :</label>
                <select name="Status" id="status" class="form-control" required>
                    <option value="1" <?php echo $mangaInfo['Status']==1?"selected":'' ?>>Ongoing</option>
                    <option value="2" <?php echo $mangaInfo['Status']==2?"selected":'' ?>>Stopped</option>
                    <option value="3" <?php echo $mangaInfo['Status']==3?"selected":'' ?>>Ended</option>
                </select>     
            </div>

            <div class="form-group">
                <label for="Genres">Genres : </label>
                <input type="text" value="<?php echo $mangaInfo['Genres'] ?>" name="Genres" placeholder="Separated with coma (,)" class="form-control" id="Genres" required>
            </div>

            <div class="form-group">
                <label for="Author">Author : </label>
                <input type="text" value="<?php echo $mangaInfo['Author'] ?>" name="Author" placeholder="Separated with coma (,)" class="form-control" id="Author" >
            </div>

            <div class="form-group">
                <label for="desc">Description : </label>
                <textarea name="Description"  class="form-control" rows="5" id="desc" required><?php echo $mangaInfo['Description'] ?></textarea>
            </div>

            <div class="form-group">
                <label for="Upload">Upload Manga Cover :</label>
                <input type="file" accept="image/*" name="Cover_img" class="form-control btn-light" id="Upload">
            </div>
           
            <button type="submit" name="AddManga" class="btn btn-primary">Update Manga</button>
        </form>

      </div>
    </div>


</div>



<!---End update manga--->
<?php


if($_SERVER["REQUEST_METHOD"] == "POST"){

    $errors = array();

    $Title = filter_var($_POST['Title'],FILTER_SANITIZE_STRING);
    $Status = $_POST['Status'];
    $Genres = $_POST['Genres'];
    $Description = $_POST['Description'];
    $Author = $_POST['Author'];

    //validate uploaded image
    $img_Name  = $_FILES['Cover_img']['name'] ;
    $img_Size  = $_FILES['Cover_img']['size'];
    $img_Tmp   = $_FILES['Cover_img']['tmp_name']; 
    $img_ext='';
    if(!empty($img_Name)){
        if($img_Size >= 4000000){     //check size
            $errors[] = "Cover image is larger than 4MB !!";
        }
        //check extenshien
        $allowedExts = array('png','jpg','jpge','gif','svg');
        $img_ext = explode('.',$img_Name);
        $img_ext = end($img_ext);
        if(!in_array($img_ext,$allowedExts)){
            $errors[] = "Cover Image is not Valid !!";
        }
    }


    //validate Title
    if(empty(trim($Title))){
        $errors[] = "Title cannot be empty !!";
    }elseif(strlen($Title) >= 25){
        $errors[] = "Title cannot be larger than 25 Characters !!";
    }elseif(strlen($Title) <= 2){
        $errors[] = "Title is too Short !!";
    }

    //validate Title
    if(empty(trim($Description))){
        $errors[] = "Description cannot be empty !!";
    }elseif(strlen($Description) < 5){
        $errors[] = "Description is too Short !!";
    }


    //validate Status
    if($Status == 0){
        $errors[] = "Status cannot be empty !!";
    }

    //validate Genres if it is not it empty
    if( empty($Genres) ){
        $errors[] = "Genres cannot be empty !!";
    }

    //check if title is already exict
    if(empty($errors)){
        $checkTitle = $crud->getAllFrom("ID","manga","where Title = '$Title' and ID != $id","","","","",True);
        if(!empty($checkTitle)){
            $errors[] = "Sorry, This Title is Already Taken !!";
        }
    }
    
    if(empty($errors)){
        //update manga
        if(!empty($img_ext)){
           $Cover_img = rand(1000,100000000000).'_'.rand(0,102354058).'.'.$img_ext; 
           move_uploaded_file($img_Tmp,"uploads/$Cover_img") ;
        }else{
           $Cover_img = $mangaInfo['Cover_img'];
        }
        
        $crud->UpdateInfo("manga",
          array("Title","Status","Genres","Description","Author","Cover_img"),
          array("$Title","$Status","$Genres","$Description","$Author","$Cover_img"),
          "ID",$id
        );

        $_SESSION['msg'] = "Manga Updated Succefully !!";
        redirect("manga.php");
        //header("Refresh:0");
               
    }else{
        $_SESSION['msg'] = $errors;
        header("Refresh:0");
    }


}




//End update manga
}else{
    //if manga id is invalid
    redirect("manga.php");
}


}elseif($action == "Delete"){

   //------- Start Delete manga page 

//check if edit id is set

$id = isset($_GET['id']) && is_numeric($_GET['id']) ? intval($_GET['id']) : 0;

$userInfo = $crud->getAllFrom("ID","manga","where ID=$id","","","","",TRUE);
if(!empty($userInfo)){

    $crud->Delete("manga","ID",$id);
    $_SESSION['msg'] = "Manga Removed Succefully !!";
    redirect("manga.php");

}else{
    redirect("manga.php");
}

//****** End Delete member page  *******// 


}else{
    redirect("manga.php");
}

?>













<?php
require_once $temp . "footer.php";
ob_flush();
?>