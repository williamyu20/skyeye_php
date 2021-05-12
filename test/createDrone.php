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
    if (formatInput($_POST["droneSerialNumber"]) == formatInput($_POST["droneSerialNumberConfirm"])){
        $url = "http://ec2-54-179-174-219.ap-southeast-1.compute.amazonaws.com/application/drone/create.php/?token=". $_SESSION["userDetails"]["userToken"] . "&dsn=" . formatInput($_POST["droneSerialNumber"]);
        $json = file_get_contents($url);
        $obj = json_decode($json);
        if($obj->status==0){
            $errorMsg = "* Create Success";
        }else if($obj->status==1){
            $errorMsg = "* Drone Exist";
        }else if($obj->status==2){
            $errorMsg = "* No Permission";
        }else if($obj->status==3){
            $errorMsg = "* PDO Error";
        }else if($obj->status==4){
            $errorMsg = "*  Insert Failed";
        }
    }else{
        $errorMsg = "* The Drone Serial Number should be same with the Confirm Drone Serial Number";
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
        echo "<title>Create Drone - SkyEye</title>";
    ?>
</head>
<body>
<?php
    //include "checkIsLogined.php";
    include "Dronepagemenu.php";
?>

<div class="center">
    <form class="createDroneBox" method="post" action="createDrone.php">
        <?php
            echo $errorMsg;
        ?>
        <div class="form-group">
            <label for="userName">Drone Serial Number</label>
            <input type="text" class="form-control" id="droneSerialNumber" name="droneSerialNumber" placeholder="Drone Serial Number" required value="">
        </div>
        
        <div class="form-group">
            <label for="userPassword">Drone Serial Number Confirm</label>
            <input type="text" class="form-control" id="droneSerialNumberConfirm" name="droneSerialNumberConfirm" placeholder="Drone Serial Number Confirm" required value="">
        </div>

        <div align="center">
            <button type="submit" class="btn btn-primary btn-SkyEyeGreen">Create</button>
        </div>
    </form>
</div>
</body>
</html>
