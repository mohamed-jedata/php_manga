<?php
session_start();

$pageTitle="MangaScore | Manage Members";
require_once 'init.php';


$action = isset($_GET['action']) ? rawurlencode($_GET['action']) : "Manage";

if($action == "Manage"){
?>
    <!-- Start Manage members page -->

<div class="container manage-members">

    <h1 class="text-center" >Manage Members</h1>

    <?php echo showMessages() ?>   
 
    <table class="table table-bordered">
        <thead>
            <tr>
            <th scope="col">#</th>
            <th scope="col">Username</th>
            <th scope="col">Fullname</th>
            <th scope="col">Email</th>
            <th scope="col">Registed Date</th>   
            <th scope="col">Control</th>   
            </tr>
        </thead>
        <tbody> 
        <?php 
        $allUsers = $crud->getAllFrom("ID,Username,Fullname,Email,DATE_FORMAT(`RegistedDate`, '%Y-%m-%d') AS `RegistedDate`","users","");
        if(!empty($allUsers)) : 
        foreach($allUsers as $user){
            echo '<tr>';
                echo '  <th scope="row">'.$user['ID'].'</th>';
                echo '  <td>'.$user['Username'].'</td>';
                echo '  <td>'.$user['Fullname'].'</td>';
                echo '  <td>'.$user['Email'].'</td>';
                echo '  <td>'.$user['RegistedDate'].'</td>';
                echo '  <td>';
                echo '<a class="btn btn-success" href="?action=Edit&id='.$user['ID'].'"><i class="fas fa-pencil-alt"></i> Edit</a> ';
                echo '<a class="btn btn-danger confirm" href="?action=Delete&id='.$user['ID'].'"><i class="fas fa-times"></i> Delete</a>
                </td>';
            echo '</tr>';
        }
        else :
            echo '<tr align="center"><th colspan="6">No member is Added :)</th></tr>';
        endif;
        ?>
            </tbody>
        </table>

<a class="btn btn-light" href="members.php?action=Add" style="border:3px solid #e3f2fd"><i class="fas fa-plus"></i> Add new Member</a>  


</div>

<!-- End  Manage members page -->

<?php
}elseif($action == "Add"){
?>
<!-- Start add member page -->

<div class="container">

    <h1 class="text-center" >Add a Member</h1>

    <div class="row">
       <div class="offset-md-3 col-md-6">

       <!-- Show form errors -->
        <?php 
         showMessages();
        ?>
        <form class="mt-3" method="POST" action="?action=Add">
            <div class="form-group">
                <label for="Username">Username :</label>
                <input type="text" pattern=".{3,24}" title="Username must be between more than 2 and less than 25" name="Username" class="form-control" id="Username" required>
            </div>

            <div class="form-group">
                <label for="Password">Password :</label>
                <input type="text" pattern=".{3,24}" title="Password must be between more than 2 and less than 25" name="Password" class="form-control" id="Password" required>
            </div>

            <div class="form-group">
                <label for="Fullname">Fullname :</label>
                <input type="text" name="Fullname" class="form-control" id="Fullname">
            </div>

            <div class="form-group">
                <label for="Email">E-Mail :</label>
                <input type="email" name="Email" class="form-control" id="Email" required> 
            </div>

            <div class="form-group">
                <label>Administrator : </label>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="IsAdmin" value="0" id="inlineRadio1" checked>
                    <label class="form-check-label" for="inlineRadio1">No</label>
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="IsAdmin" value="1"  id="inlineRadio2" >
                    <label class="form-check-label" for="inlineRadio2">Yes</label>
                </div>
            </div>
            <button type="submit" name="AddMember" class="btn btn-primary">Add Member</button>
        </form>

      </div>
    </div>


</div>


<!-- End add member page -->

<!-- Start insert info  -->
<?php
if($_SERVER["REQUEST_METHOD"] == "POST"){

    $Username = $_POST['Username'];
    $Password = $_POST['Password'];
    $Fullname = $_POST['Fullname'];
    $Email    = $_POST['Email'];
    $IsAdmin  = $_POST['IsAdmin'] ;
    

    $errors = array();

    //validate usersname
    if(empty(trim($Username))){
        $errors[] = "Username cannot be empty !!";
    }elseif(strlen($Username) >= 25){
        $errors[] = "Username cannot be larger than 25 Characters !!";
    }elseif(strlen($Username) <= 2){
        $errors[] = "Username is too Short !!";
    }

    //validate password
    if(empty(trim($Password))){
        $errors[] = "Password cannot be empty !!";
    }elseif(strlen($Password) >= 25){
        $errors[] = "Password cannot be larger than 25 Characters !!";
    }elseif(strlen($Password) <= 2){
        $errors[] = "Password is too Short !!";
    }

    //validate fullname if it is not it empty
    if( !empty(trim($Fullname)) && strlen($Fullname) >= 50 ){
        $errors[] = "Fullname cannot be larger than 50 Characters !!";
    }elseif(!empty(trim($Fullname)) && strlen($Fullname) <= 2){
        $errors[] = "Fullname is too Short !!";
    }

     //check if email is not empty and is valid
    if(empty(trim($Email))){
        $errors[] = "Email Address cannot be empty !!";
    }elseif(!filter_var($Email,FILTER_VALIDATE_EMAIL)){
        $errors[] = "Email Address is not Valid !!";
    }

    if(empty($errors)){
        //insert into users
        $Password = password_hash($Password,PASSWORD_BCRYPT);
        $crud->InsertInto
        ("users",
             "Username,Password,Fullname,Email,IsAdmin",
             "$Username,$Password,$Fullname,$Email,$IsAdmin"
        );

        $_SESSION['msg'] = "New User Added Succefully !!";
        redirect("members.php");
               
    }else{
        $_SESSION['msg'] = $errors;
        header("Refresh:0");
    }


}
?>
<!-- End insert info  -->
<?php
}elseif($action == "Edit"){

     
//------- Start Edit member page 

//check if edit id is set

    $id = isset($_GET['id']) && is_numeric($_GET['id']) ? intval($_GET['id']) : 0;

    $userInfo = $crud->getAllFrom("*","users","where ID=$id","","","","",TRUE);
    if(!empty($userInfo)){

?>



    <div class="container">

    <h1 class="text-center" >Edit a Member</h1>

    <div class="row">
    <div class="offset-md-3 col-md-6">

    <!-- Show form errors -->
        <?php 
        showMessages();
        ?>
        <form class="mt-3" method="POST" action="?action=Edit&id=<?php echo $id ?>">
            <div class="form-group">
                <label for="Username">Username :</label>
                <input type="text" pattern=".{3,24}" value="<?php echo $userInfo['Username'] ?>" title="Username must be between more than 2 and less than 25" name="Username" class="form-control" id="Username" required>
            </div>

            <div class="form-group">
                <label for="Password">Password :</label>
                <input type="text"  placeholder="Old Password will stay if you dont put any" name="Password" class="form-control" id="Password">
            </div>

            <div class="form-group">
                <label for="Fullname">Fullname :</label>
                <input type="text" name="Fullname" value="<?php echo $userInfo['Fullname'] ?>" class="form-control" id="Fullname">
            </div>

            <div class="form-group">
                <label for="Email">E-Mail :</label>
                <input type="email" name="Email" value="<?php echo $userInfo['Email'] ?>" class="form-control" id="Email" required> 
            </div>

            <div class="form-group">
                <label>Administrator : </label>
                <div class="form-check form-check-inline">
                    <?php if($userInfo['IsAdmin'] != 1){ ?>
                      <input class="form-check-input" type="radio" name="IsAdmin" value="0" id="inlineRadio1" checked>
                    <?php }else{ ?>
                      <input class="form-check-input" type="radio" name="IsAdmin" value="0" id="inlineRadio1">
                    <?php } ?>
                    <label class="form-check-label" for="inlineRadio1">No</label>
                </div>
                <div class="form-check form-check-inline">
                    <?php if($userInfo['IsAdmin'] == 1){ ?>
                    <input class="form-check-input" type="radio" name="IsAdmin" value="1"  id="inlineRadio2" checked>
                    <?php }else{ ?>
                        <input class="form-check-input" type="radio" name="IsAdmin" value="1"  id="inlineRadio2" >
                    <?php } ?>
                    <label class="form-check-label" for="inlineRadio2">Yes</label>
                </div>
            </div>
            <button type="submit" name="editMember" class="btn btn-primary">Update</button>
        </form>

    </div>
    </div>


    </div>

    <!-- Start update info  -->
    <?php
    if($_SERVER["REQUEST_METHOD"] == "POST"){

    $Username = $_POST['Username'];
    $Password = $_POST['Password'];
    $Fullname = $_POST['Fullname'];
    $Email    = $_POST['Email'];
    $IsAdmin  = $_POST['IsAdmin'] ;


    $errors = array();

    //validate usersname
    if(empty(trim($Username))){
        $errors[] = "Username cannot be empty !!";
    }elseif(strlen($Username) >= 25){
        $errors[] = "Username cannot be larger than 25 Characters !!";
    }elseif(strlen($Username) <= 2){
        $errors[] = "Username is too Short !!";
    }

    //validate password
    if(!empty(trim($Password))) :
        if(strlen($Password) >= 25){
            $errors[] = "Password cannot be larger than 25 Characters !!";
        }elseif(strlen($Password) <= 2){
            $errors[] = "Password is too Short !!";
        }
    endif;

    //validate fullname if it is not it empty
    if( !empty(trim($Fullname)) && strlen($Fullname) >= 50 ){
        $errors[] = "Fullname cannot be larger than 50 Characters !!";
    }elseif(!empty(trim($Fullname)) && strlen($Fullname) <= 2){
        $errors[] = "Fullname is too Short !!";
    }

    //check if email is not empty and is valid
    if(empty(trim($Email))){
        $errors[] = "Email Address cannot be empty !!";
    }elseif(!filter_var($Email,FILTER_VALIDATE_EMAIL)){
        $errors[] = "Email Address is not Valid !!";
    }

    if(empty($errors)){

        //update user info
        
        if(!empty(trim($Password))){
            $Password = password_hash($Password,PASSWORD_BCRYPT);
        }else{
            $Password = $userInfo['Password'];
        }

        $rowUpdates = $crud->UpdateInfo("users",
        array('Username','Password','Fullname','Email','IsAdmin'),  //Fields to update
        array($Username,$Password,$Fullname,$Email,$IsAdmin),      //Values to set
        'ID',$id);                                                 //Condition

        $_SESSION['msg'] = $rowUpdates==1?"User Updated Succefully !!":"Nothing Updated !!";
        header("Refresh:0");
            
    }else{
        $_SESSION['msg'] = $errors;
        header("Refresh:0");
    }


    }

    //- End update info  

    //****** End Edit member page  *******//  

}else{
    //if id is invalid
    redirect("members.php");
}


}elseif($action == "Delete"){

   //------- Start Delete member page 

//check if edit id is set

$id = isset($_GET['id']) && is_numeric($_GET['id']) ? intval($_GET['id']) : 0;

$userInfo = $crud->getAllFrom("ID","users","where ID=$id","","","","",TRUE);
if(!empty($userInfo)){

    $crud->Delete("users","ID",$id);
    $_SESSION['msg'] = "Member Removed Succefully !!";
    redirect("members.php");

}else{
    redirect("members.php");
}

//****** End Delete member page  *******// 


}else{
    redirect("members.php");
}

?>













<?php
require_once $temp . "footer.php";

?>