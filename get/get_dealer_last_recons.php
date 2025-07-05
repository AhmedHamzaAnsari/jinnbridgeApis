<?php
// fetch.php  
include("../config.php");

$access_key = '03201232927';

$pass = $_GET["key"] ?? ''; // Check if key is set
$dealer_id = $_GET["dealer_id"] ?? '';

if ($pass === '') {
    echo 'Key is Required';
    exit;
}

if ($pass !== $access_key) {
    echo 'Wrong Key...';
    exit;
}

if ($dealer_id === '') {
    echo 'Dealer ID is required';
    exit;
}

// Sanitize dealer_id
$dealer_id = intval($dealer_id);

// Query to get the latest task for the dealer
$sql_query1 = "SELECT * FROM inspector_task WHERE dealer_id = $dealer_id and stock_recon=1 ORDER BY id DESC LIMIT 1;";
$result1 = $db->query($sql_query1);

if (!$result1) {
    die("Error: " . mysqli_error($db));
}

$thread = array();

if ($task_row = $result1->fetch_assoc()) {
    $task_id = intval($task_row['id']);

    // Query to get product details for the dealer and task
    $sql_query2 = "SELECT rr.*, ap.name AS product_name, dc.name AS dealer_name 
        FROM dealer_stock_recon_new AS rr
        JOIN dealers_products AS dp ON dp.id = rr.product_id
        JOIN all_products AS ap ON ap.name = dp.name
        JOIN dealers AS dc ON dc.id = rr.dealer_id 
        WHERE rr.task_id = $task_id AND rr.dealer_id = $dealer_id 
        GROUP BY rr.product_id;
    ";

    $result2 = $db->query($sql_query2);

    if (!$result2) {
        die("Error: " . mysqli_error($db));
    }

    while ($row = $result2->fetch_assoc()) {
        $thread[] = $row;
    }
}

// Return data as JSON
echo json_encode($thread);
?>