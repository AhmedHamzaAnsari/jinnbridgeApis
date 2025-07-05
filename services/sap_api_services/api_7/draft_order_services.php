<?php
// fetch.php  
include("../../../config.php");
set_time_limit(5000);

ini_set('max_execution_time', '0');
$url1 = $_SERVER['REQUEST_URI'];
header("Refresh: 30; URL=$url1");

$access_key = '03201232927';
$pass = isset($_GET["key"]) ? $_GET["key"] : '';
$date = date('Y-m-d H:i:s');

if (empty($pass)) {
    echo 'Key is Required';
    exit;
}

if ($pass !== $access_key) {
    echo 'Wrong Key...';
    exit;
}

if (!$db) {
    die("Database connection failed: " . mysqli_connect_error());
}

$sql = "SELECT om.*, od.quantity, od.rate, od.product_type, dl.sap_no as dealer_sap, dl.id as dealer_id 
        FROM order_main om 
        JOIN order_detail as od ON od.main_id = om.id 
        JOIN dealers as dl ON dl.sap_no = om.dealer_sap 
        WHERE om.live_draft_status = 0 
        ORDER BY om.id DESC";

$result = mysqli_query($db, $sql);

if (!$result) {
    echo "Database query failed: " . mysqli_error($db);
    exit;
}

$count = mysqli_num_rows($result);

if ($count > 0) {
    $sessionId = login();
    if ($sessionId) {
        get_orders($db, $sessionId); // Call get_orders()
    } else {
        echo "Session ID not found.";
    }
} else {
    echo json_encode(["message" => "No orders found."]);
}

function get_orders($db, $sessionId)
{
    $sql_query1 = "SELECT om.*, od.quantity, od.rate, od.product_type, dl.sap_no as dealer_sap, dl.id as dealer_id 
                   FROM order_main om
                   JOIN order_detail as od ON od.main_id = om.id
                   JOIN dealers as dl ON dl.sap_no = om.dealer_sap 
                   WHERE om.live_draft_status = 0
                   ORDER BY om.id DESC";

    $result1 = mysqli_query($db, $sql_query1);
    if (!$result1) {
        die("Error in SQL query: " . mysqli_error($db));
    }

    if (mysqli_num_rows($result1) > 0) {
        while ($user = mysqli_fetch_assoc($result1)) {
            $system_order_id = $user['id'];
            $dealer_sap = $user['dealer_sap'];
            $comment = $user['comment'] ?? '';
            $order_type = $user['type'] ?? '';
            $tl_no = $user['tl_no'] ?? '';

            // Determine 'U_RateType' based on the value of 'type'
            $U_RateType = ($order_type === 'Self') ? 'EXDR' : 'DR';

            // Construct the order data array
            $orderData = [
                "CardCode" => $dealer_sap,
                "DocObjectCode" => "17", // Assuming "17" is fixed and required
                "NumAtCard" => 'online',
                "DocumentLines" => [], // Placeholder for document lines; add data as needed
                "U_RateType" => $U_RateType,
                "U_Veh_No" => $tl_no, // Assuming this should store the transport number
            ];

            $sql_query2 = "SELECT bb.* FROM portalflow.dealers_products_bom_list as bb
                           JOIN dealers_products as dp ON dp.id = bb.main_id 
                           WHERE dp.dealer_id = {$user['dealer_id']} AND dp.id = {$user['product_type']}";
            $result2 = mysqli_query($db, $sql_query2);

            if (!$result2) {
                die("Error in SQL query: " . mysqli_error($db));
            }

            while ($user2 = mysqli_fetch_assoc($result2)) {
                $o_qty = $user2['U_NegativeQty'] === 'Y' ? -$user['quantity'] : $user['quantity'];

                $product_name = $user2['product_name'];
                $line_item_name = $user2['line_item_name'];

                if ($order_type == 'Self') {
                    if ($line_item_name != $product_name . ' BOM - Secondary Freight') {
                        $orderData["DocumentLines"][] = [
                            "ItemCode" => $user2['line_item_sap'],
                            "Quantity" => (int) $o_qty,
                            "UnitPrice" => (float) $user2['unit_price'],
                            "WarehouseCode" => "GRW-1",
                            "TaxOnly" => $user2['U_TaxOnly'] ?? ''
                        ];
                    }
                } else {
                    $orderData["DocumentLines"][] = [
                        "ItemCode" => $user2['line_item_sap'],
                        "Quantity" => (int) $o_qty,
                        "UnitPrice" => (float) $user2['unit_price'],
                        "WarehouseCode" => "GRW-1",
                        "TaxOnly" => $user2['U_TaxOnly'] ?? ''
                    ];
                }
            }

            create_dealer_order_in_draft($orderData, $system_order_id, $db, $dealer_sap, $sessionId);
        }
    } else {
        echo json_encode(["message" => "No orders found."]);
    }

    logout();
}

function create_dealer_order_in_draft($data, $order_id, $db, $dealer_sap, $sessionId)
{
    // print_r($data);
    $curl = curl_init();
    curl_setopt_array($curl, [
        CURLOPT_URL => 'https://asco.flowpetroleum.com.pk:50000/b1s/v1/Drafts',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => json_encode($data),
        CURLOPT_HTTPHEADER => [
            'Content-Type: application/json',
            'Cookie: B1SESSION=' . $sessionId
        ],
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_SSL_VERIFYHOST => false
    ]);

    $response = curl_exec($curl);

    if (curl_errno($curl)) {
        echo "CURL error: " . curl_error($curl);
        curl_close($curl);
        return;
    }

    curl_close($curl);

    $dataArray = json_decode($response, true);

    if ($dataArray && isset($dataArray['DocEntry'])) {
        $docNum = $dataArray['DocEntry'];
        echo "Document Number: " . $docNum;

        $query = "UPDATE `order_main` SET `draft_no` = '$docNum', live_draft_status = 1 WHERE id = $order_id";
        if (!mysqli_query($db, $query)) {
            echo 'Error updating sales number: ' . mysqli_error($db);
            return;
        } else {
            echo 'Sales number Updated<br>';
        }

        $log = "INSERT INTO `order_main_push_sap_log` (`doc_no`, `api_response`, `created_at`, `created_by`) 
                VALUES ('$docNum', '$response', NOW(), '1')";
        if (!mysqli_query($db, $log)) {
            echo "Error: " . mysqli_error($db);
        }

        $record = "INSERT INTO `portalflow`.`order_dpt_sap`
        (`system_order_id`,
        `type`,
        `sap_id`,
        `status`,
        `created_at`,
        `updated_at`)
        VALUES
        ('$order_id',
        'Draft',
        '$docNum',
        '0',
        NOW(),
        '1');";
        if (!mysqli_query($db, $record)) {
            echo "Error: " . mysqli_error($db);
        }
    } else {
        echo $response;
    }
}

function login()
{
    $curl = curl_init();
    curl_setopt_array($curl, [
        CURLOPT_URL => 'https://asco.flowpetroleum.com.pk:50000/b1s/v1/Login',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => json_encode([
            // "CompanyDB" => "ASCO_PROD",
            "CompanyDB" => "ASCO_PROD",
            "Password" => "1234",
            "UserName" => "INT_O2C"
        ]),
        CURLOPT_HTTPHEADER => [
            'Content-Type: application/json',
        ],
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_SSL_VERIFYHOST => false
    ]);

    $response = curl_exec($curl);

    if (curl_errno($curl)) {
        echo "CURL error: " . curl_error($curl);
        curl_close($curl);
        return null;
    }

    curl_close($curl);

    $dataArray = json_decode($response, true);
    return $dataArray['SessionId'] ?? null;
}

function logout()
{
    $curl = curl_init();
    curl_setopt_array($curl, [
        CURLOPT_URL => 'https://asco.flowpetroleum.com.pk:50000/b1s/v1/Logout',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_HTTPHEADER => ['Cookie: ROUTEID=.node2'],
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_SSL_VERIFYHOST => false
    ]);

    $response = curl_exec($curl);

    if (curl_errno($curl)) {
        echo "CURL error: " . curl_error($curl);
    }

    curl_close($curl);
    echo $response;
}

echo 'Service Last Run => ' . date('Y-m-d H:i:s');
?>