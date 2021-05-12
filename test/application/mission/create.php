<?php
/**
 * Created by PhpStorm.
 * User: Simon
 * Date: 3/18/2019
 * Time: 10:12 PM
 */

$required = True;
require_once "../constant.php";

$token = $_GET['token'];
$m_name = $_GET['m_name'];
$m_desc = $_GET['m_desc'];
$m_loc_name = $_GET['loc_name'];
$m_loc_latlng = $_GET['latlng'];
$searching_radius = $_GET['searching_radius'];
$altitude = $_GET['altitude'];
settype($searching_radius, "double");
$lat = explode(",", $m_loc_latlng)[0];
settype($lat, "double");
$lng = explode(",", $m_loc_latlng)[1];
settype($lng, "double");
$pattern = $_GET['pattern'];
//print("$lat,$lng\n");

try{
    $conn = new PDO($tns,$db_username,$db_password);
    if($conn){
        $sql = get_permission_sql($token);
        $query = $conn->query($sql);
        $rows = $query->fetchAll();
        if(check_admin_permission($rows)){
            $userid = $rows[0]["userid"];
            $sql2 = "INSERT INTO admin.mission
                     (mname,mdesc,mcreator,mcreatetime,mlocationname,centerlat,centerlng,mrange, maltitude,mstatus,Patterns)
                     VALUES ('$m_name','$m_desc','$userid',CURRENT_TIMESTAMP(),'$m_loc_name',$lat,$lng,$searching_radius,$altitude,0,'$pattern')";
            $query2 = $conn->prepare($sql2);
            $query2->execute();
            if($query2->rowCount() > 0){
                $sql3 = "select mid from admin.mission order by mcreatetime desc";
                $query3 = $conn->query($sql3);
                $result = $query3->fetchAll();
                $json = array("status"=>0, "mid"=> $result[0]['mid']);
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
    0: Create Mission Success
    1: Mission Parameters Error
    2: Permission Denied
    3: PDO error

Pattern:
    0: Sector search
    1: Parallel Sweep Search
    2: Expanding Square Search
    3: Track Line Search
    4: Contour Search
    5: Co-ordinated Vessel-Aircraft Search
*/
