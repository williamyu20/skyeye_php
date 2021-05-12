<?php
include "checkIsLogined.php";
$errorMsg = "";
if($_SERVER["REQUEST_METHOD"] == "POST"){
    ini_set("allow_url_fopen", 1);
    if (formatInput($_POST["userName"]) == formatInput($_POST["userNameConfirm"])){
        $url = "http://localhost/ogweb/application/user/delete.php/?token=". $_SESSION["userDetails"]["userToken"] . "&username=" . formatInput($_POST["userName"]);
        $json = file_get_contents($url);
        $obj = json_decode($json);
        if($obj->status==0){
            $errorMsg = "* Delete User Success";
        }else if($obj->status==1){
            $errorMsg = "* Delete User Failed";
        }else if($obj->status==2){
            $errorMsg = "* No Permission";
        }else if($obj->status==3){
            $errorMsg = "* PDO Error";
        }
    }else{
        $errorMsg = "* The user name should be same with the confirm user name";
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
        echo "<title>Delete User - SkyEye</title>";
    ?>
</head>
<body>
<?php
    //include "checkIsLogined.php";
    include "Userpagemenu.php";
?>

<div class="center">
    <form class="deleteUserBox" method="post" action="deleteUser.php">
        <?php
            echo $errorMsg;
        ?>
        <div class="form-group">
            <label for="userName">User Name</label>
            <input type="text" class="form-control" id="userName" name="userName" placeholder="User Name" required value="">
        </div>
        
        <div class="form-group">
            <label for="userPassword">User Name Confirm</label>
            <input type="text" class="form-control" id="userNameConfirm" name="userNameConfirm" placeholder="user Name Confirm" required value="">
        </div>
        


        <div align="center">
            <button type="submit" class="btn btn-primary btn-SkyEyeGreen">Delete</button>
        </div>
    </form>
</div>
</body>
</html>
