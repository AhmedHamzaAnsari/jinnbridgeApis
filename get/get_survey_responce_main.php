<?php
header('Content-Type: application/json');
include("../config.php");

// --- Configuration ---
$access_key = '03201232927';

// --- Validate API Key ---
$pass = $_GET['key'] ?? '';
if (empty($pass)) {
    echo json_encode(['error' => 'Key is Required']);
    exit;
}

if ($pass !== $access_key) {
    echo json_encode(['error' => 'Wrong Key']);
    exit;
}

// --- Validate Dealer ID ---
$dealer_id = $_GET['dealer_id'] ?? '';
if (empty($dealer_id)) {
    echo json_encode(['error' => 'Dealer ID Required']);
    exit;
}

// --- Query (no bind_param) ---
$query = "SELECT * FROM store_audit_main WHERE dealer_id = '$dealer_id' ORDER BY id DESC LIMIT 1";
$result = $db->query($query);

if (!$result) {
    echo json_encode(['error' => 'Database error: ' . $db->error]);
    exit;
}

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();

    // Decode JSON in `data` column
    $decodedData = json_decode($row['data'], true);
    if (json_last_error() === JSON_ERROR_NONE) {
        $row['data'] = $decodedData;
        echo json_encode([$row]);
    } else {
        echo json_encode(['error' => 'Invalid JSON format in data field']);
    }
} else {
    echo json_encode(['error' => 'No audit data found']);
}
