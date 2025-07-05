<?php
ini_set('max_execution_time', '0');
$url1 = $_SERVER['REQUEST_URI'];
header("Refresh: 20; URL=$url1");
include ("../../config.php");
set_time_limit(5000);

$access_key = '03201232927';
$pass = '03201232927';

// $pass = isset($_GET["key"]) ? $_GET["key"] : '';
$date = date('Y-m-d H:i:s');
echo "<h1>Sap Trip ETA Check service .</h1><br>";

if ($access_key != '') {
    if ($pass == $access_key) {
        $sql_query1 = "SELECT oi.*,dc.id as vehicle_id,dc.name as vehicle_name FROM order_info as oi
        left join devicesnew as dc on TRIM(SUBSTRING_INDEX(dc.name, ' ', 1))=oi.vehicle where oi.status=0 order by oi.id desc";

        $result1 = $db->query($sql_query1) or die("Error in SQL query: " . $db->error);

        while ($user = $result1->fetch_assoc()) {
            $id = $user['id'];
            $customer_id = $user['customer_id'];
            $carrier_code = $user['carrier_code'];
            $vehicle_id = $user['vehicle_id'];

            $is_tracker = ($vehicle_id != '') ? "1" : "0";




            $sql = "SELECT * FROM dealers where sap_no='$customer_id';";

            // echo $sql;

            $result = mysqli_query($db, $sql);
            $row = mysqli_fetch_array($result);
            $count = mysqli_num_rows($result);
            $dealer_co = '';
            if ($count > 0) {

                $dealer_co = $row['co-ordinates'];

            }

            $sql2 = "SELECT * FROM geofenceing where code='$carrier_code' and geotype='depot';";

            // echo $sql;

            $result2 = mysqli_query($db, $sql2);
            $row2 = mysqli_fetch_array($result2);
            $count2 = mysqli_num_rows($result2);
            $geo_co = '';
            if ($count2 > 0) {

                $geo_co = $row2['Coordinates'];

            }

            if ($geo_co != '' && $dealer_co != "") {
                $mychars = explode(', ', $geo_co);
                $geo_lat = floatval($mychars[0]);
                $geo_lng = floatval($mychars[1]);

                $mychars1 = explode(', ', $dealer_co);
                $dealers_lat = floatval($mychars1[0]);
                $dealers_lng = floatval($mychars1[1]);

                // $distance = calculateDistance($geo_lat, $geo_lng, $dealers_lat, $dealers_lng);
                // $distance = mapquest_distance($geo_lat, $geo_lng, $dealers_lat, $dealers_lng);
                $distance = haversineGreatCircleDistance($geo_lat, $geo_lng, $dealers_lat, $dealers_lng);



                echo 'Distance ' . $distance;

                if ($distance !== null) {
                    $sql_query2 = "SELECT *,DATE_ADD(DATE_ADD(created_at, INTERVAL ($distance/30) HOUR), INTERVAL 20 MINUTE) as eta_time FROM order_info where id='$id';";
                    $result2 = $db->query($sql_query2) or die("Error in SQL query: " . $db->error);

                    while ($user2 = $result2->fetch_assoc()) {
                        $eta_time = $user2['eta_time'];
                        $created_at = $user2['created_at'];

                        $update = "UPDATE `order_info`
                            SET
                            `status` = '1',
                            `start_time` = '$created_at',
                            `eta` = '$eta_time',
                            `distance` = '$distance',
                            `is_tracker` = '$is_tracker'
                            WHERE `id` = '$id';";

                        if ($db->query($update)) {
                            echo 'ETA Updated';
                        } else {
                            echo 'Error updating ETA: ' . $db->error;
                        }
                    }
                } else {
                    echo 'Error calculating distance.';
                }
            }




        }
    } else {
        echo 'Wrong Key...';
    }
} else {
    echo 'Key is Required';
}

function calculateDistance($originLat, $originLng, $destLat, $destLng)
{
    $apiKey = 'AIzaSyD9ztWZaPapSg_s2x_VIKx2DwO5zq0gcDU';
    $url = "https://maps.googleapis.com/maps/api/distancematrix/json?units=metric&origins={$originLat},{$originLng}&destinations={$destLat},{$destLng}&key={$apiKey}";

    $response = file_get_contents($url);
    $data = json_decode($response, true);

    if ($data['status'] === 'OK' && isset($data['rows'][0]['elements'][0]['distance']['value'])) {
        $distanceInMeters = $data['rows'][0]['elements'][0]['distance']['value'];
        $distanceInKm = $distanceInMeters / 1000; // Convert meters to kilometers
        return $distanceInKm;
    } else {
        return null;
    }
}

function mapquest_distance($originLat, $originLng, $destLat, $destLng){
    $apiKey = 'bISm97KoaeLqoBEhF7ubFYDjr8zyH1BH';

    $url = "https://www.mapquestapi.com/directions/v2/route?key={$apiKey}&from={$originLat},{$originLng}&to={$destLat},{$destLng}&outFormat=json&ambiguities=ignore&routeType=fastest&doReverseGeocode=false&enhancedNarrative=false&avoidTimedConditions=false";

    $response = file_get_contents($url);
    $data = json_decode($response, true);

    // Check if the route data and distance exist
    if (isset($data['route']['distance'])) {
        // Extract the distance in the specified unit (assuming kilometers in the API response)
        $distanceInKm = $data['route']['distance'];

        // Return the distance in kilometers
        return $distanceInKm;
    } else {
        // Return null if the distance data is not available
        return null;
    }
}

function haversineGreatCircleDistance($latitudeFrom, $longitudeFrom, $latitudeTo, $longitudeTo, $earthRadius = 6371000) {
    // Convert degrees to radians
    $latFrom = deg2rad($latitudeFrom);
    $lonFrom = deg2rad($longitudeFrom);
    $latTo = deg2rad($latitudeTo);
    $lonTo = deg2rad($longitudeTo);

    // Calculate the difference in latitudes and longitudes
    $latDelta = $latTo - $latFrom;
    $lonDelta = $lonTo - $lonFrom;

    // Haversine formula to calculate the distance
    $angle = 2 * asin(sqrt(pow(sin($latDelta / 2), 2) +
              cos($latFrom) * cos($latTo) * pow(sin($lonDelta / 2), 2)));
    
    // Multiply by the Earth's radius (in meters)
    $distanceInMeters = $angle * $earthRadius;

    // Convert meters to kilometers
    $distanceInKilometers = $distanceInMeters / 1000;

    // Add 50% extra distance
    $distanceInKilometers *= 1.5;

    return $distanceInKilometers;
}


echo 'Service Last Run => ' . date('Y-m-d H:i:s');
?>