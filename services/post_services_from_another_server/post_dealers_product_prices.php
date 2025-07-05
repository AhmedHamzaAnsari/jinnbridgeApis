<?php
header("Content-Type: application/json");

// Connect to your MySQL database
include("../../hacol_conif_post.php");

// Check if the request method is POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Extract data from the form data
    $dealer_sap = $_POST['dealer_sap'];
    $product_sap = $_POST['product_sap'];
    $product_name = $_POST['product_name'];
    $dealer_name = $_POST['dealer_name'];
    $price_schedule = $_POST['price_schedule'];
    $from = $_POST['from'];
    $to = $_POST['to'];
    $base_price = $_POST['base_price'];
    $other_comp = $_POST['other_comp'];
    $cartage = $_POST['cartage'];
    $net_price = $_POST['net_price'];
    $created_at = date('Y-m-d H:i:s');
    $created_by = '1';
    $status = '0'; // Setting the default status to '1'

    // Prepare the INSERT ON DUPLICATE KEY UPDATE statement
    $stmt = $conn->prepare("INSERT INTO dealers_jd_product_prices (
            dealer_sap,
            product_sap,
            product_name,
            dealer_name,
            price_schedule,
            `from`,
            `to`,
            base_price,
            other_comp,
            cartage,
            net_price,
            created_at,
            created_by,
            status
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ON DUPLICATE KEY UPDATE
            `from` = VALUES(`from`),
            `to` = VALUES(`to`),
            base_price = VALUES(base_price),
            other_comp = VALUES(other_comp),
            cartage = VALUES(cartage),
            net_price = VALUES(net_price),
            created_at = VALUES(created_at),
            created_by = VALUES(created_by),
            status = VALUES(status)
    ");

    // Check if the prepare statement was successful
    if ($stmt) {
        // Bind parameters to the statement
        $stmt->bind_param(
            "ssssssssssssss",
            $dealer_sap,
            $product_sap,
            $product_name,
            $dealer_name,
            $price_schedule,
            $from,
            $to,
            $base_price,
            $other_comp,
            $cartage,
            $net_price,
            $created_at,
            $created_by,
            $status
        );

        // Execute the statement
        if ($stmt->execute() === TRUE) {
            echo json_encode(array("message" => "Record created or updated successfully"));
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