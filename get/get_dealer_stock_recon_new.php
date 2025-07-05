<?php
//fetch.php  
include("../config.php");


$access_key = '03201232927';

$pass = $_GET["key"];
if ($pass != '') {
    $dealer_id = $_GET["dealer_id"];
    $task_id = $_GET["task_id"];
    if ($pass == $access_key) {
        $sql_query1 = "SELECT rr.*,ap.name as product_name,dc.name as dealer_name FROM dealer_stock_recon_new as rr
        join dealers_products as dp on dp.id=rr.product_id
        join all_products as ap on ap.name=dp.name
        join dealers as dc on dc.id=rr.dealer_id where rr.task_id=$task_id and rr.dealer_id=$dealer_id group by rr.product_id;";

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