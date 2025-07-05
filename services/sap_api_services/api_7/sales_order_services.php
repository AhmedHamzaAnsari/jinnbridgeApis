<?php
// fetch.php  
include("../../../config.php");
set_time_limit(5000);

ini_set('max_execution_time', '0');
$url1 = $_SERVER['REQUEST_URI'];
header("Refresh: 200; URL=$url1");

$access_key = '03201232927';
$pass = isset($_GET["key"]) ? $_GET["key"] : '';
$date = date('Y-m-d H:i:s');

if ($pass != '') {
    if ($pass == $access_key) {
        $sql = "SELECT om.*, od.quantity, od.rate, od.product_type, dl.sap_no as dealer_sap, dl.id as dealer_id 
                   FROM order_main om 
                   JOIN order_detail as od ON od.main_id = om.id 
                   JOIN dealers as dl ON dl.sap_no = om.dealer_sap 
                   WHERE om.live_order_status = 0 
                   ORDER BY om.id DESC";

        // echo $sql;

        $result = mysqli_query($db, $sql);
        $row = mysqli_fetch_array($result);

        $count = mysqli_num_rows($result);
        if ($count > 0) {
            $sessionId = login();
            if ($sessionId) {
                get_orders($db, $sessionId); // Pass sessionId to get_orders
                logout();
            } else {
                echo "Session ID not found.";
            }

        } else {
            echo json_encode(["message" => "No orders found."]);

        }
    } else {
        echo 'Wrong Key...';
    }
} else {
    echo 'Key is Required';
}

function get_orders($db, $sessionId)
{
    $sql_query1 = "SELECT om.*, od.quantity, od.rate, od.product_type, dl.sap_no as dealer_sap, dl.id as dealer_id 
                   FROM order_main om 
                   JOIN order_detail as od ON od.main_id = om.id 
                   JOIN dealers as dl ON dl.sap_no = om.dealer_sap 
                   WHERE om.live_order_status = 0 
                   ORDER BY om.id DESC";
    $result1 = $db->query($sql_query1) or die("Error in SQL query: " . $db->error);

    if ($result1->num_rows > 0) {
        $user = $result1->fetch_assoc(); // Fetch the first order only
        $system_order_id = $user['id'];
        $dealer_sap = $user['dealer_sap'];
        $comment = $user['comment'];

        $orderData = [
            "CardCode" => $dealer_sap,
            "DocDueDate" => date('Y-m-d'), // Set current date
            "Comments" => $comment,
            "DocumentLines" => [],
            "U_RateType" => "EXDR"
        ];

        // Fetch product details for the first order
        $sql_query2 = "SELECT bb.* FROM portalflow.dealers_products_bom_list as bb
                       JOIN dealers_products as dp ON dp.id = bb.main_id 
                       WHERE dp.dealer_id = {$user['dealer_id']} AND dp.id = {$user['product_type']}";
        $result2 = $db->query($sql_query2) or die("Error in SQL query: " . $db->error);

        while ($user2 = $result2->fetch_assoc()) {
            // Adding items to DocumentLines
            $o_qty = $user['quantity'];
            if ($user2['U_NegativeQty'] == 'Y') {
                $o_qty = -$user['quantity'];
            } else {
                $o_qty = $user['quantity'];

            }
            $orderData["DocumentLines"][] = [
                "ItemCode" => $user2['line_item_sap'],
                "Quantity" => (int) $o_qty, // Use the quantity from the main order
                "UnitPrice" => (float) $user2['unit_price'],
                "WarehouseCode" => "GRW-1", // Hardcoded as per your example
                "TaxOnly" => $user2['U_TaxOnly'] // Assuming this field exists in your query result
            ];
        }

        // Output the final JSON structure as a single object
        // header('Content-Type: application/json'); // Set the content type to JSON
        // echo json_encode($orderData, JSON_PRETTY_PRINT); // Use JSON_PRETTY_PRINT for better readability
        create_dealer_order_in_sap($orderData, $system_order_id, $db, $dealer_sap, $sessionId); // Send each order
    } else {
        // Handle the case where no orders are found
        echo json_encode(["message" => "No orders found."]);
    }

    // You may want to return or log $ordersData here if needed
}

function create_dealer_order_in_sap($data, $order_id, $db, $dealer_sap, $sessionId)
{
    $curl = curl_init();

    curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://asco.flowpetroleum.com.pk:50000/b1s/v1/Orders',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_SSL_VERIFYHOST => false,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => json_encode($data),
        CURLOPT_HTTPHEADER => array(
            'Content-Type: application/json',
            'Cookie: B1SESSION=' . $sessionId // Use the session ID dynamically
        ),
    ));

    $response = curl_exec($curl);
    curl_close($curl);

    $dataArray = json_decode($response, true);

    // print_r($dataArray);

    // Check if decoding was successful and if 'DocNum' exists
    if ($dataArray && isset($dataArray['DocNum'])) {
        $docNum = $dataArray['DocNum'];
        echo "Document Number: " . $docNum;

        if ($docNum != '') {
            $query = "UPDATE `order_main` SET `SaleOrder` = '$docNum', live_order_status = '1' WHERE id = $order_id";
            if ($db->query($query)) {
                echo 'Sales number Updated<br>';

                $log = "INSERT INTO `order_main_push_sap_log`
                (`doc_no`, `api_response`, `created_at`, `created_by`)
                VALUES
                ('$docNum', '$response', NOW(), '1');";

                // Execute the query
                if ($db->query($log)) {
                    // Optional: You can perform some actions here if needed after successful insert
                } else {
                    // Log the error
                    echo "Error: " . $db->error;
                }

            } else {
                echo 'Error updating sales number: ' . $db->error . '<br>' . $query;
            }
        }
    } else {
        echo "Document Number not found or Curl request failed.";
    }
}

function login()
{
    $curl = curl_init();

    curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://asco.flowpetroleum.com.pk:50000/b1s/v1/Login',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_SSL_VERIFYHOST => false,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => json_encode(array(
            "CompanyDB" => "ASCO_PROD",
            "Password" => "1234",
            "UserName" => "manager"
        )),
        CURLOPT_HTTPHEADER => array(
            'Content-Type: application/json',
        ),
    ));

    $response = curl_exec($curl);
    curl_close($curl);

    $dataArray = json_decode($response, true);
    return $dataArray['SessionId'] ?? null;
}

function logout()
{
    $curl = curl_init();

    curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://asco.flowpetroleum.com.pk:50000/b1s/v1/Logout',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_SSL_VERIFYHOST => false,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_HTTPHEADER => array(
            'Cookie: ROUTEID=.node2'
        ),
    ));

    $response = curl_exec($curl);
    curl_close($curl);
    echo $response;
}

echo 'Service Last Run => ' . date('Y-m-d H:i:s');
?>