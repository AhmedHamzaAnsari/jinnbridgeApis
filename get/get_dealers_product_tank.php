<?php
//fetch.php  
include("../config.php");


$access_key = '03201232927';

$pass = $_GET["key"];
if ($pass != '') {
    $dealer_id = $_GET["dealer_id"];
    $product = $_GET["product"];
    if ($pass == $access_key) {
        $sql_query1 = "SELECT dl.* FROM dealers_lorries as dl 
        join dealers_products as dp on dp.id=dl.product where dl.dealer_id=$dealer_id and dp.id='$product'";

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