<?php
//fetch.php  
include("../config.php");


$access_key = '03201232927';

$pass = $_GET["key"];
if ($pass != '') {
    $dealer_id = $_GET["dealer_id"];
    if ($pass == $access_key) {
        $sql_query1 = "SELECT dz.*,ds.name as dispenser_name,
        GROUP_CONCAT(ap.name SEPARATOR ', ') AS product_name,
        GROUP_CONCAT(dl.lorry_no SEPARATOR ',') AS tank_name,
        GROUP_CONCAT(dz.name SEPARATOR ', ') AS nozel_name,
        GROUP_CONCAT(dz.last_reading SEPARATOR ', ') AS nozels_last_readings,
        GROUP_CONCAT(dz.last_date SEPARATOR ', ') AS nozels_last_dates
        FROM dealers_nozzel dz
        join dealers_products as dp on dp.id=dz.products
        join dealers_dispenser as ds on ds.id=dz.dispenser_id
        join dealers_lorries as dl on dl.id=dz.tank_id
        join all_products as ap on ap.name=dp.name
        where dz.dealer_id=$dealer_id group by dz.dispenser_id order by dz.id;";

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