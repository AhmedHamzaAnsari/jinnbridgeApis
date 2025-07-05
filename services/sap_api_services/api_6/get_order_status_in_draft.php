<?php
// fetch.php
include("../../../config.php");
set_time_limit(5000);
ini_set('max_execution_time', '0');
$url1 = $_SERVER['REQUEST_URI'];
header("Refresh: 30; URL=$url1");

$access_key = '03201232927';

$pass = $_GET["key"] ?? ''; // Added null coalescing to avoid undefined index notice
$date = 'Dealers Draft Orders : ' . date('Y-m-d H:i:s');
echo $date . '<br>';
$user_id = 1;

if ($pass !== '') {
    if ($pass === $access_key) {
        $sql_query1 = "SELECT om.*, od.quantity, od.rate, od.product_type, dl.sap_no as dealer_sap, dl.id as dealer_id 
        FROM order_main om 
        JOIN order_detail as od ON od.main_id = om.id 
        JOIN dealers as dl ON dl.sap_no = om.dealer_sap 
        WHERE om.live_draft_status = 1 and om.live_order_status = 0
        ORDER BY om.id DESC;";
        $result1 = $db->query($sql_query1) or die("Error: " . $db->error);

        while ($user = $result1->fetch_assoc()) {
            $order_id = $user['id'];
            $draft_no = $user['draft_no'];

            // Call function to get dealer orders
            get_dealers_orders($db, $order_id, $draft_no, $user_id);
        }
    } else {
        echo 'Wrong Key...';
    }
} else {
    echo 'Key is Required';
}

function get_dealers_orders($db, $order_id, $draft_no, $user_id)
{
    $curl = curl_init();
    curl_setopt_array($curl, [
        CURLOPT_URL => 'http://localhost:8080/api_server/get/get_draft_orders.php?draft_no=' . urlencode($draft_no),
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'GET',
    ]);

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
        $DraftID = $item['DraftID'] ?? '';
        $DraftStatus = $item['DraftStatus'] ?? '';
        $WddStatus = $item['WddStatus'] ?? '';
        $StatusDescription = $item['StatusDescription'] ?? '';
        $OrderID = $item['OrderID'] ?? '';
        $OrderNumber = $item['OrderNumber'] ?? '';

        $datetime = date('Y-m-d H:i:s');

        // Evaluate status based on conditions
        $statusMessage = evaluateOrderStatus($WddStatus, $OrderNumber, $DraftStatus);
        echo "DraftID: $DraftID, Status: $statusMessage<br>";
        $orderStatus = $statusMessage;
        $currentStatusQuery = "SELECT `status_value`, `SaleOrder` FROM `order_main` WHERE id = $order_id";
        $currentStatusResult = $db->query($currentStatusQuery);

        if ($currentStatusResult && $currentStatusRow = $currentStatusResult->fetch_assoc()) {
            $currentStatus = $currentStatusRow['status_value'];
            $currentSaleOrder = $currentStatusRow['SaleOrder'];

            // Check if the status or SaleOrder needs to be updated
            if ($orderStatus !== $currentStatus) {
                if ($orderStatus == "Order generated.") {
                    $query = "UPDATE `order_main` 
                              SET `SaleOrder` = '$OrderNumber', 
                                  `live_order_status` = '1',
                                  `status_value` = 'Order generated.' 
                              WHERE id = $order_id";

                    $record = "INSERT INTO `portalflow`.`order_dpt_sap`
                    (`system_order_id`,
                    `type`,
                    `sap_id`,
                    `status`,
                    `created_at`,
                    `updated_at`)
                    VALUES
                    ('$order_id',
                    'Depot',
                    '$OrderNumber',
                    '0',
                    NOW(),
                    '1');";
                    if (!mysqli_query($db, $record)) {
                        echo "Error: " . mysqli_error($db);
                    }
                } else {
                    // Update only the status value for other statuses
                    $query = "UPDATE `order_main` 
                              SET `status_value` = '$orderStatus' 
                              WHERE id = $order_id";
                }

                // Execute the update query
                if ($db->query($query)) {
                    echo 'Sales number/status Updated<br>';

                    // Log the response
                    // $log = "INSERT INTO `order_main_push_sap_log`
                    //         (`doc_no`, `api_response`, `created_at`, `created_by`)
                    //         VALUES
                    //         ('$OrderNumber', '$response', NOW(), '1');";

                    // if ($db->query($log)) {
                    //     // Optionally, actions can be taken here if needed after successful log insert
                    // } else {
                    //     echo "Error logging response: " . $db->error;
                    // }

                } else {
                    echo 'Error updating sales number/status: ' . $db->error . '<br>' . $query;
                }
            } else {
                echo 'No status change detected. No update performed.<br>';
            }
        } else {
            echo 'Error fetching current status: ' . $db->error;
        }

        // Check for duplicate entry (if applicable)
        // Additional logic can go here
    }
}

function evaluateOrderStatus($WddStatus, $orderNumber, $draftStatus)
{
    if ($WddStatus === 'W') {
        return "Document is in approval process.";
    }

    if ($WddStatus == '-' && $orderNumber == "") {
        return "Order is in draft.";
    }

    if ($WddStatus == 'Y') {
        return "Document is approved but order not generated.";
    }

    if ($WddStatus == '-' && $orderNumber != "") {
        return "Order generated.";
    }

    if ($WddStatus == 'N') {
        return "Approval rejected.";
    }

    if ($WddStatus == 'N' && $draftStatus == 'C') {
        return "Approval rejected and permanently closed.";
    }

    if ($draftStatus == 'C') {
        return "Permanently closed.";
    }

    if ($draftStatus == 'O') {
        return "Draft is open.";
    }

    if ($draftStatus == 'C' && $orderNumber != "") {
        return "Executed data - Draft closed with order number.";
    }

    return "Status not recognized.";
}

echo date('Y-m-d H:i:s');
?>