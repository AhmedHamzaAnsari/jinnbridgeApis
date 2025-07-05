<?php
//fetch.php  
include("../../config.php");


$access_key = '03201232927';

$pass = $_GET["key"];
// $id=$_GET["id"];
if ($pass != '') {
    if ($pass == $access_key) {
        $from = $_GET["from"];
        $to = $_GET["to"];


        $sql_query1 = "SELECT si.*,dc.id as vehicle_id,dc.name as vehicle_name,IF(dc.name IS NOT NULL, 'With-Tracker', 'Without-Tracker') AS tracker_status,oi.carrier_desc as depot_name,dc.id as uniqueId,'' as end_time,pp.name as product_name FROM order_info as oi
        left join devicesnew as dc on TRIM(SUBSTRING_INDEX(dc.name, ' ', 1))=oi.vehicle 
        join order_sales_invoice as si on si.order_no=oi.order_no
        join all_products as pp on pp.sap_no=oi.item
        where oi.created_at>='$from' and oi.created_at<='$to' group by oi.order_no order by oi.id desc";

        $result1 = $db->query($sql_query1) or die("Error :" . mysqli_error());

        $thread = [];
        while ($user = $result1->fetch_assoc()) {
            $thread[] = $user;
        }
        // echo json_encode($thread);

        $thread = utf8ize($thread);
        $json = json_encode($thread, JSON_PRETTY_PRINT);

        if ($json === false) {
            echo json_encode(["error" => "JSON encoding failed", "details" => json_last_error_msg()]);
        } else {
            echo $json;
        }

    } else {
        echo 'Wrong Key...';
    }

} else {
    echo 'Key is Required';
}

function utf8ize($data)
{
    if (is_array($data)) {
        return array_map('utf8ize', $data);
    } elseif (is_string($data)) {
        return mb_convert_encoding($data, 'UTF-8', 'UTF-8');
    }
    return $data;
}

?>