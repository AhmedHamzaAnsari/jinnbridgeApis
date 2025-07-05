<?php
include("../config.php");
session_start();
header("Content-Type: application/json");

// چیک کریں کہ ریکویسٹ `POST` ہے
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    http_response_code(405);
    echo json_encode(["status_code" => 405, "message" => "Method Not Allowed"]);
    exit;
}

// ریکویسٹ باڈی چیک کریں
if (!isset($_POST["user_id"], $_POST["json_data"], $_POST["dealer_id"])) {
    http_response_code(400);
    echo json_encode(["status_code" => 400, "message" => "Bad Request: Missing parameters"]);
    exit;
}

$user_id = mysqli_real_escape_string($db, $_POST["user_id"]);
$response = mysqli_real_escape_string($db, $_POST["json_data"]);
$dealer_id = mysqli_real_escape_string($db, $_POST["dealer_id"]);
$datetime = date('Y-m-d H:i:s');

// ڈیٹا بیس میں انسرت کرنے کی کوئری
$query = "INSERT INTO `dealer_signage_setup` (`dealer_id`, `json_data`, `created_at`, `created_by`)
          VALUES ('$dealer_id', '$response', '$datetime', '$user_id')";

if (mysqli_query($db, $query)) {
    http_response_code(201);
    echo json_encode(["status_code" => 201, "message" => "Data inserted successfully"]);
} else {
    http_response_code(500);
    echo json_encode(["status_code" => 500, "message" => "Database Error: " . mysqli_error($db)]);
}
?>
