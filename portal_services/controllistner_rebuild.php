<?php
ini_set('max_execution_time', -1);
date_default_timezone_set("Asia/Karachi");
$username = "root";
$password = "";
$database = 'jinnbridge';
$connection = mysqli_connect('localhost', $username, $password, $database);
if (!$connection) {
    die('Not connected : ' . mysqli_error($connection));
}

// Set the active MySQL database
$db_selected = mysqli_select_db($connection, $database);
if (!$db_selected) {
    die('Can\'t use db : ' . mysqli_error($connection));
}
echo 'Start time => ' . date('Y-m-d H:i:s'), '<br>';

function clean($string)
{
    $string = str_replace('', '-', $string); // Replaces all spaces with hyphens.
    return preg_replace('/[^A-Za-z0-9]/', '', $string); // Removes special chars.
}

// Update bulkdatanew status
$q_update = "UPDATE bulkdatanew SET status = '1' ORDER BY st_server DESC";
mysqli_query($connection, $q_update) or die(mysqli_error($connection));

// Select rows from bulkdatanew
$sql = "SELECT * FROM bulkdatanew WHERE status = '1' ORDER BY imei ASC, st_server DESC";
$sql_select = mysqli_query($connection, $sql) or die(mysqli_error($connection));
$todate = date("Y-m-d H:i:s", time());
$bat = 0;

// Loop through selected rows
while ($row = mysqli_fetch_array($sql_select)) {
    // Retrieve row data
    $id = $row['imei'];
    $imei = $row['imei'];
    $mainid = $row["protocol"];
    $dt_time = $row["st_server"];
    $timein = $row["st_server"];
    $lat = $row["lat"];
    $lng = $row["lng"];
    $angle = $row["angle"];
    $speed = $row["speed"];
    $name = $row["name"];
    $vehicle = $row["name"];
    $licensepn = $row["sim_number"];
    $odometer = $row["odometer"];
    $list = $row["list"];
    $protocol = $row["protocol"];
    $last_idle = $row["last_idle"];
    $last_move = $row["last_move"];
    $last_stop = $row["last_stop"];

    // Determine userid based on mainid
    $userid = ($mainid == 'TPL') ? 439 : 30;

    // Check if device already exists
    $d_select = "SELECT * FROM devicesnew WHERE name ='$vehicle'";
    $sql_devices = mysqli_query($connection, $d_select) or die(mysqli_error($connection));

    // If device exists
    if (mysqli_num_rows($sql_devices) > 0) {
        $data_Select1 = mysqli_fetch_assoc($sql_devices);
        $lasttime = isset($data_Select1['time']) ? $data_Select1['time'] : '2020-01-01 10:10:10';
        $dt_time_obj = new DateTime($dt_time);
		$lasttime_obj = new DateTime($lasttime);
		// echo $dt_time;
		// echo "<br>";
		// echo $lasttime;
		// echo "<br>";
        // Compare timestamps
        if ($dt_time_obj > $lasttime_obj) {
            // Update devicesnew table
            $dviceid = $data_Select1['id'];
            $update_devices = "UPDATE devicesnew SET time = '$dt_time', lat = '$lat', lng = '$lng', angle = '$angle', ignition = '$list', speed ='$speed', odometer = '$odometer', lasttime = '$todate', location = '$last_stop' WHERE id ='$dviceid'";
            mysqli_query($connection, $update_devices) or die(mysqli_error($connection));

            // Insert into positions_log table
            $insert_positions = "INSERT INTO positions_log (latitude, longitude, address, speed, power, odometer, course, tracker, time, vehicle_name, device_id) VALUES ('$lat', '$lng', '$last_stop', '$speed', '$list', '$odometer', '$angle', '$protocol', '$dt_time', '$name', '$dviceid')";
            mysqli_query($connection, $insert_positions) or die(mysqli_error($connection));
        } else {
            echo "$dt_time is not greater than $lasttime";
            echo "<br>";
        }
    } else {
        // Insert new device

        // Insert into devicesnew table
        $sql_insert_dev = "INSERT INTO devicesnew (name, uniqueId, latestPosition_id, device_type, trackername, organisation, tracker, speed, speedlimit, lat, lng, location, time, angle, imei, odometer, ignition, lasttime, activedate)  
        VALUES ('$vehicle', '', '', '1', '$mainid', '', '$mainid', '$speed', '$speed', '$lat', '$lng', '$last_stop', '$dt_time', '$angle', '$imei', '$odometer', '$list', '$todate', '$todate')";
        mysqli_query($connection, $sql_insert_dev) or die(mysqli_error($connection));

        // Get the last inserted device id
        $dviceidnew = mysqli_insert_id($connection);

        // Insert into positions_log table
        $insert_positions_new = "INSERT INTO positions_log (latitude, longitude, address, speed, power, odometer, course, tracker, time, vehicle_name, device_id)
        VALUES ('$lat', '$lng', '$last_stop', '$speed', '$list', '$odometer', '$angle', '$protocol', '$dt_time', '$name', '$dviceidnew')";
        mysqli_query($connection, $insert_positions_new) or die(mysqli_error($connection));

        // Insert user 1
        $sql_user_one = "INSERT INTO users_devices_new (users_id, devices_id, subacc_id, show_authority)   
        VALUES ('1', '$dviceidnew', '0', '0')";
        mysqli_query($connection, $sql_user_one) or die(mysqli_error($connection));

        // Insert user 2
        $sql_user_two = "INSERT INTO users_devices_new (users_id, devices_id, subacc_id, show_authority)   
        VALUES ('$userid', '$dviceidnew', '2', '1')";
        mysqli_query($connection, $sql_user_two) or die(mysqli_error($connection));

        echo "$name record Inserted <br>";
    }
    $bat++;
}

// Delete processed rows
if ($sql_select == true) {
    $q_delete = "DELETE FROM bulkdatanew WHERE status = '1'";
    mysqli_query($connection, $q_delete) or die(mysqli_error($connection));
}
echo "done delete";
mysqli_close($connection);
?>
<!DOCTYPE html>
<html>

<head>
    <meta http-equiv="refresh" content="30">
    <title>Bulk Importer</title>
</head>

<body style="background: #fff;">
    <br>
    <?php echo date("d-m-Y H:i:s", time()); ?>
</body>

</html>
