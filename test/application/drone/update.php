<?php
/**
 * Created by PhpStorm.
 * User: Simon
 * Date: 3/24/2019
 * Time: 6:42 AM
 */

$required = True;
require_once "../constant.php";

$token = $_GET['token'];
$droneid = $_GET['droneid'];
$dserialnumber =  $_GET['dsn'];

$data = array();
if(isset($_GET['dsn'])){
    $data["dserialnumber"] = $dserialnumber;
}

try{
    $conn = new PDO($tns,$db_username,$db_password);
    if($conn){
        $sql = get_permission_sql($token);
        $query = $conn->query($sql);
        $rows = $query->fetchAll();
        if(check_admin_permission($rows)){
            $sql2 = build_sql_update("admin.drone", $data, "droneid = '$droneid'");
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