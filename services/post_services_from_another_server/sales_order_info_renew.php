<?php
header("Content-Type: application/json");

// Connect to your MySQL database
include("../../hacol_conif_post.php");

// Check if the request method is POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Extract data from the form
    $customer_id = $_POST['customer_id'];
    $customer_name = $_POST['customer_name'];
    $order_no = $_POST['order_no'];
    $order_type = $_POST['order_type'];
    $org_order_no = $_POST['org_order_no'];
    $org_order_type = $_POST['org_order_type'];
    $item = $_POST['item'];
    $quantity = $_POST['quantity'];
    $unit_measure = $_POST['unit_measure'];
    $order_date = $_POST['order_date'];
    $depot_code = $_POST['depot_code'];
    $depot_name = $_POST['depot_name'];
    $status = '0'; // Default status is set to '0'
    $created_at = date('Y-m-d H:i:s');
    $update_at = date('Y-m-d H:i:s'); // Assuming it's updated at the same time as created for now

    // Prepare the INSERT statement (without ON DUPLICATE KEY UPDATE)
    $stmt = $conn->prepare("
        INSERT INTO order_sales_invoice_cancel_new (
            customer_id,
            customer_name,
            order_no,
            order_type,
            org_order_no,
            org_order_type,
            item,
            quantity,
            unit_measure,
            order_date,
            depot_code,
            depot_name,
            status,
            created_at,
            update_at
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");

    // Check if the prepare statement was successful
    if ($stmt) {
        // Bind parameters to the statement (15 placeholders for 15 values)
        $stmt->bind_param(
            "sssssssssssssss", // 15 placeholders (s for string type)
            $customer_id,
            $customer_name,
            $order_no,
            $order_type,
            $org_order_no,
            $org_order_type,
            $item,
            $quantity,
            $unit_measure,
            $order_date,
            $depot_code,
            $depot_name,
            $status,
            $created_at,
            $update_at
        );

        // Execute the statement
        if ($stmt->execute() === TRUE) {
            echo json_encode(array("message" => "Record created successfully"));
        } else {
            echo json_encode(array("error" => "Error: " . $conn->error));
        }

        // Close the statement
        $stmt->close();
    } else {
        echo json_encode(array("error" => "Prepare statement error: " . $conn->error));
    }
}

// Close the connection
$conn->close();
?>