<?php
/**
 * Created by PhpStorm.
 * User: Simon
 * Date: 3/24/2019
 * Time: 3:38 AM
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
        if(check_admin_permission($rows)){
            $sql2 = "select u.userid, u.uname, u.role, u.lastlogin, s.sid, s.sname, s.sgender, s.sage, p.pid, p.pname, d.did, d.dname
                     from admin.useraccount u
                     left join admin.staff s on u.sid = s.sid
                     left join admin.post p on s.pid = p.pid
                     left join admin.dept d on p.did = d.did
                     where u.status = 0";
            $query2 = $conn->query($sql2);
            $result = $query2->fetchAll(PDO::FETCH_CLASS);
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