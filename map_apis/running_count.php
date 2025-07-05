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
	// $str = $_GET['str'];
	$vehi = $_GET['vehi'];
	// $offset = $_GET['offset'];
	$access_key = "12345";

	if ($access_key_received == $access_key) {
		// get all category data from category table
		$sql_query = "SELECT TIME AS TIME, device_id AS device_id, speed AS speed, CASE WHEN speed > 0 THEN TIMESTAMPDIFF( MINUTE, lag(TIME, 1) OVER( ORDER BY TIME ), TIME ) ELSE 0 END AS VALUE FROM positions_log WHERE TIME >= CURDATE() and device_id = $vehi order by time;";
		$sql_query2 = "SELECT TIME AS TIME, device_id AS device_id, speed AS speed,power, CASE WHEN speed = 0 and power = 1 THEN TIMESTAMPDIFF( MINUTE, lag(TIME, 1) OVER( ORDER BY TIME ), TIME ) ELSE 0 END AS VALUE FROM positions_log WHERE TIME >= CURDATE() and device_id = $vehi order by time;";
		$sql_query3 = "SELECT TIME AS TIME, device_id AS device_id, speed AS speed,power, CASE WHEN speed = 0 THEN TIMESTAMPDIFF( MINUTE, lag(TIME, 1) OVER( ORDER BY TIME ), TIME ) ELSE 0 END AS VALUE FROM positions_log WHERE TIME >= CURDATE() and device_id = $vehi order by time;";

		$result = $connect->query($sql_query) or die("Error :" . mysqli_error());
		$result2 = $connect->query($sql_query2) or die("Error :" . mysqli_error());
		$result3 = $connect->query($sql_query3) or die("Error :" . mysqli_error());
		$val = 0;
		$val2 = 0;
		$val3 = 0;
		// $users = array();
		while ($user = $result->fetch_assoc()) {
			$val += $user['VALUE'];
		}
		while ($user = $result2->fetch_assoc()) {
			$val2 += $user['VALUE'];
		}
		while ($user = $result3->fetch_assoc()) {
			$val3 += $user['VALUE'];
		}
		$post_data = array(
			'run' => $val,
			'idle' => $val2,
			'stop' => $val3
		);

		$post_data = json_encode($post_data);
		// create json output
		// $output = json_encode($users);
	} else {
		die('accesskey is incorrect.');
	}
} else {
	die('accesskey is required.');
}

//Output the output.
echo $post_data;

// include_once('../includes/close_database.php'); 
?>