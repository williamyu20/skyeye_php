<?php
/**
 * Created by PhpStorm.
 * User: Simon
 * Date: 3/24/2019
 * Time: 5:10 AM
 */

$required = True;
require_once "../../constant.php";

$token = $_GET['token'];
$droneid = $_GET['droneid'];
$mid = $_GET['mid'];
$userid = $_GET['userid'];
$battery = $_GET['battery'];
settype($battery, "double");
$latlng = $_GET['latlng'];

$data = array();
if(isset($_GET['userid'])){
    $data["userid"] = $userid;
}
if(isset($_GET['battery'])){
    $data["batterystatus"] = $battery;
}
if(isset($_GET['latlng'])){
    $lat = explode(",", $latlng)[0];
    settype($lat, "double");
    $lng = explode(",", $latlng)[1];
    settype($lng, "double");
    $data["lat"] = $lat;
    $data["lng"] = $lng;
}

try{
    $conn = new PDO($tns,$db_username,$db_password);
    if($conn){
        $sql = get_permission_sql($token);
        $query = $conn->query($sql);
        $rows = $query->fetchAll();
        if(check_permission($rows)){
            $sql2 = build_sql_update("admin.dronemission", $data, "droneid='$droneid' AND mid='$mid'");
            $query2 = $conn->prepare($sql2);
            $query2->execute();
            if($query2->rowCount() > 0){
                $json = array("status"=>0);
            }else{
                $json = array("status" => 1);
            }
        }else{
            $json = array("status"=>2);
        }
    }
}catch(PDOException $e){
    $json = array("status"=>3);
}
print(json_encode($json, JSON_PRETTY_PRINT));


/*
Status code:
    0: Update Drone Success
    1: Update Drone Failed
    2: Permission Denied
    3: PDO error
*/