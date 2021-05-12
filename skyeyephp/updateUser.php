<?php
include "checkIsLogined.php";
$errorMsg = "";
if($_SERVER["REQUEST_METHOD"] == "POST"){
    ini_set("allow_url_fopen", 1);
    if (formatInput($_POST["userPassword"]) == formatInput($_POST["userPasswordConfirm"])){
        $url = "http://localhost/ogweb/application/user/update.php/?token=". $_SESSION["userDetails"]["userToken"] . "&userid=". formatInput($_POST["userID"]) . "&username=" . formatInput($_POST["newuserName"]). "&password=" . formatInput($_POST["userPassword"]) . "&role=" .$_POST["userRole"];
        $json = file_get_contents($url);
        $obj = json_decode($json);
        if($obj->status==0){
            $errorMsg = "* Update User Success";
        }else if($obj->status==1){
            $errorMsg = "* Update User Failed";
        }else if($obj->status==2){
            $errorMsg = "* No Permission";
        }else if($obj->status==3){
            $errorMsg = "* PDO Error";
        }
    }else{
        $errorMsg = "* The password should be same with the confirm password";
    };
}

function formatInput($input){
    $input = trim($input);
    $input = stripslashes($input);
    $input = htmlspecialchars($input);
    return $input;
}        

?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <?php
        include "header.php";
        echo "<title>Update User - SkyEye</title>";
    ?>
</head>
<body>
<?php
    //include "checkIsLogined.php";
    include "Userpagemenu.php";
?>

<div class="center">
    <form class="updateUserBox" method="post" action="updateUser.php">
        <?php
            echo $errorMsg;
        ?>
        
        <div class="form-group">
            <label for="userName">User ID</label>
            <input type="text" class="form-control" id="userID" name="userID" placeholder="User ID" required value="">
        </div>
        
        <div class="form-group">
            <label for="userName">New User Name</label>
            <input type="text" class="form-control" id="newuserName" name="newuserName" placeholder="New User Name" required value="">
        </div>
        
        <div class="form-group">
            <label for="userPassword">New User Password</label>
            <input type="password" class="form-control" id="userPassword" name="userPassword" placeholder="User Password" required value="">
        </div>
        
        <div class="form-group">
            <label for="userPasswordConfirm">New User Password Confirm</label>
            <input type="password" class="form-control" id="userPasswordConfirm" name="userPasswordConfirm" placeholder="User Password Confirm" required value="">
        </div>
        
        <div class="form-group">
            <label for="userRole">Role</label>
            <select name="userRole" class="form-control" id="userRole" name="userRole" placeholder="User Role" >
                <option value="user" selected>User</option>
                <option value="admin" >Admin</option>
            </select>
        </div>
        


        <div align="center">
            <button type="submit" class="btn btn-primary btn-SkyEyeGreen">Update</button>
        </div>
    </form>
</div>
</body>
</html>
