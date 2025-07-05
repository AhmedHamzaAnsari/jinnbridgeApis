<?php
include_once('../config.php');

header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(["status" => "error", "message" => "Only POST requests allowed"]);
    exit;
}

if (!isset($_POST['dealer_id'], $_POST['inspection_id'], $_POST['data'])) {
    echo json_encode(["status" => "error", "message" => "Missing required fields"]);
    exit;
}

$dealer_id = $db->real_escape_string($_POST['dealer_id']);
$inspection_id = $db->real_escape_string($_POST['inspection_id']);
$created_by = isset($_POST['created_by']) ? $db->real_escape_string($_POST['created_by']) : 'system';
$created_at = date('Y-m-d H:i:s');

$data = json_decode($_POST['data'], true);
if (!is_array($data)) {
    echo json_encode(["status" => "error", "message" => "Invalid JSON in data"]);
    exit;
}

// Helper function to get category_id from category name (optional)
function getCategoryId($categoryName, $db) {
    $categoryName = $db->real_escape_string($categoryName);
    $sql = "SELECT id FROM categories WHERE name = '$categoryName' LIMIT 1";
    $result = $db->query($sql);
    if ($result && $row = $result->fetch_assoc()) {
        return $row['id'];
    }
    return 0;
}

// Insert into survey_response_main
$json_data = $db->real_escape_string(json_encode($data));
$sql_main = "INSERT INTO survey_response_main (inspection_id, dealer_id, data, created_at, created_by) 
             VALUES ('$inspection_id', '$dealer_id', '$json_data', '$created_at', '$created_by')";

if (!$db->query($sql_main)) {
    echo json_encode(["status" => "error", "message" => "Failed to insert into survey_response_main"]);
    exit;
}

$main_id = $db->insert_id;

// Insert into survey_response for each item
foreach ($data as $item) {
    $question_id = isset($item['question_id']) ? $db->real_escape_string($item['question_id']) : '';
    $category_name = isset($item['category']) ? $item['category'] : '';
    $category_id = getCategoryId($category_name, $db);
    $response = isset($item['response']) ? $db->real_escape_string($item['response']) : '';
    $comment = isset($item['comment']) ? $db->real_escape_string($item['comment']) : '';
    $date = isset($item['date']) ? $db->real_escape_string($item['date']) : '';
    $description = isset($item['description']) ? $db->real_escape_string($item['description']) : '';

    $sql_resp = "INSERT INTO survey_response (main_id, inspection_id, category_id, question_id, response, comment, dealer_id, date, description, created_at, created_by)
                 VALUES ('$main_id', '$inspection_id', '$category_id', '$question_id', '$response', '$comment', '$dealer_id', '$date', '$description', '$created_at', '$created_by')";

    $db->query($sql_resp);
}

echo json_encode(["status" => "success", "message" => "Survey data saved successfully"]);
?>
