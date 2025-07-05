<?php
//fetch.php  
include("../config.php");


$access_key = '03201232927';

$pass = $_GET["key"];
$order_id = $_GET["order_id"];
if ($pass != '') {
    if ($pass == $access_key) {
        $sql_query1 = "SELECT od.*,us.name FROM order_detail_log as od
        join users as us on us.id=od.created_by
        where od.order_id=$order_id;";

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