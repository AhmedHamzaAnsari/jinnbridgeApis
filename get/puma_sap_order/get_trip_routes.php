<?php
//fetch.php  
include("../../config.php");


$access_key = '03201232927';

$pass = $_GET["key"];
// $id=$_GET["id"];
if ($pass != '') {
    if ($pass == $access_key) {
        $vehicle_id = $_GET["vehicle_id"];
        $start_time = $_GET["start_time"];
        $end_time = $_GET["end_time"];


        $sql_query1 = "SELECT * FROM positions_log where device_id='$vehicle_id' and time>='$start_time' and time<='$end_time' and latitude!=0 and latitude!=0 order by time asc;";

        $result1 = $db->query($sql_query1) or die("Error :" . mysqli_error());

        $thread = array();
        while ($user = $result1->fetch_assoc()) {
            $thread[] = $user;
        }
        echo json_encode($thread);

    } else {
        echo 'Wrong Key...';
    }

} else {
    echo 'Key is Required';
}

?>