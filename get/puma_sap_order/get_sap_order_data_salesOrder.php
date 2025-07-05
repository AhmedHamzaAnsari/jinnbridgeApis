<?php
//fetch.php  
include("../../config.php");


$access_key = '03201232927';

$pass = $_GET["key"];
// $id=$_GET["id"];
if ($pass != '') {
    if ($pass == $access_key) {
        $sales_order = $_GET["sales_order"];


        $sql_query1 = "SELECT om.*,dc.name as vehiclenames,geo.consignee_name as depot_name,dc.id as uniqueId,'' as end_time,dc.name as vehiclename ,dc.lat,dc.lng,IF(dc.name IS NOT NULL, 'With-Tracker', 'Without-Tracker') AS tracker_status  FROM order_main as om
        join devicesnew as dc on dc.id=om.tl_no
        left join geofenceing as geo on geo.id=om.depot
        where om.id=$sales_order";

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