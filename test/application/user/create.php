<?php
/*
 * Created by PhpStorm.
 * User: Simon
 * Date: 1/15/2019
 * Time: 8:44 PM
 */

$required = True;
require_once "../constant.php";

$token = $_GET['token'];
$username = $_GET['username'];
$password = $_GET['password'];
$role = $_GET['role'];
$staffid = $_GET['staffid'];

try{
    $conn = new PDO($tns,$db_username,$db_password);
    if($conn){
        $sql = get_permission_sql($token);
        $query = $conn->query($sql);
        $rows = $query->fetchAll();
        if(check_admin_permission($rows)){
            $sql = "SELECT userid FROM admin.useraccount WHERE uname='$username'";
            $query = $conn->query($sql);
            $result = $query->fetchAll();
            if(sizeof($result) > 0){
                $json = array("status"=>1);
            }else{
                $sql = "INSERT INTO admin.useraccount (UserID,UNAME,PWD,Role,SID,status) 
                    VALUES(admin.seq_userid.nextval,'$username','$password','$role','$staffid',0)";
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
    1: User exist
    2: Permission denied
    3: PDO error
    4: Insert failed
*/