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
$dserialnumber =  $_GET['dsn'];

try{
    $conn = new PDO($tns,$db_username,$db_password);
    if($conn){
        $sql = get_permission_sql($token);
        $query = $conn->query($sql);
        $rows = $query->fetchAll();
        if(check_admin_permission($rows)){
            $sql = "SELECT droneid FROM admin.drone WHERE droneid='$droneid'";
            $query = $conn->query($sql);
            if(sizeof($query->fetchAll()) > 0){
                $result = array("status"=>1);
            }else{
                $sql = "INSERT INTO admin.drone VALUES(admin.seq_droneid.nextval,'$dserialnumber', 0)";
                $query = $conn->prepare($sql);
                $query->execute();
                if($query->rowCount() > 0){
                    $json = array("status"=>0);
                }else{
                    $json = array("status"=>4);
                }
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
    0: Insert Success
    1: Drone exist
    2: Permission denied
    3: PDO error
    4: Insert failed
*/