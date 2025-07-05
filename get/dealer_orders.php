<?php
//fetch.php  
include("../config.php");


$access_key = '03201232927';

$pass = $_GET["key"];
$id = $_GET["id"];
if ($pass != '') {
    if ($pass == $access_key) {
        $sql_query1 = "SELECT om.*,geo.consignee_name,dl.name ,od.quantity,od.rate,
        CASE
        WHEN om.status = 0 THEN 'Pending'
        WHEN om.status = 1 THEN 'Start'
        WHEN om.status = 3 THEN 'Complete'
        WHEN om.status = 5 THEN 'Pending'
        END AS current_status
        FROM order_main as om 
        join order_detail as od on od.main_id=om.id
        left join geofenceing as geo on geo.id=om.depot
        join dealers as dl on dl.id=om.created_by
        where om.created_by='$id' and om.total_amount>0 order by om.id desc";

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