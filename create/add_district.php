<?php
include("../config.php");
session_start();
header("Content-Type: application/json");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $district = trim($_POST['district'] ?? '');

    if ($district !== '') {
        $stmt = $db->prepare("INSERT INTO district (district) VALUES (?)");
        $stmt->bind_param("s", $district);

        if ($stmt->execute()) {
            echo json_encode(['status' => 'success']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Insert failed']);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Empty district']);
    }
}
