<?php
/**
 * Created by PhpStorm.
 * User: Simon
 * Date: 3/24/2019
 * Time: 5:11 AM
 */

$required = True;
require_once "../../constant.php";

$token = $_GET['token'];
$uid = $_GET['userid'];
$mid = $_GET['mid'];
$jid = $_GET['jid'];
$status = $_GET['status'];

$status = strtolower($status);

try{
    $conn = new PDO($tns,$db_username,$db_password);
    if($conn){
        $sql = get_permission_sql($token);
        $query = $conn->query($sql);
        $rows = $query->fetchAll();
        if(check_admin_permission($rows)){
            $sql2 = "";
            if($status == "start"){
                $sql2 = "UPDATE admin.usermission SET starttime=CURRENT_TIMESTAMP() WHERE userid='$uid' AND mid='$mid' AND jid='$jid'";
            }elseif ($status == "end"){
                $sql2 = "UPDATE admin.usermission SET endtime=CURRENT_TIMESTAMP() WHERE userid='$uid' AND mid='$mid' AND jid='$jid'";
            }
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
