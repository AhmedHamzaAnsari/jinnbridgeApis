<?php
//fetch.php  
include("../config.php");


$access_key = '03201232927';

$pass = $_GET["key"];
$id = $_GET['dealer_id'];
if ($pass != '') {
    if ($pass == $access_key) {
        $sql_query1 = "SELECT dt.*,pp.name,(SELECT sum(amount)
        FROM order_detail
        WHERE YEAR(date) = YEAR(CURDATE())
          AND MONTH(date) = MONTH(CURDATE()) and product_type=dt.product_id and cus_id=dt.dealer_id) as total_sum_target FROM dealers_monthly_targets as dt 
        join dealers_products as pp on pp.id=dt.product_id 
        where dt.dealer_id=$id order by dt.id desc";

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