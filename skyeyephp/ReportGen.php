<?php
// Get Mission info

function dateFormat($d){
  if(($d)==0){
      return '';
  }else{
      $date = DateTime::createFromFormat('d-M-y H.i.s A +P', $d);
      $date = $date->format("F j, Y, G:i:s");
      return $date;
  };
}

function ReportDateFormat($d){
  if(($d)==0){
      return '';
  }else{
      $date = DateTime::createFromFormat('d-M-y H.i.s A +P', $d);
      $date = $date->format("F j, Y");
      return $date;
  };
}

if($_SERVER["REQUEST_METHOD"] == "POST"){
    $Mission_ID = $_POST['viewDetails'];
}

// for Mission Detail
$url = "http://ec2-54-179-174-219.ap-southeast-1.compute.amazonaws.com/application/mission/get.php/?token=" .$_SESSION["userDetails"]["userToken"] .'&status=3';
$json = file_get_contents($url, true);
$obj = json_decode($json,true);

$MissionDetail;
foreach($obj['missions'] as $mission){
    if ($mission['MID'] ==$Mission_ID){
        $MissionDetail = $mission;
    };
}
//print_r($MissionDetail);

//Set Mission Details Var
$Mission_Name = $MissionDetail['MNAME'];
$Mission_Desc = $MissionDetail['MDESC'];
$Mission_Creator = $MissionDetail['MCREATORNAME'];
$Mission_CreatrTime = dateFormat($MissionDetail['MCREATETIME']);
$Mission_StartTime = dateFormat($MissionDetail['MSTARTTIME']);
$Mission_EndTime = dateFormat($MissionDetail['MENDTIME']);
$Report_genDate =  ReportDateFormat($MissionDetail['MENDTIME']);

//Set Mission Location Var
$Location_Name = $MissionDetail['MLOCATIONNAME'];
$CenterLat = $MissionDetail['CENTERLAT'];
$CenterLng = $MissionDetail['CENTERLNG'];
$Search_Radius = $MissionDetail['MRANGE'];
$Flight_Altitude = $MissionDetail['MALTITUDE'];
$Victim_Lat = $MissionDetail['MVICTIMLAT'];
$Victim_Lng = $MissionDetail['MVICTIMLNG'];

$droneID_list = array();

if(strlen($Victim_Lat) < 2){
    $Victim_Found = "No";
    $Victim_Lng = 'NULL';
    $Victim_Lat = 'NULL';
} else {
    $Victim_Found = "Yes";
}

// Mission Detail
echo '
<br>
<div class="container"  style="color:black">
  <div class="card">
<div class="card-header">
<strong>Mission Report</strong>
<span class="float-right" style="margin-left:3%"> <strong> Date: </strong>' .$Report_genDate. '</span>
<span class="float-right"> <strong>Status:</strong> Completed </span>

  

</div>
<div class="card-body">
<div class="row">
<div class="col-xs-12 col-md-6 col-lg-6 float-xs-left" text-align:center>
    <div class="card  height">
        <div class="card-header"><h5 class="display-5">Mission Details<h5></div>
        <div class="card-block" style="text-align:left">
        <ul class="list-group list-group-flush"  style="color:black">
        <li class="list-group-item"><b>Name: </b>'.$Mission_Name.' <span class="float-right"><b>ID: </b>'.$Mission_ID.'</span></li>
        <li class="list-group-item"><b>Description: </b>'.$Mission_Desc.'</li>
        <li class="list-group-item"><b>Creator Name: </b>'.$Mission_Creator.'</li>
        <li class="list-group-item"><b>Create time: </b>'.$Mission_CreatrTime.'</li>
        <li class="list-group-item"><b>Start With: </b>'.$Mission_StartTime.'<br> <b>End With: </b>'.$Mission_EndTime.'</li>
      </ul>
        </div>
    </div>
</div>
<br>
<div class="col-xs-12 col-md-6 col-lg-6">
    <div class="card  height">
        <div class="card-header"><h5 class="display-5">Mission Location<h5></div>
        <div class="card-block" style="text-align:left">
        <ul class="list-group list-group-flush"  style="color:black">
        <li class="list-group-item"><b>Location Name:</b> '.$Location_Name.'</li>
        <li class="list-group-item"><b>Center Lat:</b> '.$CenterLat.' <br><b>Center Lng:</b> '.$CenterLng.'</li>
        <li class="list-group-item"><b>Search Radius(M):</b> '.$Search_Radius.' <br><b>Flight Altitude(M): </b>'.$Flight_Altitude.'<br><b>Victim Found:</b> '.$Victim_Found.'</li>';
        if($Victim_Found == "Yes"){
          echo '<li class="list-group-item"><b>Victim Lat:</b> '.$Victim_Lat.' <br><b>Victim Lng: </b>'.$Victim_Lng.'</li>';
      }


echo'  
      </ul>
      </div>
    </div>
</div>
<br>
<div class="table-responsive" style="margin-top:5%">
                        <table class="table  table-bordered table-hover">
                            <thead>
                                <tr class="table-active">
                                  <h2><th scope="col">#</th></h2>
                                  <h2><th scope="col">Member Name</th></h2>
                                  <h2><th scope="col">Member ID</th></h2>
                                  <h2><th scope="col">Drone ID</th></h2>
                                </tr>
                            </thead>
                            <tbody>';
                            
                            for($i=0; $i<sizeof($MissionDetail['members']);$i++) {
                              $mName = $MissionDetail['members'][$i]['UNAME'];
                              $mId = $MissionDetail['members'][$i]['USERID'];
                              $droneId = $MissionDetail['drones'][$i]['DRONEID'];
                              array_push($droneID_list,$droneId);
                              
                              echo '
                                <tr class="table">
                                  <td>'.((string)$i+1).'</td>
                                  <td>'.$mName.'</td>
                                  <td>'.$mId.'</td>
                                  <td>'.$droneId.'</td>
                                </tr> 
                              ';
                            }  
echo'</tbody>
                        </table>

</div>

</div>

</div>
</div>
    <div class="card" style="margin-top:2%">
      <div class="card-header">
        <strong>Flight Path</strong>
        </div>
        <div class="card-body">
        <div id="map" class="map-responsive">
        
        </div>
  
    </div>
</div>
';

// For drone filght route
$waypoint_url = "http://ec2-54-179-174-219.ap-southeast-1.compute.amazonaws.com/application/waypoint/get.php/?token=" .$_SESSION["userDetails"]["userToken"] .'&mid='.$Mission_ID;
$waypoint_json = file_get_contents($waypoint_url, true);
$waypoint_obj = json_decode($waypoint_json,true);

// set var
$all_drone =  $waypoint_obj["result"];
$drone_list = array();
for($i=0; $i<sizeof($all_drone);$i++) {
  $drone_num = print_r("Drone ".($i+1),true);
  array_push($drone_list,$all_drone[$i][$drone_num]);
  //debugToConsole($all_drone[$i][$drone_num]);
  //debugToConsole($drone_num);

}

$start_lat = floatval($drone_list[0][0]["Latitude"]);
$start_lng = floatval($drone_list[0][0]["Longitude"]);
debugToConsole($start_lat);

function debugToConsole($msg) { 
  echo "<script>console.log(".json_encode($msg).")</script>";
}

echo '<script>var drone_list = '.json_encode($drone_list).' </script>';
echo '<script>var droneID_list = '.json_encode($droneID_list).' </script>';

echo'
<script>
// In the following example, markers appear when the user clicks on the map.
var VictimInfo;

function initialize() {';
  

  if($Victim_Lat=="NULL"){
    echo ' var bangalore = { lat: '.$start_lat.', lng: '.$start_lng.' };';
  } else {
    echo 'var bangalore = { lat: '.$Victim_Lat.', lng: '.$Victim_Lng.' };
    VictimInfo = new google.maps.InfoWindow({
      content: "<h4>Vicitim was found here!</h4>"+
                "<b>Lat: </b> " + "'.$Victim_Lat.'" +
                "<br><b>Lng: </b>" + "'.$Victim_Lng.'"
    });';
  };
  echo
  '
  var map = new google.maps.Map(document.getElementById("map"), {
    zoom: 18,
    center: bangalore
  });

  // Add a marker at the center of the map.
  addMarker(bangalore, map);
}

// Adds a marker to the map.
function addMarker(location, map) {
  // Add the marker at the clicked location, and add the next-available label
  // from the array of alphabetical characters.
  var marker = new google.maps.Marker({
    position: location,
    //label: labels,
    map: map,
    icon:{
      url:"image/Victim.png",
      scaledSize: new google.maps.Size(30,30)
    }
  });

    marker.addListener("mouseover", function() {
      VictimInfo.open(map, marker);
    });';
    if($Victim_Lat!='NULL'){
      echo'google.maps.event.trigger(marker,"mouseover");
      marker.addListener("click", function() {
        VictimInfo.open(map, marker);
      });';
    };
  
echo'
  marker.addListener("mouseout", function() {
    VictimInfo.close();
  });

  // add line
  line_color = ["#071418","#00BFFF","#FF0000","#FFFF00","#01DF01"];

  drone_list.forEach(function(drone, index) {
    
    //console.log(drone);
    var waypoint = [];
    drone.forEach(replace);
    console.log(waypoint)
    var flightPlanCoordinates = waypoint;
    var flightPath = new google.maps.Polyline({
      path: flightPlanCoordinates,
      geodesic: true,
      strokeColor: line_color[index],
      strokeOpacity: 1.0,
      strokeWeight: 2
    });
    function replace(item,index){
      waypoint.push({lat:parseFloat(item["Latitude"]),lng:parseFloat(item["Longitude"])})
  };


  var start_contentString = "<h5>Start Point</h5>"+
  "<b>Lat: </b>"+ (waypoint[0].lat+"").substr(0,10)+
  "<br><b>Lng: </b>"+ (waypoint[0].lng+"").substr(0,10);

  var Start_info = new google.maps.InfoWindow({
    content: start_contentString
  });

  var end_contentString = "<h5>End Point</h5><b>Drone ID: </b>"+ droneID_list[index]+
  "<br><b>Lat: </b>"+ (waypoint[waypoint.length-1].lat+"").substr(0,10)+
  "<br><b>Lng: </b>"+ (waypoint[waypoint.length-1].lng+"").substr(0,10);

  var End_info = new google.maps.InfoWindow({
    content: end_contentString
  });


    flightPath.setMap(map);

    var icon_path = "image/Drone"+index+".png"+"";
    // start point
    addPoint(waypoint[0], map,"image/startpoint.png");
    // end point
    addPoint(waypoint[waypoint.length-1], map,icon_path);

    function addPoint(location, map, path) {
       
      // Add the marker at the clicked location, and add the next-available label
      // from the array of alphabetical characters.
      var marker = new google.maps.Marker({
        position: location,
        //label:status,
        map: map,
        icon:{
          url:path,
          scaledSize: new google.maps.Size(30,30)
        }
      });

      var str = path.split("/")[1];
      var n = str.startsWith("Drone");
      if(n===true){

        marker.addListener("click", function() {
          End_info.open(map, marker);
        });
        marker.addListener("mouseover", function() {
          End_info.open(map, marker);
        });
        marker.addListener("mouseout", function() {
          End_info.close();
        });
      } else {
        marker.addListener("click", function() {
          Start_info.open(map, marker);
        });
        marker.addListener("mouseover", function() {
          Start_info.open(map, marker);
        });
        marker.addListener("mouseout", function() {
          Start_info.close();
        });
      }
    }
  });


}

google.maps.event.addDomListener(window, "load", initialize);
</script>
';

?>
