<?php
/**
 * Created by PhpStorm.
 * User: Wilson
 * Date: 2/24/2019
 * Time: 18:03
 */
session_start();
$development_mode = false;
$testUsername = "test1";
$testPassword = "12345678";

$errorMsg = "";
$_SESSION["lastActivity"] = time();

if($_SERVER["REQUEST_METHOD"] == "POST"){
    if($development_mode){
        $tempUserArray = array(
            "mode" => "development",
            "userToken" => "stub!"
        );
        $_SESSION["userDetails"] = $tempUserArray;
        header("Location: ./startMission.php");
        exit;
    }else{
        ini_set("allow_url_fopen", 1);
        $url ="http://ec2-54-179-248-71.ap-southeast-1.compute.amazonaws.com/application/user/login.php/?username=" . formatInput($_POST["username"]) . "&password=" . formatInput($_POST["password"]);
        //$url = 'http://' . $_SERVER['HTTP_HOST'] . "/testLogin.php"; //For relative path usage
        //$url = "https://dsafyp1819skyeye.azurewebsites.net/testLogin.php";
        $json = file_get_contents($url, true);
        $obj = json_decode($json);
        echo($json);
        switch($obj->status){
            case 0:
                if($obj->role=='admin'){
                    $token = $obj->token;
                    $userToken = $token;

                    $tempUserArray = array(
                        "mode" => "Rescue Team Leader",
                        "userToken" => $token,
                        "username" => $_POST["username"],
                    );
                    $_SESSION["userDetails"] = $tempUserArray;
                    header("Location: ./OngoingMission.php");
                    exit;
                }else{
                    $errorMsg = "* No Permission";
                    break;
                };
            case 1:
                $errorMsg = "* Invalid Username & Password";
                break;
            case 2:
                $errorMsg = "* Server Error";
                break;
        }
    }
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
    <?php include "header.php" ?>
    <title>Login - SkyEye</title>
</head>
<body>

<div class="center">
    <img src="image/skyeye_logo.png" class="img-fluid"/>
    <form class="loginBox" method="post" action="login.php">
        <?php
            echo $errorMsg;
        ?>
        <div class="form-group">
            <label for="username">Username</label>
            <input type="text" class="form-control" id="username" name="username" placeholder="Username" required value="<?php if($development_mode) echo $testUsername?>">
        </div>

        <div class="form-group">
            <label for="password">Password</label>
            <input type="password" class="form-control" id="password" name="password" placeholder="Password" required value="<?php if($development_mode) echo $testPassword?>">
        </div>
        <div align="center">
            <button type="submit" class="btn btn-primary btn-SkyEyeGreen">Login</button>
        </div>
    </form>
</div>

</body>
</html>
