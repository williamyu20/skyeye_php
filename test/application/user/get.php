<?php
/**
 * Created by PhpStorm.
 * User: Simon
 * Date: 3/28/2019
 * Time: 10:56 AM
 */

$required = True;
require_once "../constant.php";

$token = $_GET['token'];

try{
    $conn = new PDO($tns,$db_username,$db_password);
    if($conn){
        $sql = "select u.userid, u.uname, u.role, u.lastlogin, s.sid, s.sname, s.sgender, s.sage, p.pid, p.pname, d.did, d.dname
                     from admin.useraccount u
                     left join admin.staff s on u.sid = s.sid
                     left join admin.post p on s.pid = p.pid
                     left join admin.dept d on p.did = d.did
		     where u.utoken = '$token' AND status = 0
		     ORDER BY u.userid DESC";
                     
        /*$sql = "select u.userid, u.uname, u.role, u.lastlogin, s.sid, s.sname, s.sgender, s.sage, p.pid, p.pname, d.did, d.dname
                    from admin.useraccount u
                    left join admin.staff s on u.sid = s.sid
                    left join admin.post p on s.pid = p.pid
                    left join admin.dept d on p.did = d.did";*/
        $query = $conn->query($sql);
        $result = $query->fetchAll(PDO::FETCH_CLASS);
        //echo($result);
        if(sizeof($result) > 0){
            $json = array("status"=>0, "result"=>$result);
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
    0: Get Success
    1: Get Failed
    3: PDO error
*/
