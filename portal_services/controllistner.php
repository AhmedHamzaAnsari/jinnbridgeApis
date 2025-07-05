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
$q_update = "UPDATE bulkdatanew set status = '1' order by st_server desc";
mysqli_query($connection, $q_update);

$sql = "SELECT * FROM bulkdatanew WHERE status = '1' order by imei asc, st_server desc";
$sql_select = mysqli_query($connection, $sql);
mysqli_error($connection);
$todate = date("Y-m-d H:i:s", time());
//foreach( mysqli_fetch_array($sql_select) as $row){
while ($row = mysqli_fetch_array($sql_select)) {
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
	if ($mainid == 'TPL') {
		$userid = 439;
	} else {
		$userid = 30;
	}
	$d_select = "SELECT * FROM devicesnew WHERE name ='$vehicle'";
	$sql_devices = mysqli_query($connection, $d_select);
	mysqli_error($connection);
	if (mysqli_num_rows($sql_devices) > 0) {
		// $Select1 = "SELECT time,id FROM devicesnew WHERE name ='$vehicle'";
		// $exe_Select1 = mysqli_query($connection, $Select1);
		// mysqli_error($connection);
		// $data_Select1 = mysqli_fetch_assoc($exe_Select1);

		$data_Select1 = mysqli_fetch_assoc($sql_devices);
		echo $data_Select1['name'];
		echo "<br>";
		if (!isset($data_Select1['time'])) {
			$lasttime = '2020-01-01 10:10:10';
		} else {
			$lasttime = $data_Select1['time']; //--------------err
		}
		// echo $lasttime.' imei '.$imei .' servertime '. $dt_time. '<br>';
		// if ($dt_time > $lasttime) {
		// 	$dviceid = $data_Select1['id'];
		// 	// $pos_select ="SELECT id FROM Jinn.positions_log where device_id= '$dviceid' order by id desc limit 1";
		// 	// $pos_Select1 = mysqli_query( $connection,$pos_select);
		// 	// mysqli_error($connection);
		// 	// $posdataSelect1= mysqli_fetch_assoc($pos_Select1);
		// 	// $latest = $posdataSelect1['id'];
		// 	$update_devices = "UPDATE devicesnew SET  time = '$dt_time', lat = '$lat' , lng = '$lng', angle = '$angle' , ignition = '$list', speed ='$speed' , odometer = '$odometer' , lasttime = '$todate' ,location = '$last_stop' where imei ='$imei'";
		// 	$basit1 = mysqli_query($connection, $update_devices);
		// 	echo "Record update $imei <br>";

		// 	if ($basit1 == TRUE) {
		// 		$insert_positions = "INSERT into positions_log(latitude,longitude,address,speed,power,odometer,course,tracker,time,vehicle_name,device_id)
		// 		VALUES ('$lat','$lng','$last_stop','$speed','$list','$odometer','$angle','$protocol','$dt_time','$name','$dviceid');";
		// 		$basit = mysqli_query($connection, $insert_positions);
		// 		echo mysqli_error($connection);
		// 	}
		// }
		$dt_time_obj = new DateTime($dt_time);
		$lasttime_obj = new DateTime($lasttime);
		echo $dt_time;
		echo "<br>";
		echo $lasttime;
		echo "<br>";
		// Compare the DateTime objects
		if ($dt_time_obj > $lasttime_obj) {
			// Update devicesnew table
			$dviceid = $data_Select1['id']; // Assuming $data_Select1['id'] is properly defined
			$update_devices = "UPDATE devicesnew SET time = '$dt_time', lat = '$lat', lng = '$lng', angle = '$angle', ignition = '$list', speed ='$speed', odometer = '$odometer', lasttime = '$todate', location = '$last_stop' WHERE id ='$dviceid'";
			echo $update_devices;
			echo "<br>";
			// Execute the update query
			$basit1 = mysqli_query($connection, $update_devices);
			if ($basit1) {
				echo "Update successful <br>";
			} else {
				echo "Error updating record: " . mysqli_error($connection) . "<br>";
			}

			// Insert into positions_log table
			$insert_positions = "INSERT INTO positions_log (latitude, longitude, address, speed, power, odometer, course, tracker, time, vehicle_name, device_id) VALUES ('$lat', '$lng', '$last_stop', '$speed', '$list', '$odometer', '$angle', '$protocol', '$dt_time', '$name', '$dviceid')";
			$basit = mysqli_query($connection, $insert_positions);
			if ($basit) {
				echo "Record inserted successfully <br>";
			} else {
				echo "Error inserting record: " . mysqli_error($connection) . "<br>";
			}
		} else {
			// Handle the case where $dt_time is not greater than $lasttime
			echo "$dt_time is not greater than $lasttime";
		}

	} else {
		//New installation

		$sql_insert_dev = mysqli_query($connection, "INSERT INTO  devicesnew 
			(name, uniqueId, latestPosition_id, device_type, trackername, organisation, tracker, speed, speedlimit, lat, lng, location, time,
			angle, imei, odometer, ignition, lasttime, activedate)  
			VALUES 
			('$vehicle','','','1','$mainid','','$mainid','$speed','$speed', '$lat', '$lng','$last_stop','$dt_time','$angle','$imei','$odometer','$list','$todate','$todate')") or die(mysqli_error($connection));
		$Selectnew = "SELECT time,id FROM devicesnew WHERE imei ='$imei'";
		$exe_Selectnew = mysqli_query($connection, $Selectnew);
		mysqli_error($connection);
		$data_Selectnew = mysqli_fetch_assoc($exe_Selectnew);
		$dviceidnew = $data_Selectnew['id'];

		$insert_positions_new = "INSERT into positions_log(latitude,longitude,address,speed,power,odometer,course,tracker,time,vehicle_name,device_id)
			VALUES ('$lat','$lng','$last_stop','$speed','$list','$odometer','$angle','$protocol','$dt_time','$name','$dviceidnew');";
		$basit_new = mysqli_query($connection, $insert_positions_new);

		//inser user 1
		$sql_user_one = mysqli_query($connection, "INSERT INTO  users_devices_new (users_id, devices_id, subacc_id, show_authority)   
			VALUES ('1' , '$dviceidnew' , '0' , '0')") or die(mysqli_error($connection));
		// $inserted_one = mysqli_query($connection,$sql_user_one);

		//inser user 2
		$inserted_three = mysqli_query($connection, "INSERT INTO  users_devices_new (users_id, devices_id, subacc_id, show_authority)   
			VALUES ('$userid' , '$dviceidnew' , '2' ,'1')") or die(mysqli_error($connection));
		// $inserted_three = mysqli_query($connection,$sql_user_three);
		echo $name . " record Inserted <br>";
	}
}


if ($sql_select == true) {
	$q_delete = "DELETE FROM bulkdatanew WHERE status = '1'";
	$sql_delete = mysqli_query($connection, $q_delete);
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