<?php
include("../config.php");

$access_key = '03201232927';
$pass = $_GET["key"] ?? '';
$id = $_GET['id'] ?? '';

if ($pass === '') {
    echo 'Key is Required';
    exit;
}

if ($pass !== $access_key) {
    echo 'Wrong Key...';
    exit;
}

if ($id === '') {
    echo 'ID is required';
    exit;
}

$sql_query = "SELECT * FROM `users` WHERE id = ?";
$stmt = $db->prepare($sql_query);
$stmt->bind_param("s", $id);
$stmt->execute();
$result = $stmt->get_result();

$response = [];

while ($user = $result->fetch_assoc()) {

    // If Depot, fetch emails from users_depott
    if ($user['privilege'] === 'Depot') {
        $email_query = "SELECT user_email FROM users_depott WHERE created_by = ? ORDER BY id DESC LIMIT 1";
        $stmt_email = $db->prepare($email_query);
        $stmt_email->bind_param("s", $id);
        $stmt_email->execute();
        $email_result = $stmt_email->get_result();

        if ($email_result && $email_result->num_rows > 0) {
            $email_row = $email_result->fetch_assoc();
            $emails = array_map('trim', explode(',', $email_row['user_email']));
            $user['depot_emails'] = $emails;
        } else {
            $user['depot_emails'] = [];
        }
    }

    $response[] = $user;
}

echo json_encode($response);
?>
