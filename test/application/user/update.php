<?php
/**
 * Created by PhpStorm.
 * User: Simon
 * Date: 3/24/2019
 * Time: 3:00 AM
 */

$required = True;
require_once "../constant.php";

$token = $_GET['token'];
$uid = $_GET['userid'];
$username = $_GET['username'];
$password = $_GET['password'];
$role = $_GET['role'];
$sid = $_GET['staffid'];

$data = array();
if(isset($_GET['username'])){
    $data["uname"] = $username;
}
if(isset($_GET['password'])){
    $data["pwd"] = $password;
}
if(isset($_GET['role'])){
    $data["role"] = $role;
}
if(isset($_GET['sid'])){
    $data["sid"] = $sid;
}

try{
    $conn = new PDO($tns,$db_username,$db_password);
    if($conn){
        $sql = get_permission_sql($token);
        $query = $conn->query($sql);
        $rows = $query->fetchAll();
        if(check_admin_permission($rows)){
            $sql2 = build_sql_update("admin.useraccount", $data, "userid = '$uid'");
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
    0: Update User Success
    1: Update User Failed
    2: Permission Denied
    3: PDO error
*/