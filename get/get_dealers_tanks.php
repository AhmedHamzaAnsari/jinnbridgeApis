<?php
//fetch.php  
include ("../config.php");


$access_key = '03201232927';

$pass = $_GET["key"];
if ($pass != '') {
    $dealer_id = $_GET["dealer_id"];
    if ($pass == $access_key) {
        $sql_query1 = "SELECT dl.*,ap.name ,
        (SELECT previous_dip FROM dealer_dip_log where tank_id=dl.id order by id desc limit 1) as previous_dips,
        (SELECT current_dip FROM dealer_dip_log where tank_id=dl.id order by id desc limit 1) as current_dips,
        (SELECT old_reading FROM dealer_dip_log where tank_id=dl.id order by id desc limit 1) as old_readings,
        (SELECT current_reading FROM dealer_dip_log where tank_id=dl.id order by id desc limit 1) as current_readings,
        (SELECT created_at FROM dealer_dip_log where tank_id=dl.id order by id desc limit 1) as last_dip_date
        FROM dealers_lorries as  dl 
        join dealers_products as dp on dp.id=dl.product
		join all_products as ap on ap.name=dp.name
        where dl.dealer_id='$dealer_id'";

        $result1 = $db->query($sql_query1) or die("Error :" . mysqli_error($db));

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