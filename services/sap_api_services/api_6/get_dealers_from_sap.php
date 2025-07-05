<?php
// fetch.php  
include("../../../config.php");
set_time_limit(5000);
ini_set('max_execution_time', '0');
$url1 = $_SERVER['REQUEST_URI'];
header("Refresh: 30; URL=$url1");

$access_key = '03201232927';

$pass = $_GET["key"] ?? ''; // Added null coalescing to avoid undefined index notice
$date = 'Dealers Sap Order : ' . date('Y-m-d H:i:s');
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
    $curl = curl_init();
    curl_setopt_array($curl, array(
        CURLOPT_URL => 'http://localhost:8080/api_server/get/get_dealers_orders.php?sap=' . urlencode($sap_no),
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
        $order_no = $item['Order Number'] ?? '';
        $dealer_sap = $item['Customer Code'] ?? '';
        $dealer_name = $item['Customer Name'] ?? '';
        $order_date = $item['Order Date'] ?? '';
        $product_code = $item['Product Code'] ?? '';
        $product_name = $item['Product Name'] ?? '';
        $total_order_qty = $item['Total Order Quantity'] ?? 0;
        $total_item_amount = $item['Total Item Amount'] ?? 0;
        $total_tax_amount = $item['Total Tax Amount'] ?? 0;
        $freight_charges = $item['Freight Charge'] ?? 0;
        $line_total = $item['LINETOTAL'] ?? 0;
        $total_order_amount_with_tax_freigth = $item['Total Order Amount with Tax and Freight'] ?? 0;
        $status = $item['Order Status'] ?? '';
        $depots = $item['depots'] ?? '';
        $item_data = json_encode($item['item_data'] ?? []); // Properly encode item_data to JSON
        $datetime = date('Y-m-d H:i:s');

        // Check for duplicate entry
        $check_query = "SELECT * FROM `portalflow`.`dealers_sap_orders` WHERE `sap_order_no` = '$order_no' AND `dealer_id` = '$d_id'";
        $check_result = $db->query($check_query);

        if ($check_result->num_rows > 0) {
            echo "Duplicate entry for SAP Order No: $order_no, skipping insert.<br>";
            continue; // Skip the current iteration if a duplicate is found
        }

        // Prepare the SQL query
        $main_orders = "INSERT INTO `portalflow`.`dealers_sap_orders`
        (`sap_order_no`, `dealer_id`, `dealer_sap`, `dealer_name`, `order_date`, 
        `product_code`, `product_name`, `total_order_qty`, `total_item_amount`, 
        `total_tax_amount`, `freight_charges`, `overall_total_amount`, 
        `line_total`, `depot`, `bom_data`, `sap_status`, `created_at`, `created_by`)
        VALUES
        ('$order_no', '$d_id', '$dealer_sap', '$dealer_name', '$order_date', 
        '$product_code', '$product_name', '$total_order_qty', '$total_item_amount', 
        '$total_tax_amount', '$freight_charges', 
        '$total_order_amount_with_tax_freigth', '$line_total', '$depots', 
        '$item_data', '$status', '$datetime', '$user_id');";
        
        if (mysqli_query($db, $main_orders)) {
            $system_order_id = mysqli_insert_id($db); // Use correct mysqli function
            create_bom_data($db, json_decode($item_data, true), $system_order_id, $d_id, $user_id); // Decode item_data back to array
        } else {
            echo "Error: " . $db->error . "<br>" . $main_orders;
        }
    }
}

function create_bom_data($db, $data, $system_order_id, $dealer_id, $user_id) {
    $datetime = date('Y-m-d H:i:s');
    foreach ($data as $item) {
        // Extract values from item data
        $order_no = $item['Order Number'] ?? '';
        $dealer_sap = $item['Customer Code'] ?? '';
        $dealer_name = $item['Customer Name'] ?? '';
        $order_date = $item['Order Date'] ?? '';
        $order_time = $item['Order Time'] ?? ''; // Assumed to be part of item_data
        $product_code = $item['Product Code'] ?? '';
        $product_name = $item['Product Name'] ?? '';
        $total_order_qty = $item['Total Order Quantity'] ?? 0;
        $total_item_amount = $item['Total Item Amount'] ?? 0;
        $total_tax_amount = $item['Total Tax Amount'] ?? 0;
        $freight_charges = $item['Freight Charge'] ?? 0;
        $line_total = $item['LINETOTAL'] ?? 0;
        $total_order_amount_with_tax_freigth = $item['Total Order Amount with Tax and Freight'] ?? 0;
        $status = $item['status'] ?? '';
        $depots = $item['depots'] ?? '';

        // Check for duplicate entry
        $bom_check_query = "SELECT * FROM `portalflow`.`dealers_sap_orders_bom_list` WHERE `sap_order_no` = '$order_no' AND `main_id` = '$system_order_id'";
        $bom_check_result = $db->query($bom_check_query);

        if ($bom_check_result->num_rows > 0) {
            echo "Duplicate entry for BOM List SAP Order No: $order_no, skipping insert.<br>";
            continue; // Skip the current iteration if a duplicate is found
        }

        // Prepare the BOM SQL query
        $bom_list = "INSERT INTO `portalflow`.`dealers_sap_orders_bom_list`
        (`main_id`, `dealer_id`, `sap_order_no`, `dealer_sap`, `dealer_name`, 
        `order_date`, `order_time`, `product_code`, `product_name`, 
        `total_order_qty`, `total_item_amount`, `total_tax_amount`, 
        `freight_charges`, `overall_total_amount`, `line_total`, 
        `created_at`, `created_by`)
        VALUES
        ('$system_order_id', '$dealer_id', '$order_no', '$dealer_sap', '$dealer_name', 
        '$order_date', '$order_time', '$product_code', '$product_name', 
        '$total_order_qty', '$total_item_amount', '$total_tax_amount', 
        '$freight_charges', '$total_order_amount_with_tax_freigth', 
        '$line_total', '$datetime', '$user_id');";

        if (!$db->query($bom_list)) {
            echo "Error: " . $db->error . "<br>" . $bom_list;
        }
    }
}

echo date('Y-m-d H:i:s');
?>
