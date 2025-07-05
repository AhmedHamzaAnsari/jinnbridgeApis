<?php
//fetch.php  
include("../config.php");


$access_key = '03201232927';

$pass = $_GET["key"];
if ($pass != '') {
    $dealer_id = $_GET["dealer_id"];
    if ($pass == $access_key) {
        $sql_query1 = "SELECT dz.*,dp.name as product_name,ds.name as dispenser_name,dl.lorry_no as tank_name,
        (SELECT new_reading FROM dealer_reconcilation where nozle_id=dz.id order by id desc limit 1) as new_reading 
        FROM dealers_nozzel dz 
        join dealers_products as dp on dp.id=dz.products
        join dealers_dispenser as ds on ds.id=dz.dispenser_id
        join dealers_lorries as dl on dl.id=dz.tank_id 
        where dz.dealer_id=$dealer_id order by dz.id;";

        $result1 = $db->query($sql_query1) or die("Error :" . mysqli_error($db));

        $thread = array();
        while ($user = $result1->fetch_assoc()) {
            $thread[] = $user;
        }
        echo json_encode($thread);

    } else {
        echo 'Wrong Key...';
    }

} 
else 
{
    echo 'Key is Required';
}


?>