<?php
/*
 * Created by PhpStorm.
 * User: Simon
 * Date: 30/12/2018
 * Time: 11:29 AM
 */

$required = True;
require_once "../constant.php";

$login_name = $_GET['username'];
$login_pwd = $_GET['password'];

try{
    $conn = new PDO($tns,$db_username,$db_password);
    if($conn){
        //print("Connection Success.");
        $sql = "SELECT userid,role,status FROM admin.useraccount WHERE uname='$login_name' AND pwd='$login_pwd'";
        $query = $conn->query($sql);
        $result = $query->fetchAll();
        if(check_permission($result)){
            $rndToken = "";
            while(True){
                for($i = 0; $i < 32; $i++){
                    $rndToken .= dechex(rand(0,15));
                }
                $sql2 = "SELECT * FROM admin.useraccount WHERE UToken = '$rndToken'";
                $query2 = $conn->query($sql2);
                $result2 = $query2->fetchAll();
                if(sizeof($result2) == 0){
                    break;
                }
            }
            $userid = $result[0]["userid"];
            $sql3 = "UPDATE admin.useraccount SET UToken = '$rndToken',lastlogin = CURRENT_TIMESTAMP()
                         WHERE userid = '$userid'";
            $query3 = $conn->prepare($sql3);
            $query3->execute();
            if($query3->rowCount() > 0){
                $json = array("status"=>0,"token"=>$rndToken, "role"=>$result[0]["role"]);
            }else{
                $json = array("status"=>1);
            }
        }else{
            //print("Login Failed.");
            $json = array("status"=>1);
        }
    }
}catch(PDOException $e){
    print($e->getMessage());
    $json = array("status"=>2);
}
print(json_encode($json, JSON_PRETTY_PRINT));

/*
Status code:
    0: Login Success
    1: Login failed
    2: PDO error
*/
