<?php
/**
 * Created by PhpStorm.
 * User: Simon
 * Date: 3/19/2019
 * Time: 1:09 AM
 */

$required = True;
require_once "../constant.php";

$token = $_GET['token'];
$mid = $_GET['mid'];
$m_name = $_GET['m_name'];
$m_desc = $_GET['m_desc'];
$m_creaator = $_GET['m_creator'];
$m_loc_name = $_GET['loc_name'];
$m_loc_latlng = $_GET['latlng'];
$searching_radius = $_GET['searching_radius'];
$altitude = $_GET['altitude'];
settype($searching_radius, "double");
$lat = explode(",", $m_loc_latlng)[0];
settype($lat, "double");
$lng = explode(",", $m_loc_latlng)[1];
settype($lng, "double");
settype($altitude,"double");

$data = array();
if(isset($_GET['m_name'])){
    $data["mname"] = $m_name;
}
if(isset($_GET['m_desc'])){
    $data["mdesc"] = $m_desc;
}
if(isset($_GET['m_creator'])){
    $data["mcreator"] = $m_creaator;
}
if(isset($_GET['loc_name'])){
    $data["mlocationname"] = $m_loc_name;
}
if(isset($_GET['latlng'])){
    $data["centerlat"] = $lat;
    $data["centerlng"] = $lng;
}
if(isset($_GET['searching_radius'])){
    $data["mrange"] = $searching_radius;
}
if(isset($_GET['altitude'])){
    $data["maltitude"] = $altitude;
}
//print(json_encode($data)."\r\n");

try{
    $conn = new PDO($tns,$db_username,$db_password);
    if($conn){
        $sql = get_permission_sql($token);
        $query = $conn->query($sql);
        $rows = $query->fetchAll();
        if(check_admin_permission($rows)){
            $sql2 = build_sql_update("admin.mission", $data, "mid = '$mid'");
            //print($sql2."\r\n");
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
    0: Update Mission Success
    1: Update Mission Failed
    2: Permission Denied
    3: PDO error
*/
