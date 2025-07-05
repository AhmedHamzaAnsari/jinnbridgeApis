<?php
// fetch.php  
include("../../../config.php");
set_time_limit(5000);
ini_set('max_execution_time', '0');
$url1 = $_SERVER['REQUEST_URI'];
header("Refresh: 30; URL=$url1");

$access_key = '03201232927';
$pass = $_GET["key"] ?? ''; // Added null coalescing to avoid undefined index notice
$date = 'Dealers Orders_dispatches : ' . date('Y-m-d H:i:s');
echo $date . '<br>';
$user_id = 1;

if ($pass !== '') {
    if ($pass === $access_key) {
        $sql_query1 = "SELECT * FROM dealers WHERE privilege='Dealer' AND indent_price=1;";
        $result1 = $db->query($sql_query1) or die("Error: " . $db->error);

        while ($user = $result1->fetch_assoc()) {
            $dealers_id = $user['id'];
            $sap = $user['sap_no'];

            // Call function to get dealer orders
            get_dealers_orders($db, $sap, $dealers_id, $user_id);
        }
    } else {
        echo 'Wrong Key...';
    }
} else {
    echo 'Key is Required';
}

function get_dealers_orders($db, $sap_no, $d_id, $user_id) {
    // echo $sap_no;
    $curl = curl_init();
    curl_setopt_array($curl, array(
        CURLOPT_URL => 'http://localhost:8080/api_server/get/get_orders_dispatches.php?sap_code=' . urlencode($sap_no),
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'GET',
    ));

    $response = curl_exec($curl);
    if (curl_errno($curl)) {
        echo 'Curl error: ' . curl_error($curl);
        curl_close($curl);
        return; // Exit the function if curl fails
    }

    curl_close($curl);

    $response_data = json_decode($response, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        echo 'JSON decode error: ' . json_last_error_msg();
        return; // Exit the function if JSON decoding fails
    }

    // Process each item in the response
    foreach ($response_data as $item) {
        // Extract values with null coalescing to handle missing keys
        $main_order_no = $item['Main Order Number'] ?? '';
        $dispatch_order_no = $item['Dispatch Order Number'] ?? '';
        $dealer_sap = $item['Customer Code'] ?? '';
        $dealer_name = $item['Customer Name'] ?? '';
        $order_date = $item['Order Date'] ?? '';
        $dispatch_date = $item['Dispatch Date'] ?? '';
        $dispatch_time = $item['Dispatch Time'] ?? '';
        $depot = $item['Dispatch Warehouse'] ?? '';
        $vehicle = $item['Vehicle Number'] ?? '';
        $order_qty = $item['Order Quantity'] ?? 0;
        $total_item_amount = $item['Order Amount without Tax'] ?? 0;
        $total_item_amount_with_tax = $item['Order Amount with Tax'] ?? 0;
        $datetime = date('Y-m-d H:i:s');

        // Skip this item if the Dispatch Order Number is empty
        if (empty($dispatch_order_no)) {
            echo "Dispatch Order No is empty, skipping insert.<br>";
            continue;
        }

        // Check for duplicate entry
        $check_query = "SELECT * FROM `portalflow`.`dealers_sap_order_info` WHERE `invoice` = '$dispatch_order_no' AND `customer_id` = '$dealer_sap'";
        $check_result = $db->query($check_query);

        if ($check_result->num_rows > 0) {
            echo "Duplicate entry for SAP Order No: $dispatch_order_no, skipping insert.<br>";
            continue; // Skip the current iteration if a duplicate is found
        }

        // Prepare the SQL query directly for insertion
        $main_orders = "INSERT INTO `portalflow`.`dealers_sap_order_info`
        (`customer_id`, `customer_name`, `order_no`, `order_date`, `invoice`, `item`, `quantity`, `dispatch_date`, `dispatch_time`, `vehicle`, `depot`, `total_amount`, `total_amount_with_tax`)
        VALUES
        ('$dealer_sap', '$dealer_name', '$main_order_no', '$order_date', '$dispatch_order_no', '', '$order_qty', '$dispatch_date', '$dispatch_time', '$vehicle', '$depot', '$total_item_amount', '$total_item_amount_with_tax');";

        // Execute the query
        if (mysqli_query($db, $main_orders)) {
            $system_order_id = mysqli_insert_id($db); // Use correct mysqli function
            echo "Dispatch created .: " . $dealer_name . "<br>";
        } else {
            echo "Error: " . $db->error . "<br>" . $main_orders;
        }
    }
}

echo date('Y-m-d H:i:s');
?>
