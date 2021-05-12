<?php
/**
 * Created by PhpStorm.
 * User: Simon
 * Date: 3/18/2019
 * Time: 11:35 PM
 */

$required = True;
require_once "../constant.php";

$token = $_GET['token'];
$status = $_GET['status'];
$mid = $_GET['mid'];

try{
    $conn = new PDO($tns,$db_username,$db_password);
    if($conn){
        $sql = get_permission_sql($token);
        $query = $conn->query($sql);
        $rows = $query->fetchAll();
        if(check_admin_permission($rows)){
            $sql2 = "SELECT m.mid, m.mname, m.mdesc, m.mcreator, u.uname as MCREATORNAME, m.mcreatetime,
                    m.MStartTime, m.MEndTime, m.MLocationName, m.CenterLat, m.CenterLng,
                    m.MRange, m.maltitude, m.MStatus, m.MVictimLat, m.MVictimLng, m.Patterns 
		    FROM admin.mission m
		    LEFT JOIN useraccount u on m.mcreator = u.userid";
            if(isset($_GET['status'])){
                $sql2 = $sql2." WHERE m.mstatus IN ($status) ORDER BY m.mid DESC";
            }
            if(isset($_GET['mid'])){
                $sql2 = $sql2." WHERE m.mid = '$mid' ORDER BY m.mid DESC";
            }
            $query2 = $conn->query($sql2);
            $result = $query2->fetchAll();
            if(sizeof($result) > 0){
                for ($i = 0; $i < sizeof($result); $i++){
                    $mid = $result[$i]["mid"];
                    //print($mid);
                    $sql3 = "SELECT u.userid, u.uname, um.mid, um.jid, um.starttime, um.endtime 
                         FROM admin.usermission um
                         LEFT JOIN admin.useraccount u ON u.userid = um.userid
			 WHERE um.mid = '$mid'
			 ORDER BY um.mid DESC";
                    $query3 = $conn->query($sql3);
                    $result2 = $query3->fetchAll(PDO::FETCH_CLASS);
                    //print(json_encode($result2, JSON_PRETTY_PRINT));
                    $result[$i]["members"] = $result2;
                    $sql4 = "SELECT d.droneid, d.dserialnumber
                         FROM admin.drone d
                         LEFT JOIN admin.dronemission dm ON d.droneid = dm.droneid
			 WHERE dm.mid = '$mid'
			 ORDER BY dm.mid DESC";
                    $query4 = $conn->query($sql4);
                    $result3 = $query4->fetchAll(PDO::FETCH_CLASS);
                    $result[$i]["drones"] = $result3;
                    //print(json_encode($result3, JSON_PRETTY_PRINT));
                    for ($j = 0; $j < sizeof($result[$i]); $j++){
                        unset($result[$i][$j]);
                    }
                }
                $json = array("status"=>0, "missions"=>$result);
            }else{
                $json = array("status"=>1);
            }
        }else {
            $json = array("status"=>2);
        }
    }
}catch(PDOException $e){
    $json = array("status"=>3);
}
print(json_encode($json, JSON_PRETTY_PRINT));

/*
Status code:
    0: Get Mission Success
    1: Get Mission Failed
    2: Permission denied
    3: PDO error

Mission Status Code
    0: Created
    1: Started
    2: Victim Found
    3. Finish
    4: Deleted
*/
