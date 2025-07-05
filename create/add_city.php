<?php
include("../config.php");
session_start();
header("Content-Type: application/json");

$response = ['status' => 'error', 'message' => 'Invalid request'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['city'])) {
    $cityName = trim($_POST['city']);

    if (!empty($cityName)) {
        // Check if city already exists
        $stmt = $db->prepare("SELECT id FROM city WHERE city = ?");
        $stmt->bind_param("s", $cityName);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows === 0) {
            $stmt->close();

            $stmt = $db->prepare("INSERT INTO city (city, created_at) VALUES (?, NOW())");
            $stmt->bind_param("s", $cityName);
            if ($stmt->execute()) {
                $response['status'] = 'success';
                $response['message'] = 'City added successfully';
            } else {
                $response['message'] = 'Insert failed';
            }
        } else {
            $response['message'] = 'City already exists';
        }

        $stmt->close();
    } else {
        $response['message'] = 'City name is required';
    }
}

echo json_encode($response);
?>
