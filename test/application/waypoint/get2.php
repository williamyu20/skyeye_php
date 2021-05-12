<?php

$required = True;
require_once "../constant.php";

$token = $_GET['token'];
$mid = $_GET['mid'];
$pattern = $_GET['pattern'];
try{
    $conn = new PDO($tns,$db_username,$db_password);
    if($conn){
        $sql = get_permission_sql($token);
        $query = $conn->query($sql);
        $rows = $query->fetchAll();
        if(check_permission($rows)){
            $sql = "SELECT mid, MLocationName, CenterLat, CenterLng, MRange, MAltitude
                    FROM admin.mission
                    WHERE mid = '$mid'";
            $query = $conn->query($sql);
            $result = $query->fetchAll();
            $mid = $result[0]["mid"];
            $lat = $result[0]["CenterLat"];
            $lng = $result[0]["CenterLng"];
            $radius = $result[0]["MRange"];
            $altitude = $result[0]['MAltitude'];
            $sql2 = "SELECT d.droneid, d.dserialnumber
                         FROM admin.drone d
                         LEFT JOIN admin.dronemission dm ON d.droneid = dm.droneid
                         WHERE dm.mid = '$mid'";
            $query2 = $conn->query($sql2);
            $result2 = $query2->fetchAll(PDO::FETCH_CLASS);
            $drone = sizeof($result2);
	    //          $result = getwaypoin($drone, $lat, $lng, $radius,$altitude);
	  if($pattern == '0'){
		  $result = waypoint0($lng, $lat, $radius);
	  }else if($pattern == '1'){
		  $result = getwaypoin($drone, $lat, $lng, $radius);
		};
            if(sizeof($result) > 0){
		    $json = array("status"=>0, "result"=>$result);
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
    0: Get Route Success
    1: Get Route Failed
    2: Permission denied
    3: PDO error
*/


//End of user input
class LonLatCalculator{
    var $longitude;
    var $latitude;
    function computerThatLonLat($lon, $lat, $brng, $dist){
        $a = 6378137;
        $b = 6356752.3142;
        $f = 1 / 298.2572236;
        $alpha1 = deg2rad ($brng);
        $sinAlpha1 = sin($alpha1);
        $cosAlpha1 = cos($alpha1);
        $tanU1 = (1 - $f) * tan(deg2rad($lat));
        $cosU1 = 1 / sqrt((1 + $tanU1 * $tanU1));
        $sinU1 = $tanU1 * $cosU1;
        $sigma1 = atan2($tanU1, $cosAlpha1);
        $sinAlpha = $cosU1 * $sinAlpha1;
        $cosSqAlpha = 1 - $sinAlpha * $sinAlpha;
        $uSq = $cosSqAlpha * ($a * $a - $b * $b) / ($b * $b);
        $A = 1 + $uSq / 16384 * (4096 + $uSq * (-768 + $uSq * (320 - 175 * $uSq)));
        $B = $uSq / 1024 * (256 + $uSq * (-128 + $uSq * (74 - 47 * $uSq)));
        $cos2SigmaM=0;
        $sinSigma=0;
        $cosSigma=0;
        $sigma = $dist / ($B * $A);
        $sigmaP = 2 * PI();
        while (abs($sigma - $sigmaP) > 1e-12) {
            $cos2SigmaM = cos(2 * $sigma1 + $sigma);
            $sinSigma = sin($sigma);
            $cosSigma = cos($sigma);
            $deltaSigma = $B * $sinSigma * ($cos2SigmaM + $B / 4 * ($cosSigma * (-1 + 2 * $cos2SigmaM * $cos2SigmaM) - $B / 6 * $cos2SigmaM * (-3 + 4 * $sinSigma * $sinSigma) * (-3 + 4 * $cos2SigmaM * $cos2SigmaM)));
            $sigmaP = $sigma;
            $sigma = $dist / ($b * $A) + $deltaSigma;
        }
        $tmp = $sinU1 * $sinSigma - $cosU1 * $cosSigma * $cosAlpha1;
        $lat2 = atan2($sinU1 * $cosSigma + $cosU1 * $sinSigma * $cosAlpha1, (1 - $f) * sqrt($sinAlpha * $sinAlpha + $tmp * $tmp));
        $lambda = atan2($sinSigma * $sinAlpha1, $cosU1 * $cosSigma - $sinU1 * $sinSigma * $cosAlpha1);
        $C = $f / 16 * $cosSqAlpha * (4 + $f * (4 - 3 * $cosSqAlpha));
        $L = $lambda - (1 - $C) * $f * $sinAlpha * ($sigma + $C * $sinSigma * ($cos2SigmaM + $C * $cosSigma * (-1 + 2 * $cos2SigmaM * $cos2SigmaM)));

        $revAz = atan2($sinAlpha, -$tmp);
        $this->longitude= $lon+rad2deg($L);
        $this->latitude= rad2deg($lat2);

    }

    public function getLongitude(){
        return $this->longitude;
    }

    public function getLatitude(){
        return $this->latitude;
    }

}

/*function waypoint1($lon, $lat, $height, $weight, $dist, $brng, $t_weight){
    $test = new LonLatCalculator;
    $loc_array = [];
    array_push($loc_array,Array("Latitude" => $lat, "Longitude" => $lon));
    $wide =0;
    $Pa = 0;
    $newlon = 0;
    $newlat = 0;
    $test->computerThatLonLat($lon, $lat, $brng, $dist);
    $newlon = $test->getLongitude();
    $newlat = $test->getLatitude();
    array_push($loc_array,Array("Latitude" => $newlat, "Longitude" => $newlon));

    $brng = 0;
    $test->computerThatLonLat($newlon, $newlat, $brng, $height);
    $newlon = $test->getLongitude();
    $newlat = $test->getLatitude();
    array_push($loc_array,Array("Latitude" => $newlat, "Longitude" => $newlon));

    while($wide<$t_weight){
        if($brng==0){
            $Pa = 90;
        }else if($brng == 180){
            $Pa = -90;
        }
        $brng = $brng + $Pa;
        $test->computerThatLonLat($newlon, $newlat, $brng, $weight);
        $newlon = $test->getLongitude();
        $newlat = $test->getLatitude();
        array_push($loc_array,Array("Latitude" => $newlat, "Longitude" => $newlon));

        if($brng==0){
            $Pa = 90;
        }else if($brng == 180){
            $Pa = -90;
        }
        $brng = $brng + $Pa;
        $test->computerThatLonLat($newlon, $newlat, $brng, $height);
        $newlon = $test->getLongitude();
        $newlat = $test->getLatitude();
        array_push($loc_array,Array("Latitude" => $newlat, "Longitude" => $newlon));
        $wide = $wide + $weight;
    }
    return $loc_array;
}*/

function waypoint0($lon, $lat, $dist){
    $test = new LonLatCalculator;
    $loc_array = [];
    $brng = 0;
    array_push($loc_array,Array("Latitude" => $lat, "Longitude" => $lon));
    $newlon = 0;
    $newlat = 0;

    $test->computerThatLonLat($lon, $lat, $brng, $dist);
    $newlon = $test->getLongitude();
    $newlat = $test->getLatitude();
    array_push($loc_array,Array("Latitude" => $newlat, "Longitude" => $newlon));

    $brng = 120;
    $test->computerThatLonLat($newlon, $newlat, $brng, $dist);
    $newlon = $test->getLongitude();
    $newlat = $test->getLatitude();
    array_push($loc_array,Array("Latitude" => $newlat, "Longitude" => $newlon));

    $brng = 240;
    $test->computerThatLonLat($newlon, $newlat, $brng,2 * $dist);
    $newlon = $test->getLongitude();
    $newlat = $test->getLatitude();
    array_push($loc_array,Array("Latitude" => $newlat, "Longitude" => $newlon));

    $brng = 0;
    $test->computerThatLonLat($newlon, $newlat, $brng,$dist);
    $newlon = $test->getLongitude();
    $newlat = $test->getLatitude();
    array_push($loc_array,Array("Latitude" => $newlat, "Longitude" => $newlon));

    $brng = 120;
    $test->computerThatLonLat($newlon, $newlat, $brng,2 * $dist);
    $newlon = $test->getLongitude();
    $newlat = $test->getLatitude();
    array_push($loc_array,Array("Latitude" => $newlat, "Longitude" => $newlon));

    $brng = 240;
    $test->computerThatLonLat($newlon, $newlat, $brng,$dist);
    $newlon = $test->getLongitude();
    $newlat = $test->getLatitude();
    array_push($loc_array,Array("Latitude" => $newlat, "Longitude" => $newlon));

    $brng = 0;
    $test->computerThatLonLat($newlon, $newlat, $brng,$dist);
    $newlon = $test->getLongitude();
    $newlat = $test->getLatitude();
    array_push($loc_array,Array("Latitude" => $newlat, "Longitude" => $newlon));
 
    /*while($wide<$t_weight){
        if($brng==0){
            $Pa = 90;
        }else if($brng == 180){
            $Pa = -90;
        }
        $brng = $brng + $Pa;
        $test->computerThatLonLat($newlon, $newlat, $brng, $weight);
        $newlon = $test->getLongitude();
        $newlat = $test->getLatitude();
        array_push($loc_array,Array("Latitude" => $newlat, "Longitude" => $newlon));

        if($brng==0){
            $Pa = 90;
        }else if($brng == 180){
            $Pa = -90;
        }
        $brng = $brng + $Pa;
        $test->computerThatLonLat($newlon, $newlat, $brng, $height);
        $newlon = $test->getLongitude();
        $newlat = $test->getLatitude();
        array_push($loc_array,Array("Latitude" => $newlat, "Longitude" => $newlon));
        $wide = $wide + $weight;
    }*/
    return $loc_array;
}




/*function getwaypoin($drone, $lat, $lon, $radius, $altitude){
        $all_waypoint = [];
    switch ($drone) {
        case 1:
            $brng = 225;
            $dist = $radius * pow(2, 0.5);
            $weight = $radius * 2 / ceil(($radius * 2) / (1.6 * $altitude));
            $t_weight = $radius * 2;
            $height = 2 * $radius;
            $waypoint1 = waypoint1($lon, $lat, $height, $weight, $dist, $brng, $t_weight);
            array_push($all_waypoint,Array("Drone 1" => $waypoint1));
            break;
        case 2:
            for($i=1; $i<=$drone; $i++){
                $weight = $radius / ceil($radius / (1.6 * $altitude));
                $t_weight = $radius;
                $height = 2 * $radius;
                if($i==1){
                    $brng = 225;
                    $dist = $radius * pow(2, 0.5);
                    $waypoint1 = waypoint1($lon, $lat, $height, $weight, $dist, $brng, $t_weight);
                    array_push($all_waypoint,Array("Drone 1" => $waypoint1));
                }else if($i==2){
                    $brng = 180;
                    $dist = $radius;
                    $waypoint2 = waypoint1($lon, $lat, $height, $weight, $dist, $brng, $t_weight);
                    array_push($all_waypoint,Array("Drone 2" => $waypoint2));
                }
            }
            break;
        case 3:
            for($i=1; $i<=$drone; $i++){
                $weight = $radius / ceil(($radius*2/3) / (1.6 * $altitude));
                $t_weight = $radius*2/3;
                $height = 2 * $radius;
                if($i==1){
                    $brng = 225;
                    $dist = $radius * pow(2, 0.5);
                    $waypoint1 = waypoint1($lon, $lat, $height, $weight, $dist, $brng, $t_weight);
                    array_push($all_waypoint,Array("Drone 1" => $waypoint1));
                }else if($i==2){
                    $brng = 180 + rad2deg(atan(($radius / 3) / $radius));
                    $dist = (pow(10, 0.5) * $radius) / 3;
                    $waypoint2 = waypoint1($lon, $lat, $height, $weight, $dist, $brng, $t_weight);
                    array_push($all_waypoint,Array("Drone 2" => $waypoint2));
                }else if($i==3){
                    $brng = 180 - rad2deg(atan(($radius / 3) / $radius));
                    $dist = (pow(10, 0.5) * $radius) / 3;
                    $waypoint3 = waypoint1($lon, $lat, $height, $weight, $dist, $brng, $t_weight);
                    array_push($all_waypoint,Array("Drone 3" => $waypoint3));
                }
            }
            break;
        case 4:
            for($i=1; $i<=$drone; $i++){
                $weight = $radius / ceil($radius / (1.6 * $altitude));
                $t_weight = $radius;
                $height = $radius;
                if($i==1){
                    $brng = 225;
                    $dist = $radius * pow(2, 0.5);
                    $waypoint1 = waypoint1($lon, $lat, $height, $weight, $dist, $brng, $t_weight);
                    array_push($all_waypoint,Array("Drone 1" => $waypoint1));
                }else if($i==2){
                    $brng = 180;
                    $dist = $radius;
                    $waypoint2 = waypoint1($lon, $lat, $height, $weight, $dist, $brng, $t_weight);
                    array_push($all_waypoint,Array("Drone 2" => $waypoint2));
                }else if($i==3){
                    $brng = 270;
                    $dist = $radius;
                    $waypoint3 = waypoint1($lon, $lat, $height, $weight, $dist, $brng, $t_weight);
                    array_push($all_waypoint,Array("Drone 3" => $waypoint3));
                }else if($i==4){
                    $brng = 0;
                    $dist = 0;
                    $waypoint4 = waypoint1($lon, $lat, $height, $weight, $dist, $brng, $t_weight);
                    array_push($all_waypoint,Array("Drone 4" => $waypoint4));
                }
            }
            break;
    }
    return $all_waypoint;
}

 */
function getwaypoin($drone, $lat, $lon, $radius){
    $all_waypoint = [];
	$waypoint1 = [];
	$calculator = new LonLatCalculator;
	$brng = 0;
    switch ($drone) {
        case 1:
			array_push($waypoint1,Array("Latitude" => $lat, "Longitude" => $lon));
			$newlon = 0;
			$newlat = 0;
			$brng = 225;

			$calculator -> computerThatLonLat($lon, $lat, $brng, $radius);
			$newlon = $calculator->getLongitude();
			$newlat = $calculator->getLatitude();
			array_push($waypoint1,Array("Latitude" => $newlat, "Longitude" => $newlon));

			$brng = 0;
			$calculator -> computerThatLonLat($newlon, $newlat, $brng, 2 * $radius);
			$newlon = $calculator->getLongitude();
			$newlat = $calculator->getLatitude();
			array_push($waypoint1,Array("Latitude" => $newlat, "Longitude" => $newlon));

			$brng = 90;
			$calculator -> computerThatLonLat($newlon, $newlat, $brng, $radius / 2);
			$newlon = $calculator->getLongitude();
			$newlat = $calculator->getLatitude();
			array_push($waypoint1,Array("Latitude" => $newlat, "Longitude" => $newlon));

			$brng = 180;
			$calculator -> computerThatLonLat($newlon, $newlat, $brng, 2 * $radius);
			$newlon = $calculator->getLongitude();
			$newlat = $calculator->getLatitude();
			array_push($waypoint1,Array("Latitude" => $newlat, "Longitude" => $newlon));

			$brng = 90;
			$calculator -> computerThatLonLat($newlon, $newlat, $brng, $radius / 2);
			$newlon = $calculator->getLongitude();
			$newlat = $calculator->getLatitude();
			array_push($waypoint1,Array("Latitude" => $newlat, "Longitude" => $newlon));


			$brng = 0;
			$calculator -> computerThatLonLat($newlon, $newlat, $brng, 2 * $radius );
			$newlon = $calculator->getLongitude();
			$newlat = $calculator->getLatitude();
			array_push($waypoint1,Array("Latitude" => $newlat, "Longitude" => $newlon));

			$brng = 90;
			$calculator -> computerThatLonLat($newlon, $newlat, $brng, $radius / 2);
			$newlon = $calculator->getLongitude();
			$newlat = $calculator->getLatitude();
			array_push($waypoint1,Array("Latitude" => $newlat, "Longitude" => $newlon));

			$brng = 180;
			$calculator -> computerThatLonLat($newlon, $newlat, $brng, 2 * $radius);
			$newlon = $calculator->getLongitude();
			$newlat = $calculator->getLatitude();
			array_push($waypoint1,Array("Latitude" => $newlat, "Longitude" => $newlon));

			$brng = 90;
			$calculator -> computerThatLonLat($newlon, $newlat, $brng, $radius / 2);
			$newlon = $calculator->getLongitude();
			$newlat = $calculator->getLatitude();
			array_push($waypoint1,Array("Latitude" => $newlat, "Longitude" => $newlon));

			$brng = 0;
			$calculator -> computerThatLonLat($newlon, $newlat, $brng, 2 * $radius );
			$newlon = $calculator->getLongitude();
			$newlat = $calculator->getLatitude();
			array_push($waypoint1,Array("Latitude" => $newlat, "Longitude" => $newlon));

            array_push($all_waypoint,Array("Drone 1" => $waypoint1));
            break;
        case 2:

			array_push($waypoint1,Array("Latitude" => $lat, "Longitude" => $lon));
			$newlon = 0;
			$newlat = 0;
			$brng = 0;

			$calculator -> computerThatLonLat($lon, $lat, $brng, $radius);
			$newlon = $calculator->getLongitude();
			$newlat = $calculator->getLatitude();
			array_push($waypoint1,Array("Latitude" => $newlat, "Longitude" => $newlon));

			$brng = 270;
			$calculator -> computerThatLonLat($newlon, $newlat, $brng, $radius / 2 );
			$newlon = $calculator->getLongitude();
			$newlat = $calculator->getLatitude();
			array_push($waypoint1,Array("Latitude" => $newlat, "Longitude" => $newlon));

			$brng = 180;
			$calculator -> computerThatLonLat($newlon, $newlat, $brng, 2 * $radius);
			$newlon = $calculator->getLongitude();
			$newlat = $calculator->getLatitude();
			array_push($waypoint1,Array("Latitude" => $newlat, "Longitude" => $newlon));

			$brng = 270;
			$calculator -> computerThatLonLat($newlon, $newlat, $brng, $radius / 2 );
			$newlon = $calculator->getLongitude();
			$newlat = $calculator->getLatitude();
			array_push($waypoint1,Array("Latitude" => $newlat, "Longitude" => $newlon));

			$brng = 0;
			$calculator -> computerThatLonLat($newlon, $newlat, $brng, 2 * $radius);
			$newlon = $calculator->getLongitude();
			$newlat = $calculator->getLatitude();
			array_push($waypoint1,Array("Latitude" => $newlat, "Longitude" => $newlon));


			array_push($all_waypoint,Array("Drone 1" => $waypoint1));

			$waypoint2 = [];

			array_push($waypoint2,Array("Latitude" => $lat, "Longitude" => $lon));
			$newlon = 0;
			$newlat = 0;
			$brng = 180;

			$calculator -> computerThatLonLat($lon, $lat, $brng, $radius);
			$newlon = $calculator->getLongitude();
			$newlat = $calculator->getLatitude();
			array_push($waypoint2,Array("Latitude" => $newlat, "Longitude" => $newlon));

			$brng = 90;
			$calculator -> computerThatLonLat($newlon, $newlat, $brng, $radius / 2 );
			$newlon = $calculator->getLongitude();
			$newlat = $calculator->getLatitude();
			array_push($waypoint2,Array("Latitude" => $newlat, "Longitude" => $newlon));

			$brng = 0;
			$calculator -> computerThatLonLat($newlon, $newlat, $brng, 2 * $radius );
			$newlon = $calculator->getLongitude();
			$newlat = $calculator->getLatitude();
			array_push($waypoint2,Array("Latitude" => $newlat, "Longitude" => $newlon));

			$brng = 90;
			$calculator -> computerThatLonLat($newlon, $newlat, $brng, $radius / 2 );
			$newlon = $calculator->getLongitude();
			$newlat = $calculator->getLatitude();
			array_push($waypoint2,Array("Latitude" => $newlat, "Longitude" => $newlon));

			$brng = 180;
			$calculator -> computerThatLonLat($newlon, $newlat, $brng, 2 * $radius);
			$newlon = $calculator->getLongitude();
			$newlat = $calculator->getLatitude();
			array_push($waypoint2,Array("Latitude" => $newlat, "Longitude" => $newlon));

			array_push($all_waypoint,Array("Drone 2" => $waypoint2));

            break;

    }
    return $all_waypoint;
}
