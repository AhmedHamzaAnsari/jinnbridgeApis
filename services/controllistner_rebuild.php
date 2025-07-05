<?php
ini_set('max_execution_time', -1);
date_default_timezone_set("Asia/Karachi");

// Refresh page every 30 seconds
$url1 = $_SERVER['REQUEST_URI'];
header("Refresh: 30; URL=$url1");

include("../config_apis.php");

echo 'Start time => ' . date('Y-m-d H:i:s') . '<br>';

// Function to clean strings
function clean($string) {
    // Replaces all spaces with hyphens and removes special characters
    $string = str_replace(' ', '-', $string);
    return preg_replace('/[^A-Za-z0-9]/', '', $string);
}

try {
    // Update bulkdatanew status
    $q_update = "UPDATE bulkdatanew SET status = '1' ORDER BY st_server DESC";
    mysqli_query($connect, $q_update);

    // Select rows from bulkdatanew
    $sql = "SELECT * FROM bulkdatanew WHERE status = '1' ORDER BY imei ASC, st_server DESC";
    $sql_select = mysqli_query($connect, $sql);
    $todate = date("Y-m-d H:i:s", time());
    $bat = 0;

    // Loop through selected rows
    while ($row = mysqli_fetch_array($sql_select)) {
        $imei = $row['imei'];
        $mainid = $row["protocol"];
        $dt_time = $row["st_server"];
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
        $last_stop = $row["last_stop"];

        // Determine userid based on mainid
        $userid = ($mainid == 'TPL') ? 439 : 30;

        // Check if the device already exists
        $d_select = "SELECT * FROM devicesnew WHERE name = '$vehicle'";
        $sql_devices = mysqli_query($connect, $d_select);

        if (mysqli_num_rows($sql_devices) > 0) {
            $data_Select1 = mysqli_fetch_assoc($sql_devices);
            $lasttime = isset($data_Select1['time']) ? $data_Select1['time'] : '2020-01-01 10:10:10';
            $dt_time_obj = new DateTime($dt_time);
            $lasttime_obj = new DateTime($lasttime);

            // Update device if the new time is later
            if ($dt_time_obj > $lasttime_obj) {
                $dviceid = $data_Select1['id'];
                $update_devices = "UPDATE devicesnew 
                                   SET time = '$dt_time', lat = '$lat', lng = '$lng', angle = '$angle', 
                                       ignition = '$list', speed = '$speed', odometer = '$odometer', 
                                       lasttime = '$todate', location = '$last_stop' 
                                   WHERE id = '$dviceid'";
                mysqli_query($connect, $update_devices);

                // Insert into positions_log
                $insert_positions = "INSERT INTO positions_log 
                                     (latitude, longitude, address, speed, power, odometer, course, tracker, time, vehicle_name, device_id) 
                                     VALUES ('$lat', '$lng', '$last_stop', '$speed', '$list', '$odometer', '$angle', '$protocol', '$dt_time', '$name', '$dviceid')";
                mysqli_query($connect, $insert_positions);
            } else {
                echo "$dt_time is not greater than $lasttime<br>";
            }
        } else {
            // Handle uniqueId if necessary
            $uniqueId = ''; // Set this to the appropriate value or logic

            if ($uniqueId === '') {
                $uniqueId = 0; // Set to NULL if empty or assign a valid integer value
            }

            // Insert new device
            $sql_insert_dev = "INSERT INTO devicesnew 
                               (name, device_type, trackername, organisation, tracker, speed, speedlimit, lat, lng, location, time, angle, imei, odometer, ignition, lasttime, activedate)  
                               VALUES ('$vehicle', '1', '$mainid', '', '$mainid', '$speed', '$speed', '$lat', '$lng', '$last_stop', '$dt_time', '$angle', '$imei', '$odometer', '$list', '$todate', '$todate')";
            mysqli_query($connect, $sql_insert_dev);

            // Get the last inserted device id
            $dviceidnew = mysqli_insert_id($connect);

            // Insert into positions_log table
            $insert_positions_new = "INSERT INTO positions_log 
                                     (latitude, longitude, address, speed, power, odometer, course, tracker, time, vehicle_name, device_id)
                                     VALUES ('$lat', '$lng', '$last_stop', '$speed', '$list', '$odometer', '$angle', '$protocol', '$dt_time', '$name', '$dviceidnew')";
            mysqli_query($connect, $insert_positions_new);

            // Insert user 1
            $sql_user_one = "INSERT INTO users_devices_new 
                             (users_id, devices_id, subacc_id, show_authority)   
                             VALUES ('1', '$dviceidnew', '0', '0')";
            mysqli_query($connect, $sql_user_one);

            // Insert user 2
            $sql_user_two = "INSERT INTO users_devices_new 
                             (users_id, devices_id, subacc_id, show_authority)   
                             VALUES ('$userid', '$dviceidnew', 2, '1')";
            mysqli_query($connect, $sql_user_two);

            echo "$name record Inserted<br>";
        }

        $bat++;
    }

    // Delete processed rows
    if ($sql_select) {
        $q_delete = "DELETE FROM bulkdatanew WHERE status = '1'";
        mysqli_query($connect, $q_delete);
    }
    echo "done delete";
    
    // Custom function calls if needed (trip_close, trip_eta)
    // trip_close();
    // trip_eta();

    echo "Last Run " . date('Y-m-d H:i:s');

    // Close the connection
    mysqli_close($connect);

} catch (Exception $e) {
    echo 'Error: ' . $e->getMessage();
}

// Function to close trips
function trip_close() {
    $curl = curl_init();
    curl_setopt_array($curl, array(
        CURLOPT_URL => 'http://' . $_SERVER['HTTP_HOST'] . '/JinnbridgeApis/services/orders/geo_check/sap_trip_close.php',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FOLLOWLOCATION => true,
    ));
    $response = curl_exec($curl);
    curl_close($curl);
    echo $response;
}

// Function to check trip ETA
function trip_eta() {
    $curl = curl_init();
    curl_setopt_array($curl, array(
        CURLOPT_URL => 'http://' . $_SERVER['HTTP_HOST'] . '/JinnbridgeApis/services/orders/sap_trip_eta.php?key=03201232927',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FOLLOWLOCATION => true,
    ));
    $response = curl_exec($curl);
    curl_close($curl);
    echo $response;
}

?>
<?php echo date("d-m-Y H:i:s", time()); ?>
