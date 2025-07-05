<?php
ini_set('max_execution_time', '0');
$url1 = $_SERVER['REQUEST_URI'];
header("Refresh: 20; URL=$url1");
include("../../config.php");
set_time_limit(5000);

$access_key = '03201232927';

$pass = isset($_GET["key"]) ? $_GET["key"] : '';
$date = date('Y-m-d H:i:s');
echo "<h1>Sap Trip ETA Check service .</h1><br>";

if ($pass != '') {
    if ($pass == $access_key) {
        $sql_query1 = "SELECT dt.*,ps.vehicle,dl.`co-ordinates` as co,dl.name
        FROM puma_sap_data_trips as dt 
        join dealers as dl on dl.sap_no=CAST(TRIM(LEADING '0' FROM dt.dealer_sap ) AS UNSIGNED)
        join puma_sap_data as ps on ps.id=dt.main_id 
        JOIN order_main as om ON om.SaleOrder=dt.salesapNo 
        where om.delivered_status=1 and dt.status=0 and ps.is_tracker=1 
         group by om.SaleOrder;";

        $result1 = $db->query($sql_query1) or die("Error in SQL query: " . $db->error);

        while ($user = $result1->fetch_assoc()) {
            $id = $user['id'];
            $co = $user['co'];
            $vehicle = $user['vehicle'];
            $mychars = explode(', ', $co);
            $c_lat = floatval($mychars[0]);
            $c_lng = floatval($mychars[1]);

            $get_vehicle_data_query = "SELECT * FROM devicesnew WHERE name = '$vehicle'";
            $vehicle_data_result = mysqli_query($db, $get_vehicle_data_query);

            if (!$vehicle_data_result) {
                die("Query failed: " . mysqli_error($db));
            }

            if ($vehicle_row = mysqli_fetch_array($vehicle_data_result)) {
                $v_lat = $vehicle_row['lat'];

                $vehicle_id = $vehicle_row['id'];
                $v_lat = $vehicle_row['lat'];
                $v_lng = $vehicle_row['lng'];



                $distance = calculateDistance($v_lat, $v_lng, $c_lat, $c_lng);

                if ($distance !== null) {
                    $sql_query2 = "SELECT *,DATE_ADD(DATE_ADD(created_at, INTERVAL ($distance/30) HOUR), INTERVAL 20 MINUTE) as eta_time FROM puma_sap_data_trips where id='$id';";
                    $result2 = $db->query($sql_query2) or die("Error in SQL query: " . $db->error);

                    while ($user2 = $result2->fetch_assoc()) {
                        $eta_time = $user2['eta_time'];
                        $created_at = $user2['created_at'];

                        $update = "UPDATE `puma_sap_data_trips`
                        SET
                        `status` = '1',
                        `active_time` = '$created_at',
                        `eta` = '$eta_time',
                        `distance` = '$distance',
                        `dealer_lat` = '$c_lat',
                        `dealer_lng` = '$c_lng'
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

echo 'Service Last Run => ' . date('Y-m-d H:i:s');
?>