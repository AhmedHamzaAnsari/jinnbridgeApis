<?php
//fetch.php  
include("../config.php");


$access_key = '03201232927';

$pass = $_GET["key"];
$product_id=$_GET["product_id"];
$dealer_id=$_GET["dealer_id"];
if ($pass != '') {
    if ($pass == $access_key) {
        $sql_query1 = "SELECT pl.*,pp.name FROM dealer_nozel_price_log as pl 
        join dealers_products as pp on pp.id=pl.product_id where pl.dealer_id = $dealer_id and pl.product_id = $product_id order by pl.id desc";

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