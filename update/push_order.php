<?php
include("../config.php");
header("Content-Type: application/json");
date_default_timezone_set("Asia/Karachi");

$access_key = '03201232927';
$data = json_decode(file_get_contents("php://input"), true);

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $key = $data['key'] ?? null;
    $id = $data['id'] ?? null;
    $saleOrder = $data['sale_order'] ?? null;

    if ($key !== $access_key) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid access key']);
        exit;
    }

    if (!$id || !$saleOrder) {
        echo json_encode(['status' => 'error', 'message' => 'ID and Sale Order are required']);
        exit;
    }

    // Check if the order exists
    $check_query = "SELECT id FROM order_main WHERE id = '$id'";
    $check_result = $db->query($check_query);

    if (!$check_result || $check_result->num_rows == 0) {
        echo json_encode(['status' => 'error', 'message' => 'Order not found']);
        exit;
    }

    // Set static status to Forwarded
    $approved_time = date("Y-m-d H:i:s");

    $update_query = "UPDATE order_main 
                     SET SaleOrder = '$saleOrder', 
                         approved_time = '$approved_time',
                         live_order_status = '1',
                         status_value = 'Forwarded'
                     WHERE id = '$id'";

    if ($db->query($update_query)) {
        echo json_encode(['status' => 'success', 'message' => 'Order pushed successfully']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Database update failed']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
}
?>
