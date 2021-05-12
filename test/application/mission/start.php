<?php
/**
 * Created by PhpStorm.
 * User: Simon
 * Date: 3/19/2019
 * Time: 2:06 AM
 */

$required = True;
require_once "../constant.php";

$token = $_GET['token'];
$mid = $_GET['mid'];

try{
    $conn = new PDO($tns,$db_username,$db_password);
    if($conn){
        $sql = get_permission_sql($token);
        $query = $conn->query($sql);
        $rows = $query->fetchAll();
        if(check_admin_permission($rows)){
            $sql2 = "UPDATE admin.mission SET mstarttime = CURRENT_TIMESTAMP(), mstatus = 1 WHERE mid = '$mid' AND mstatus != 4 AND mstatus = 0";
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
    0: Start Mission Success
    1: Start Mission Failed
    2: Permission Denied
    3: PDO error
*/
