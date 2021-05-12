<?php
/**
 * Created by PhpStorm.
 * User: Simon
 * Date: 3/30/2019
 * Time: 1:03 AM
 */

$required = True;
require_once "../constant.php";

$token = $_GET['token'];

try{
    $conn = new PDO($tns,$db_username,$db_password);
    if($conn){
        $sql = get_permission_sql($token);
        $query = $conn->query($sql);
        $rows = $query->fetchAll();
        if(check_permission($rows)){
            $sql = "SELECT * FROM admin.drone WHERE dstatus = 0";
            $query = $conn->query($sql);
            $result = $query->fetchAll(PDO::FETCH_CLASS);
            if(sizeof($result) > 0){
                $json = array("status"=>0, "result"=>$result);
            }else{
                $json = array("status"=>1);
            }
        }else{
            $json = array("status"=>2);
        }
    }
}catch(PDOException $e){
    //print($e->getMessage());
    $json = array("status"=>3);
}
print(json_encode($json, JSON_PRETTY_PRINT));

/*
Status code:
    0: Get Success
    1: Get Failed
    2: Permission denied
    3: PDO error
*/