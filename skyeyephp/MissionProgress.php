<html>
    <head>
        <meta name="viewport" content="initial-scale=1.0">
        <meta charset="utf-8">
        <?php include "header.php" ?>
        <title>Mission Progress - SkyEye</title>
        <style>
        /* Always set the map height explicitly to define the size of the div
         * element that contains the map. */
        #map {
            height: 100%;
        }
        /* Optional: Makes the sample page fill the window. */
        </style>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
        <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAXYPXriGjkZIuRcm7A6AqV9TWY7ro0uE4&callback=initMap" async defer></script>
        <script>
            var map;
            var markets = [];
            <?php
                include "checkIsLogined.php";
                //var token = "5313c250d9e4dee21e5954b137304a55";
                //var mid = "72";
                if($_SERVER["REQUEST_METHOD"] == "POST"){
                    echo 'var token = "' . $_SESSION["userDetails"]["userToken"] . '";';
                    echo 'var mid = "' . $_POST["MID"] . '";';
                    //echo 'var mid = "72"';
                }
                
            ?>


            function init() {
                $.getJSON('getmission.php', {"token": token, "mid": mid}
                    , function(data) {
                        if(data["status"] == 0) {
                            result_json = data["missions"][0];
                            var lat = parseFloat(result_json["CENTERLAT"]);
                            var lng = parseFloat(result_json["CENTERLNG"]);
                            var radius = parseFloat(result_json["MRANGE"]);
                            moveToLocation(lat, lng, radius);
                            drawMissionCircle(lat, lng, radius);
                            drawFlightPath();
                            addMarkets(result_json["drones"].length);
                            updatemarkers();
                            addvictim();
                        }else{
                            console.log("Get mission failed.");
                        }
                    });

            }

            function initMap(){
                map = new google.maps.Map(document.getElementById('map'), {
                    center: {lat: 0, lng: 0},
                    zoom: 2
                });
            }

            function moveToLocation(lat, lng, radius){
                var zoom = Math.trunc(16 - Math.log(radius / 500) / Math.log(2));
                var center = new google.maps.LatLng(lat, lng);
                map.panTo(center);
                map.setZoom(zoom);
            }
            
            function drawMissionCircle(lat, lng, radius) {
                var center = new google.maps.LatLng(lat, lng);
                var missionCircle = new google.maps.Circle({
                    strokeColor: '#000fff',
                    strokeOpacity: 0.5,
                    strokeWeight: 2,
                    fillColor: '#39e7ff',
                    fillOpacity: 0.25,
                    map: map,
                    center: center,
                    radius: radius * 2
                });
            }

            function drawFlightPath(){
                $.getJSON('getwaypoint.php', {"token": token, "mid": mid}
                    , function(data) {
                        if(data["status"] == 0) {
                            result_json = data["result"];
                            for(var i = 0; i < result_json.length; i++){
                                dronePath = result_json[i]["Drone "+(i+1)];
                                flightPlanCoordinates = [];
                                for(var j = 1; j < dronePath.length; j++){
                                    var lat = dronePath[j]["Latitude"];
                                    var lng = dronePath[j]["Longitude"];
                                    flightPlanCoordinates.push({lat: lat, lng: lng});
                                }
                                var flightPath = new google.maps.Polyline({
                                    path: flightPlanCoordinates,
                                    geodesic: true,
                                    strokeColor: getRandomColor(),
                                    strokeOpacity: 1.0,
                                    strokeWeight: 2
                                });
                                flightPath.setMap(map);
                            }
                        }else{
                            console.log("Get waypoints failed.");
                        }
                    });
            }

            function addMarkets(num){
                for(var i = 0; i < num; i++){
                    var marker = new google.maps.Marker({
                        position: {lat: 0, lng: 0},
                        title:"Drone "+(i+1),
                        icon:{
                            url:"image/Drone"+(i+1)+".png",
                            scaledSize: new google.maps.Size(30,30)
                        }
                    });
                    markets.push(marker);
                }
            }

            function updatemarkers(){
                window.setInterval(function(){
                    $.getJSON('droneLocation.php', {"token": token, "mid": mid}
                        , function(data) {
                            if(data["status"] == 0) {
                                result_json = data["result"];
                                for(var i = 0; i < result_json.length; i++){
                                    markets[i].setMap(map);
                                    var lat = parseFloat(result_json[i]["LAT"]);
                                    var lng = parseFloat(result_json[i]["LNG"]);
                                    var newLatLng = new google.maps.LatLng(lat, lng);
                                    markets[i].setPosition(newLatLng);
                                }
                            }else{
                                console.log("Get drones status failed.");
                            }
                        });
                    $.getJSON('getmission.php', {"token": token, "mid": mid}
                                , function(data) {
                                    if(data["status"] == 0) {
                                        result_json = data["missions"][0];
                                        console.log(result_json);
                                        if(result_json["MSTATUS"] == "2"){
                                            var lat = parseFloat(result_json["MVICTIMLAT"]);
                                            var lng = parseFloat(result_json["MVICTIMLNG"]);
                                            var victim_marker = new google.maps.Marker({
                                                position: {lat: lat, lng: lng},
                                                title:"Victim",
                                                icon:{
                                                url:"image/Victim.png",
                                                scaledSize: new google.maps.Size(50,50)
                                                }
                                            });
                                            victim_marker.setMap(map);
                                        }
                                    }else{
                                        console.log("Get mission failed.");
                                    }
                                });
                }, 1000);
            }
            


            function getRandomColor() {
                var letters = '0123456789ABCDEF';
                var color = '#';
                for (var i = 0; i < 6; i++) {
                    color += letters[Math.floor(Math.random() * 16)];
                }
                return color;
            }
            init();
            



        </script>
    </head>
    <body>
        <?php 
            //include "Missionpagemenu.php";
            /**
             * Created by PhpStorm.
             * User: Wilson
             * Date: 2/25/2019
             * Time: 00:56
             */

            /**
             * TODO: Modify the following code to fulfil the requirements & permission of different members,
             * for example:
             * crew of the team do not need the page of ongoingMission
            **/

            //echo header bar
            echo '
            <nav class="navbar navbar-dark pageHeaderBar sticky-top navbar-expand-lg">

            <a class="navbar-brand" href="#" data-toggle="modal" data-target="#personalInformationModal">
            <img src="image/skyeye_logo.png" width="50%" height="6.3%" alt="">
            </a> 
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
              <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a href="OngoingMission.php" class="nav-link">Active Mission</a>
                    </li>
                    <li class="nav-item">
                        <a href="FinishedMission.php" class="nav-link">Completed Mission</a>
                    </li>
                </ul>
            </div>

            </nav>
            ';

            //get personal information
            $url = "http://localhost/ogweb/application/user/get.php/?token=" . $_SESSION["userDetails"]["userToken"]; 
                    //$url = 'http://' . $_SERVER['HTTP_HOST'] . "/testLogin.php"; //For relative path usage
                    //$url = "https://dsafyp1819skyeye.azurewebsites.net/testLogin.php";
                    $json = file_get_contents($url, true);
                    $obj = json_decode($json, true);
                    $_SESSION["SID"] = $obj['result'][0]['SID'];
            //echo personal information and logout btn
            echo '
            <!-- Modal -->
            <div class="modal fade" id="personalInformationModal" tabindex="-1" role="dialog" aria-labelledby="personalInfoTitle" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title blackText" id="personalInfoTitle">Personal Information</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body blackText">
                            <b>Member ID:</b> '. $obj['result'][0]['USERID'].'<br>
                            <b>Name:</b> '. $obj['result'][0]['UNAME'] .'<br>
                            <b>Role:</b> '. $_SESSION["userDetails"]["mode"] .'<br>

                        </div>
                        <div class="modal-footer">
                                <button onclick="changeUserPage()" type="button" class="btn btn-secondary" data-dismiss="modal">User Management</button>
                                <button onclick="changeDronePage()" type="button" class="btn btn-secondary" data-dismiss="modal">Drone Management</button>
                            <form action="checkIsLogined.php" method="post">
                                <button type="submit" name="logout_btn" value="logout" class="btn btn-outline-danger">Logout</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <script>
                function changeUserPage() {
                  location.replace("CreateUser.php")
                }
                function changeDronePage() {
                  location.replace("CreateDrone.php")
                }
            </script>
            ';
        ?>
        <div id="map"></div>
    </body>
</html>
