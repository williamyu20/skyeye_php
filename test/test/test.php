<?php 


function dateFormat($d){
    if(($d)==0){
        return '';
    }else{
        $date = DateTime::createFromFormat('d-M-y H.i.s A +P', $d);
        $date = $date->format('Y-m-d');
        return $date;
    };
}

  $url = "http://ec2-18-136-209-223.ap-southeast-1.compute.amazonaws.com/mission/get.php/?token=7b9a9c0df4d69547ac60bf068cc63c45&status=3"; //. "&username=" . $_SESSION["userDetails"]["username"];
  //$url = 'http://' . $_SERVER['HTTP_HOST'] . "/testLogin.php"; //For relative path usage
  //$url = "https://dsafyp1819skyeye.azurewebsites.net/testLogin.php";
  $json = file_get_contents($url, true);
  $obj = json_decode($json,true);
  $MissionDetail = $obj['missions'][1];
  #print_r($MissionDetail);

  //Set Mission Details Var
  $Mission_Name = $MissionDetail['MNAME'];
  $Mission_ID = $MissionDetail['MID'];
  $Mission_Desc = $MissionDetail['MDESC'];
  $Mission_Creator = $MissionDetail['MCREATORNAME'];
  $Mission_CreatrTime = dateFormat($MissionDetail['MCREATETIME']);
  $Mission_StartTime = dateFormat($MissionDetail['MSTARTTIME']);
  $Mission_EndTime = dateFormat($MissionDetail['MENDTIME']);

  //Set Mission Location Var
  $Location_Name = $MissionDetail['MLOCATIONNAME'];
  $CenterLat = $MissionDetail['CENTERLAT'];
  $CenterLng = $MissionDetail['CENTERLNG'];
  $Search_Radius = $MissionDetail['MRANGE'];
  $Flight_Altitude = $MissionDetail['MALTITUDE'];
  $Victim_Found = "搵你唔到";
  $Victim_Lat = $MissionDetail['MVICTIMLAT'];
  $Victim_Lng = $MissionDetail['MVICTIMLNG'];

  class member
{
  public $m_name;
  public $m_id;
  public $drone_id;

}

$menber_list = array();
//print_r($MissionDetail['members'][0]);


foreach($MissionDetail['members'] as $member){
    echo '<br>';
    print_r($member);
    
};