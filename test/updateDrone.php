<?php
include "checkIsLogined.php";
$errorMsg = "";
if($_SERVER["REQUEST_METHOD"] == "POST"){
    ini_set("allow_url_fopen", 1);
    if (formatInput($_POST["droneSerialNumber"]) == formatInput($_POST["droneSerialNumberConfirm"])){
        $url = "http://localhost/ogweb/application/drone/update.php/?token=". $_SESSION["userDetails"]["userToken"] . "&droneID=". formatInput($_POST["droneID"]) . "&dsn=" . formatInput($_POST["droneSerialNumber"]);
        $json = file_get_contents($url);
        $obj = json_decode($json);
        if($obj->status==0){
            $errorMsg = "* Update Drone Success";
        }else if($obj->status==1){
            $errorMsg = "* Update Drone Failed";
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
        echo "<title>Update Drone - SkyEye</title>";
    ?>
</head>
<body>
<?php
    //include "checkIsLogined.php";
    include "Dronepagemenu.php";
?>

<div class="center">
    <form class="updateDroneBox" method="post" action="updateDrone.php">
        <?php
            echo $errorMsg;
        ?>
        
        <div class="form-group">
            <label for="userName">Drone ID</label>
            <input type="text" class="form-control" id="droneID" name="droneID" placeholder="Drone ID" required value="">
        </div>
        
        <div class="form-group">
            <label for="userName">Drone Serial Number</label>
            <input type="text" class="form-control" id="droneSerialNumber" name="droneSerialNumber" placeholder="Drone Serial Number" required value="">
        </div>
        
        <div class="form-group">
            <label for="userPassword">Drone Serial Number Confirm</label>
            <input type="text" class="form-control" id="droneSerialNumberConfirm" name="droneSerialNumberConfirm" placeholder="Drone Serial Number Confirm" required value="">
        </div>
        
        <div align="center">
            <button type="submit" class="btn btn-primary btn-SkyEyeGreen">Update</button>
        </div>
    </form>
</div>
</body>
</html>
