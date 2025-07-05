<?php
ini_set('max_execution_time', -1);
$url1 = $_SERVER['REQUEST_URI'];
header("Refresh: 400; URL=$url1");
include("../config_apis.php");

function clean($string)
{
    $string = str_replace(' ', '-', $string); // Replaces all spaces with hyphens.
    return preg_replace('/[^A-Za-z0-9-]/', '', $string); // Removes special chars except hyphens.
}

// Tpl_puma

$tel_link_by = "https://mobile.telogix.com.pk/GetUserInfo.asmx/GetUserData?uname=info@jinn.com&upwd=h@fleet";
$tel_file_by = file_get_contents($tel_link_by);

if ($tel_file_by === false) {
    die("Error fetching data from the API.");
}

$tel_replace_by = str_replace('<string xmlns="http://tempuri.org/">', '', $tel_file_by);
$tel_replace1_by = str_replace('<?xml version="1.0" encoding="utf-8"?>', '', $tel_replace_by);
$tel_replace2_by = str_replace('</string>', '', $tel_replace1_by);
$content_tel_by = json_decode($tel_replace2_by, true);

if (json_last_error() !== JSON_ERROR_NONE) {
die("Error decoding JSON data: " . json_last_error_msg());
}

foreach ($content_tel_by as $obj_by) {
$tel_vehicle_clean = "tel" . clean($obj_by['vehicleName']);
$tel_time = $obj_by['gpsTime'];
$tel_lat = $obj_by['lat'];
$tel_long = $obj_by['lng'];
$tel_address = $obj_by['location'];
$tel_angle = $obj_by['Direction'];
$tel_speed = $obj_by['speed'];
$tel_vehicle = $obj_by['vehicleName'];
$tel_odo = $obj_by['mileage'];
$ttigne = $obj_by['Status_Ignition'];
$tel_igne = ($ttigne == 'ON') ? 'On' : 'Off';

$sqltel = "INSERT INTO bulkdatanew
(id, imei, st_server, lat, lng, angle, speed, name, sim_number, odometer, list, protocol, last_idle, last_move,
last_stop, status)
VALUES
('tellogix', '$tel_vehicle_clean', '$tel_time', '$tel_lat', '$tel_long', '$tel_angle', '$tel_speed', '$tel_vehicle', '',
'$tel_odo', '$tel_igne', 'tellogix', '$tel_time', '$tel_time', '$tel_address', '0');";

if (!mysqli_query($connect, $sqltel)) {
echo "Error: " . $sqltel . "<br>" . mysqli_error($connect);
}
}

// Tpl_puma end

if (isset($sqltel) && $sqltel === true) {
echo "<br> New record created successfully, Yahoo TPL ";
}

$sql1 = mysqli_query($connect, "SELECT COUNT(*) as num FROM bulkdatanew");
if ($sql1) {
$result = mysqli_fetch_assoc($sql1);
echo '<br>' . $result['num'];
$t_row = $result['num'];
} else {
echo "Error fetching row count: " . mysqli_error($connect);
}

mysqli_close($connect);
?>
<?php
            echo "Successfully done<br>";
            echo date("d-m-Y H:i:s", time());
            ?>