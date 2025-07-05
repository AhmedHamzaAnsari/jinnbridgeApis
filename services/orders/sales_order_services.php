<?php
//fetch.php  
ini_set('max_execution_time', '0');
$url1 = $_SERVER['REQUEST_URI'];
header("Refresh: 20; URL=$url1");
include("../../config.php");
set_time_limit(5000);

$access_key = '03201232927';
$pass = isset($_GET["key"]) ? $_GET["key"] : '';
$date = date('Y-m-d H:i:s');

if ($pass != '') {
    if ($pass == $access_key) {
        $sql_query1 = "SELECT * FROM order_main WHERE live_order_status = 0 ORDER BY id DESC";
        $result1 = $db->query($sql_query1) or die("Error in SQL query: " . $db->error);

        while ($user = $result1->fetch_assoc()) {
            $id = $user['id'];
            $product_json = $user['product_json'];
            $type = $user['type'];
            $dealer_sap = $user['dealer_sap'];

            update_sale_id($product_json, $id, $db, $type, $dealer_sap);
        }
    } else {
        echo 'Wrong Key...';
    }
} else {
    echo 'Key is Required';
}

function update_sale_id($data, $order_id, $db, $type, $dealer_sap)
{
    $curl = curl_init();

    curl_setopt_array(
        $curl,
        array(
            CURLOPT_URL => 'http://110.38.69.114:5003/jinnbridgeApis/create/order/get_sales_order_id.php?data=' . $data . '&order_id=' . $order_id . '&type=' . $type . '&dealer_sap=' . $dealer_sap . '',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_SSL_VERIFYPEER => false, // Set to false to skip SSL verification
            CURLOPT_SSL_VERIFYHOST => false, // Set to false to skip SSL verification
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => array(
                'Cookie: PHPSESSID=qcb4jg2do7fvnq6nk8dv4fg9b3'
            ),
        )
    );

    $response = curl_exec($curl);

    if ($response !== false) {
        $query = "UPDATE `order_main` SET `SaleOrder`='$response', live_order_status='1' WHERE id=$order_id";
        if (mysqli_query($db, $query)) {
            echo 'Sales number Updated <br>';
        } else {
            echo 'Error updating sales number: ' . mysqli_error($db) . '<br>' . $query;
        }
    } else {
        echo 'Error executing Curl request: ' . curl_error($curl);
    }

    curl_close($curl);
}

echo 'Service Last Run => ' . date('Y-m-d H:i:s');
?>