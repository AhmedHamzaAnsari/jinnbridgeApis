<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: *");
ini_set('max_execution_time', -1);
date_default_timezone_set("Asia/Karachi");
include("../../config.php");

if (isset($_GET['accesskey'])) {
    $access_key_received = $_GET['accesskey'];
    $user = intval($_GET['id']);
    $access_key = "12345";
    $from = $_GET['from'];
    $to = $_GET['to'];

    $todate = date("Y-m-d H:i:s", time());
    $prev_date = date("Y-m-d H:i:s", strtotime($todate . ' -1 day'));

    if ($access_key_received == $access_key) {
        $que1 = "SELECT COUNT(DISTINCT dc.name) as all_devices FROM users_devices_new as ud 
        JOIN devicesnew as dc ON dc.id=ud.devices_id WHERE ud.users_id=$user";

        $que2 = "SELECT COUNT(DISTINCT dc.name) as moving_devices FROM users_devices_new as ud 
        JOIN devicesnew as dc ON dc.id=ud.devices_id WHERE dc.speed > 0 AND dc.speed < 60 AND dc.time >= '$prev_date' AND ud.users_id='$user'";

        $que3 = "SELECT COUNT(DISTINCT dc.name) as stop_devices FROM users_devices_new as ud 
        JOIN devicesnew as dc ON dc.id=ud.devices_id WHERE dc.speed = 0 AND dc.ignition = 'Off' AND dc.time >= '$prev_date' AND ud.users_id='$user'";

        $que4 = "SELECT COUNT(DISTINCT dc.name) as nr_devices FROM users_devices_new as ud 
        JOIN devicesnew as dc ON dc.id=ud.devices_id WHERE dc.time <= '$prev_date' AND ud.users_id='$user'";

        $que5 = "SELECT COUNT(DISTINCT dc.name) as idle_devices FROM users_devices_new as ud 
        JOIN devicesnew as dc ON dc.id=ud.devices_id WHERE dc.speed = 0 AND dc.ignition = 'On' AND dc.time >= '$prev_date' AND ud.users_id='$user'";

        $que6 = "SELECT COUNT(*) as black_count FROM geo_check as ck
        JOIN geofenceing as geo ON geo.id=ck.geo_id
        JOIN users_devices_new as ud ON ud.devices_id=ck.veh_id
        JOIN devicesnew as dc ON dc.id=ud.devices_id
        WHERE geo.geotype = 'Black Spote'
        AND DATE(ck.in_time) >= '$from'
        AND DATE(ck.in_time) <= '$to'
        AND ud.users_id='$user'
        AND ck.in_duration >= 60";

        $que7 = "SELECT COUNT(*) as night_count FROM driving_alerts as da 
        JOIN devicesnew as dc ON dc.id=da.device_id 
        WHERE da.type = 'Night time violations' AND da.created_at >= '$from' AND da.created_at <= '$to' AND da.created_by='$user'";

        $que8 = "SELECT COUNT(*) as excess_count FROM axcess_driving_alerts as da 
        JOIN devicesnew as dc ON dc.id=da.vehicle_id 
        WHERE da.created_at >= '$from' AND da.created_at <= '$to' AND da.created_by='$user'";

        $que9 = "SELECT COUNT(dc.name) as overspeed_devices FROM users_devices_new as ud 
        join devicesnew as dc on dc.id=ud.devices_id where dc.time <='$prev_date' and ud.users_id='$user' and dc.speed>55";

        $result1 = $db->query($que1) or die("Error: " . mysqli_error($db));
        $result2 = $db->query($que2) or die("Error: " . mysqli_error($db));
        $result3 = $db->query($que3) or die("Error: " . mysqli_error($db));
        $result4 = $db->query($que4) or die("Error: " . mysqli_error($db));
        $result5 = $db->query($que5) or die("Error: " . mysqli_error($db));
        $result6 = $db->query($que6) or die("Error: " . mysqli_error($db));
        $result7 = $db->query($que7) or die("Error: " . mysqli_error($db));
        $result8 = $db->query($que8) or die("Error: " . mysqli_error($db));
        $result9 = $db->query($que9) or die("Error: " . mysqli_error($db));

        $row1 = mysqli_fetch_array($result1);
        $row2 = mysqli_fetch_array($result2);
        $row3 = mysqli_fetch_array($result3);
        $row4 = mysqli_fetch_array($result4);
        $row5 = mysqli_fetch_array($result5);
        $row6 = mysqli_fetch_array($result6);
        $row7 = mysqli_fetch_array($result7);
        $row8 = mysqli_fetch_array($result8);
        $row9 = mysqli_fetch_array($result9);

        $post_data = array(
            'all_devices' => $row1['all_devices'],
            'moving_devices' => $row2['moving_devices'],
            'stop_devices' => $row3['stop_devices'],
            'nr_devices' => $row4['nr_devices'],
            'idle_devices' => $row5['idle_devices'],
            'black_count' => $row6['black_count'],
            'night_count' => $row7['night_count'],
            'excess_count' => $row8['excess_count'],
            'overspeed_devices' => $row9['overspeed_devices']
        );

        $post_data = json_encode($post_data);
    } else {
        die('Access key is incorrect.');
    }
} else {
    die('Access key and required parameters are missing.');
}

echo $post_data;
?>
