<html>
    <head>
        <title>Mission Progress</title>
        <meta name="viewport" content="initial-scale=1.0">
        <meta charset="utf-8">
        <style>
        /* Always set the map height explicitly to define the size of the div
         * element that contains the map. */
        #map {
            height: 100%;
        }
        /* Optional: Makes the sample page fill the window. */
        html, body {
            height: 100%;
            margin: 0;
            padding: 0;
        }
        </style>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
        <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCmcafkULzCZLty4KR3wtYmcHicZKPLC7A&callback=initMap" async defer></script>
        <script>
            var map;
            var markets = [];

            var token = "5313c250d9e4dee21e5954b137304a55";
            var mid = "72";

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
                            updatemarkets();
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
                $.getJSON('waypoint/get.php', {"token": token, "mid": mid}
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
                        title:"Drone "+(i+1)
                    });
                    markets.push(marker);
                }
            }

            function updatemarkets(){
                window.setInterval(function(){
                    $.getJSON('mission/drone/get.php', {"token": token, "mid": mid}
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
        <div id="map"></div>
    </body>
</html>
