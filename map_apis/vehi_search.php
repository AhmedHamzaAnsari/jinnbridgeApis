<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: *");
ini_set('max_execution_time', -1);
date_default_timezone_set("Asia/Karachi");
include("../config_apis.php");
// include_once('../includes/connect_database.php'); 
// include_once('../includes/variables.php');
if (isset($_GET['accesskey'])) {
	$access_key_received = $_GET['accesskey'];
	$str = $_GET['str'];
	$sel = $_GET['sel'];
	$user = $_GET['user'];
	$access_key = "12345";

	if ($access_key_received == $access_key) {
		// get all category data from category table
		if ($sel == 1) {
			$sql_query = "SELECT * FROM `devicesnew` as pos join users_devices_new ud on pos.id = ud.devices_id where pos.speed=0 and pos.ignition = 'OFF' and ud.users_id = $user and pos.time >=curdate() and  name like '%$str%'  order by time desc limit 50 ;";
		}
		if ($sel == 2) {
			$sql_query = "SELECT * FROM `devicesnew` as pos join users_devices_new ud on pos.id = ud.devices_id where pos.speed > 0 and pos.speed < 50 and ud.users_id = $user and pos.ignition ='ON' and pos.time >=curdate()  and  name like '%$str%'  order by time desc limit 50 ;";
		}
		if ($sel == 3) {
			$sql_query = "SELECT * FROM `devicesnew` as pos join users_devices_new ud on pos.id = ud.devices_id where pos.speed = 0 and pos.ignition ='ON' and ud.users_id = $user and pos.time >=CURDATE() and name like '%$str%' order by time desc limit 50 ;";
		}
		if ($sel == 4) {
			$sql_query = "SELECT * FROM `devicesnew` as pos join users_devices_new ud on pos.id = ud.devices_id where  pos.time <=CURDATE()  and ud.users_id = $user  and name like '%$str%' order by time desc limit 50 ;";
		}
		if ($sel == 5) {
			$sql_query = "SELECT * FROM `devicesnew` as pos join users_devices_new ud on pos.id = ud.devices_id where pos.speed>=50 and pos.ignition = 'ON' and ud.users_id = $user and pos.time >=CURDATE() and name like '%$str%' limit 50 ;";
		}
		if ($sel == 6) {
			$sql_query = "SELECT * FROM devicesnew as pos join users_devices_new ud on pos.id = ud.devices_id  where ud.users_id = $user and name like '%$str%' limit 50 ;";
		}


		$result = $connect->query($sql_query) or die("Error :" . mysqli_error());

		$users = array();
		while ($user = $result->fetch_assoc()) {
			$users[] = $user;
		}

		// create json output
		$output = json_encode($users);
	} else {
		die('accesskey is incorrect.');
	}
} else {
	die('accesskey is required.');
}

//Output the output.
echo $output;

// include_once('../includes/close_database.php'); 
?>