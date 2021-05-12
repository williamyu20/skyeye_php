<?php
/**
 * Created by PhpStorm.
 * User: Wilson
 * Date: 2/26/2019
 * Time: 17:04
 */
include "checkIsLogined.php";
$errorMsg = "";
if($_SERVER["REQUEST_METHOD"] == "POST"){
    ini_set("allow_url_fopen", 1);
    if (formatInput($_POST["userPassword"]) == formatInput($_POST["userPasswordConfirm"])){
        $url = "http://ec2-54-179-174-219.ap-southeast-1.compute.amazonaws.com/application/user/create.php/?token=". $_SESSION["userDetails"]["userToken"] . "&username=" . formatInput($_POST["userName"]) . "&password=" . formatInput($_POST["userPassword"]) . "&staffid=" . $_SESSION["SID"] . "&role=" . $_POST["userRole"];
        $json = file_get_contents($url);
        $obj = json_decode($json);
        if($obj->status==0){
            $errorMsg = "* Create Success";
        }else if($obj->status==1){
            $errorMsg = "* User Exist";
        }else if($obj->status==2){
            $errorMsg = "* No Permission";
        }else if($obj->status==3){
            $errorMsg = "* PDO Error";
        }else if($obj->status==4){
            $errorMsg = "*  Insert Failed";
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
        echo "<title>Create User - SkyEye</title>";
    ?>
</head>
<body>
<?php
    //include "checkIsLogined.php";
    include "Userpagemenu.php";
?>

<div class="center">
    <form class="createUserBox" method="post" action="createUser.php">
        <?php
            echo $errorMsg;
        ?>
        <div class="form-group">
            <label for="userName">User Name</label>
            <input type="text" class="form-control" id="userName" name="userName" placeholder="User Name" required value="">
        </div>
        
        <div class="form-group">
            <label for="userPassword">User Password</label>
            <input type="password" class="form-control" id="userPassword" name="userPassword" placeholder="User Password" required value="">
        </div>
        
        <div class="form-group">
            <label for="userPasswordConfirm">User Password Confirm</label>
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
            <button type="submit" class="btn btn-primary btn-SkyEyeGreen">Create</button>
        </div>
    </form>
</div>
</body>
</html>
