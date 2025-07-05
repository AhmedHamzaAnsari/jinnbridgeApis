<?php
include("../config.php");
header("Content-Type: application/json");

$response = ['status' => 'error', 'message' => 'Invalid request'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['region'])) {
    $region = trim($_POST['region']);

    if (!empty($region)) {
        $stmt = $db->prepare("SELECT id FROM region WHERE region = ?");
        $stmt->bind_param("s", $region);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows === 0) {
            $stmt = $db->prepare("INSERT INTO region (region, created_at) VALUES (?, NOW())");
            $stmt->bind_param("s", $region);
            if ($stmt->execute()) {
                $response['status'] = 'success';
                $response['message'] = 'Region added successfully';
            } else {
                $response['message'] = 'Insert failed';
            }
        } else {
            $response['message'] = 'Region already exists';
        }

        $stmt->close();
    } else {
        $response['message'] = 'Region name is required';
    }
}

echo json_encode($response);
