<?php
/**
 * Created by PhpStorm.
 * User: Wilson
 * Date: 2/25/2019
 * Time: 16:59
 */

abstract class missionPageType{
    const startMission = 0;
    const joinMission = 1;
    const ongoingMission = 2;
    const finishedMission = 3;
}

//change date format
function dateFormat($d){
    if(($d)==0){
        return '';
    }else{
        $date = DateTime::createFromFormat('d-M-y H.i.s A +P', $d);
        $date = $date->format('Y-m-d');
        return $date;
    };
}

/* Finish mission
function FinishMission(){
    $url = 'http://ec2-54-169-161-54.ap-southeast-1.compute.amazonaws.com/mission/finish.php?token=' . $_SESSION["userDetails"]["userToken"] . '&mid='.$_POST['finish'];
    $json = file_get_contents($url, true);
    $obj = json_decode($json);
    if($obj->status==0){
        echo '<script language="javascript">';
        echo 'alert("* Finish Mission Success")
                window.onbeforeunload();';
        echo '</script>';
    }else if($obj->status==1){
        echo '<script language="javascript">';
        echo 'alert("* Finish Mission Failed")';
        echo '</script>';
        $errorMsg = "* User Exist";
    }else if($obj->status==2){
        echo '<script language="javascript">';
        echo 'alert("* No Permission")';
        echo '</script>';
        $errorMsg = "* No Permission";
    }else if($obj->status==3){
        echo '<script language="javascript">';
        echo 'alert("* PDO Error")';
        echo '</script>';
    }    
}

if(isset($_POST['finish'])) {
        FinishMission();
}
*/
function generateMissionList($missionPageType, array $missionDetails){
    if($missionDetails['status']!=1){
        for($i = 0; $i<sizeof($missionDetails['missions']); $i++){
            //Echo out the part of list
            echo '<div class="mt-3 ml-1 mr-1 missionContainer rounded">';
            echo '<div class="p-3 missionContainer">';
    //        echo '<a data-toggle="collapse" href="#'. $mission["missionID"].'" role="button" aria-expanded="false"
    //           aria-controls="collapseExample">'; //Collapse animation

            echo '<span class="float-left missionListTextSmall">'. dateFormat($missionDetails['missions'][$i]['MCREATETIME']) .'</span>'; //Mission Date
            echo '<span class="float-right missionListTextSmall">'. $missionDetails['missions'][$i]['MCREATORNAME'] .'</span><br>'; //Mission Creator
            echo '<h1>'. $missionDetails['missions'][$i]["MNAME"] .'</h1>'; //Mission Name
    //        echo '</a>';
    //        echo '<div class="collapse" id="'. $mission["missionID"] .'">'; //Collapse animation target
            echo '<span class="missionListTextSmall pl-3">Description: '. $missionDetails['missions'][$i]['MDESC'] .'</span><br>'; //Mission Description
            echo '<span class="missionListTextSmall pl-3">Location: '. $missionDetails['missions'][$i]['MLOCATIONNAME'] .'</span><br>'; //Mission Location

            switch($missionPageType){
                case missionPageType::ongoingMission:{
                    echo '<span class="missionListTextSmall pl-3">Date of Start: '. dateFormat($missionDetails['missions'][$i]['MSTARTTIME']) .'</span><br>'; //Finish Mission 
                    //echo '<button name="finish" class="btn btn-primary float-left btn-SkyEyeBlue" value="'. $missionDetails['missions'][$i]['MID'] .'">Finish</button>';
                    
                    
                    echo '<br><form action="MissionProgress.php" method="post">';
                    echo '<input type="hidden" id="MID" name="MID" value="' . $missionDetails['missions'][$i]['MID'] . '">';
                    echo '<button name="viewProgress" type="submit" class="btn btn-warning float-right mr-3" value="'. $missionDetails['missions'][$i]['MID'] .'">View Progress</button>';
                    echo '</form>';
                    break;
                }
                case missionPageType::finishedMission:{
                    echo '<br><form action="MissionReport.php" method="post">';
                    echo '<button name="viewDetails" type="submit" class="btn btn-warning float-right mr-3" value="'. $missionDetails['missions'][$i]['MID'] .'">View Details</button>';
                    echo '</form>';
                    break;
                    break;
                }
            }

    //        echo '</div>';
            echo '</div></div>';
        }
    }
}

//TODO: Modify as get mission for different pages
function getMissionList($missionPageType){
    switch($missionPageType){
            case missionPageType::ongoingMission:{
                ini_set("allow_url_fopen", 1);
                $url = [];
                array_push($url,'http://localhost/ogweb/application/mission/get.php?token=' . $_SESSION["userDetails"]["userToken"] . '&status=2');
                array_push($url,'http://localhost/ogweb/application/mission/get.php?token=' . $_SESSION["userDetails"]["userToken"] . '&status=1');
                //$url= 'http://localhost/ogweb/application/mission/get.php?token=' . $_SESSION["userDetails"]["userToken"] . '&status=1';
                break;
            }
            case missionPageType::finishedMission:{
                ini_set("allow_url_fopen", 1);
                $url = [];
                array_push($url,'http://localhost/ogweb/application/mission/get.php?token=' . $_SESSION["userDetails"]["userToken"] . '&status=3');
            }
    }
    for($i = 0; $i<sizeof($url); $i++){
        $json = file_get_contents($url[$i], true);
        $obj = json_decode($json, true);

        generateMissionList($missionPageType, $obj);
    }
}

//echo box to confirm finish the mission 
