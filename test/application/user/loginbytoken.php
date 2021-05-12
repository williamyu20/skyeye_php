<?php
/**
 * Created by PhpStorm.
 * User: Simon
 * Date: 3/18/2019
 * Time: 10:00 PM
 */

$required = True;
require_once "../constant.php";

$token = $_GET['token'];

try{
    $conn = new PDO($tns,$db_username,$db_password);
    if($conn){
        $sql = get_permission_sql($token);
        $query = $conn->query($sql);
        $result = $query->fetchAll();
        if(check_permission($result)){
            $userid = $result[0]["USERID"];
            $sql2 = "UPDATE admin.useraccount SET lastlogin = SYSTIMESTAMP
                    WHERE userid = '$userid'";
            $query2 = $conn->prepare($sql2);
            $query2->execute();
            if($query2->rowCount() > 0){
                $json = array("status"=>0, "role"=>$result[0]["ROLE"]);
            }else{
                $json = array("status"=>1);
            }
        }else{
            $json = array("status"=>1);
        }
    }
}catch(PDOException $e){
    //print($e->getMessage());
    $json = array("status"=>2);
}
print(json_encode($json, JSON_PRETTY_PRINT));

/*
Status code:
    0: Login Success
    1: Login failed
    2: PDO error
*/