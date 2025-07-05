<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: *");
ini_set('max_execution_time', -1);
date_default_timezone_set("Asia/Karachi");
include("../config_apis.php");
if (isset($_GET['accesskey'])) {
    $access_key_received = $_GET['accesskey'];
    $vehi = $_GET['vehi'];
    $access_key = "12345";
    if ($access_key_received == $access_key) {
        // get all category data from category table
        $sql_query = "SELECT *, CASE WHEN ignition = 'OFF' and speed =0 and TIMESTAMPDIFF(HOUR, time, curdate()) < 24 THEN 'red' WHEN ignition = 'ON' and speed <=60 and speed > 0 and TIMESTAMPDIFF(HOUR, time, curdate()) < 24 THEN '#1D738D' WHEN ignition = 'ON' and speed =0 and TIMESTAMPDIFF(HOUR, time, curdate()) < 24 THEN 'yellow' WHEN ignition = 'ON' and speed >60 and TIMESTAMPDIFF(HOUR, time, curdate()) < 24 THEN '#e62e2d' WHEN TIMESTAMPDIFF(HOUR, time, curdate()) > 24 THEN '#c34c9c' ELSE 'either' END as color FROM devicesnew where id IN ($vehi)";

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