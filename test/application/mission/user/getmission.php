<?php
/**
 * Created by PhpStorm.
 * User: Simon
 * Date: 3/28/2019
 * Time: 11:09 AM
 */

$required = True;
require_once "../../constant.php";

$token = $_GET['token'];

try{
    $conn = new PDO($tns,$db_username,$db_password);
    if($conn){
        $sql = get_permission_sql($token);
        $query = $conn->query($sql);
        $rows = $query->fetchAll();
        //print(json_encode($rows, JSON_PRETTY_PRINT));
        if(check_permission($rows)){
            $sql = "SELECT DISTINCT m.mid, m.mname, m.mdesc, m.mcreator, u2.uname as MCREATORNAME, m.mcreatetime,
                    m.MStartTime, m.MEndTime, m.MLocationName, m.CenterLat, m.CenterLng,
                    m.MRange, m.maltitude, m.MStatus, m.MVictimLat, m.MVictimLng,Patterns
                    FROM admin.mission m 
                    LEFT JOIN admin.usermission um on m.mid = um.mid
                    LEFT JOIN admin.useraccount u on um.userid = u.userid
                    LEFT JOIN admin.useraccount u2 on m.mcreator = u2.userid
		    WHERE mstatus in (0, 1, 2) AND u.utoken = '$token'
		    ORDER BY m.mid DESC";
            $query = $conn->query($sql);
            $result = $query->fetchAll();
            //print(json_encode($result, JSON_PRETTY_PRINT));
            if(sizeof($result) > 0){
                for ($i = 0; $i < sizeof($result); $i++){
                    $mid = $result[$i]["mid"];
                    //print($mid);
                    $sql2 = "SELECT u.userid, u.uname, um.mid, um.jid, um.starttime, um.endtime 
                         FROM admin.usermission um
                         LEFT JOIN admin.useraccount u ON u.userid = um.userid
			 WHERE um.mid = '$mid'
			 ORDER BY um.mid DESC";
                    $query2 = $conn->query($sql2);
                    $result2 = $query2->fetchAll(PDO::FETCH_CLASS);
                    //print(json_encode($result2, JSON_PRETTY_PRINT));
                    $result[$i]["members"] = $result2;
                    $sql3 = "SELECT d.droneid, d.dserialnumber
                         FROM admin.drone d
                         LEFT JOIN admin.dronemission dm ON d.droneid = dm.droneid
			 WHERE dm.mid = '$mid'
			 ORDER BY dm.mid DESC";
                    $query3 = $conn->query($sql3);
                    $result3 = $query3->fetchAll(PDO::FETCH_CLASS);
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
