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
	$user = $_GET['user'];
	// $offset = $_GET['offset'];
	$todate=date("Y-m-d H:i:s", time());
    $prev_date=date("Y-m-d H:i:s", strtotime($todate .' -1 day'));
	$access_key = "12345";
	if ($user === 'resq911' || $user === 'tw_x') {
        $cls = "AND pos.tracker = '" . mysqli_real_escape_string($connect, $user) . "' and ud.users_id='1'";
    }
	else{
		
		$cls = "and ud.users_id='$user'";
	}
	if ($access_key_received == $access_key) {
		// get all category data from category table
		$sql_query = "SELECT *  FROM `devicesnew` as pos join users_devices_new ud on pos.id = ud.devices_id where pos.speed=0 and pos.ignition = 'OFF' $cls and pos.time >='$prev_date' order by time desc";

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