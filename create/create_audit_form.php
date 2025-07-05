<?php
include_once('../config.php');
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");

// Only allow POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(["status" => "error", "message" => "Only POST requests are allowed"]);
    exit;
}

// Check required POST fields
if (!isset($_POST['user_id'], $_POST['dealer_id'], $_POST['json_data'])) {
    http_response_code(400);
    echo json_encode(["status" => "error", "message" => "Missing required fields"]);
    exit;
}

$user_id    = $db->real_escape_string($_POST['user_id']);
$dealer_id  = $db->real_escape_string($_POST['dealer_id']);
$json_input = $_POST['json_data'];
$created_at = date('Y-m-d H:i:s');

// Decode JSON input
$data = json_decode($json_input, true);
if (!is_array($data)) {
    http_response_code(400);
    echo json_encode(["status" => "error", "message" => "Invalid JSON format"]);
    exit;
}

$escaped_json = $db->real_escape_string(json_encode($data));
$inspection_id = uniqid('insp_');
$user_app_id = $user_id; // Use same as user_id for now

// Step 1: Insert into store_audit_main
$main_sql = "INSERT INTO store_audit_main 
    (inspection_id, dealer_id, data, user_app_id, created_at, created_by)
    VALUES 
    ('$inspection_id', '$dealer_id', '$escaped_json', '$user_app_id', '$created_at', '$user_id')";

if (!$db->query($main_sql)) {
    http_response_code(500);
    echo json_encode(["status" => "error", "message" => "Failed to insert into store_audit_main", "db_error" => $db->error]);
    exit;
}

$main_id = $db->insert_id;

// Step 2: Loop and insert into survey_audit_response
foreach ($data as $item) {
    $category_id = $db->real_escape_string($item['category_id'] ?? '');
    $question_id = $db->real_escape_string($item['question_id'] ?? '');
    $response    = $db->real_escape_string($item['response'] ?? '');
    $comment     = $db->real_escape_string($item['comment'] ?? '');
    $date        = $db->real_escape_string($item['date'] ?? '');
    $description = $db->real_escape_string($item['description'] ?? '');

    $response_sql = "INSERT INTO survey_audit_response 
        (main_id, inspection_id, category_id, question_id, response, comment, dealer_id, date, description, created_at, created_by)
        VALUES 
        ('$main_id', '$inspection_id', '$category_id', '$question_id', '$response', '$comment', '$dealer_id', '$date', '$description', '$created_at', '$user_id')";
    
    $db->query($response_sql); // Ignore errors for individual rows
}

// Final success response
http_response_code(201);
echo json_encode([
    "status" => "success",
    "message" => "Audit form submitted successfully",
    "inspection_id" => $inspection_id,
    "main_id" => $main_id
]);
?>
