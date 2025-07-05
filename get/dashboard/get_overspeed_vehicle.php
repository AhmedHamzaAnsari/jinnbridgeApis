<?php
// fetch.php  
include("../../../config_indemnifier.php");
ini_set('max_execution_time', -1);

$access_key = '12345';

$pass = $_GET["accesskey"];
if ($pass != '') {
    $id = $_GET["id"];
    $from = $_GET['from'];
    $to = $_GET['to'];
    if ($pass == $access_key) {
        $sql_query1 = "SELECT da.*, dc.name, dc.lat as v_lat, dc.lng as v_lng 
                       FROM sitara.driving_alerts as da 
                       JOIN devicesnew as dc ON dc.id = da.device_id 
                       WHERE da.type = 'Overspeed' 
                       AND da.created_at >= '$from' 
                       AND da.created_at <= '$to' 
                       AND da.created_by = '$id' 
                       ORDER BY da.time DESC;";

        $result1 = $db->query($sql_query1) or die("Error: " . mysqli_error($db));

        $device_ids = [];
        $alerts = [];
        
        // Collect all device IDs and their in/out times
        while ($user = $result1->fetch_assoc()) {
            $device_ids[] = $user['device_id'];
            $alerts[] = $user;
        }

        // Fetch MIN/MAX speed and odometer for all device IDs in a single query
        if (!empty($device_ids)) {
            $device_ids_str = implode(',', array_unique($device_ids));

            $userQuery = "SELECT device_id, 
                                 MIN(speed) AS min_speed, 
                                 MAX(speed) AS max_speed, 
                                 MIN(odometer) AS min_odometer, 
                                 MAX(odometer) AS max_odometer 
                          FROM sitara.positionsnew 
                          WHERE device_id IN ($device_ids_str) 
                          AND time >= '$from' 
                          AND time <= '$to' 
                          AND speed >= 55 
                          GROUP BY device_id";

            $userResult = $db->query($userQuery);

            $device_data = [];
            while ($row = $userResult->fetch_assoc()) {
                $device_data[$row['device_id']] = [
                    'min_speed' => $row['min_speed'] ?? 0,
                    'max_speed' => $row['max_speed'] ?? 0,
                    'min_odometer' => $row['min_odometer'] ?? 0,
                    'max_odometer' => $row['max_odometer'] ?? 0
                ];
            }

            // Merge data with alerts
            foreach ($alerts as &$alert) {
                $device_id = $alert['device_id'];
                if (isset($device_data[$device_id])) {
                    $alert['min_speed'] = $device_data[$device_id]['min_speed'];
                    $alert['max_speed'] = $device_data[$device_id]['max_speed'];
                    $alert['final_odo'] = $device_data[$device_id]['max_odometer'] - $device_data[$device_id]['min_odometer'];
                } else {
                    $alert['min_speed'] = 0;
                    $alert['max_speed'] = 0;
                    $alert['final_odo'] = 0;
                }
            }
        }

        echo json_encode($alerts);

    } else {
        echo 'Wrong Key...';
    }
} else {
    echo 'Key is Required';
}
?>
