<?php
include '../config.php';
header('Content-Type: application/json');

// Validate input
if (!isset($_GET['id']) || !isset($_GET['key']) || $_GET['key'] !== '03201232927') {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid request.']);
    exit();
}

$id = intval($_GET['id']);

// Use correct table and field mappings
$query = "
    SELECT dp.id,
           dp.dealer_id,
           d.name AS dealer_name,
           dp.name AS product_name,
           dp.from AS from_date,
           dp.to AS to_date,
           dp.indent_price,
           dp.nozel_price,
           dp.freight_value,
           dp.description
    FROM dealers_products dp
    JOIN dealers d ON dp.dealer_id = d.id
    WHERE dp.id = $id
";

$result = $db->query($query);

// Handle errors
if (!$result) {
    http_response_code(500);
    echo json_encode(['error' => 'Database error', 'details' => $db->error]);
    exit();
}

if ($result->num_rows === 0) {
    http_response_code(404);
    echo json_encode(['error' => 'Record not found.']);
    exit();
}

// Return the data
$data = $result->fetch_assoc();
echo json_encode($data);
exit();
