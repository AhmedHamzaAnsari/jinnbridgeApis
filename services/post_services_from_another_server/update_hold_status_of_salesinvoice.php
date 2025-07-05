<?php
include("../../hacol_conif_post.php");


// Check if the request method is POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Extract data from the form data
    $id = $_POST['id'];
    $hold_code = $_POST['hold_code'];
    $invoice_no = $_POST['invoice_no']; // Get the invoice_no from the POST request
    $update_at = date('Y-m-d H:i:s');

    // Prepare the UPDATE statement
    $stmt = $conn->prepare("UPDATE order_sales_invoice SET
        hold_code = ?, 
        invoice_no = ?, 
        update_at = ?
        WHERE id = ?");

    // Check if the prepare statement was successful
    if ($stmt) {
        // Bind parameters to the statement
        $stmt->bind_param(
            "sisi", // 's' for string, 'i' for integer
            $hold_code,
            $invoice_no,
            $update_at,
            $id
        );

        // Execute the statement
        if ($stmt->execute() === TRUE) {
            echo json_encode(array("message" => "Record updated successfully"));
        } else {
            echo json_encode(array("error" => "Error: " . $stmt->error));
        }

        // Close the statement
        $stmt->close();
    } else {
        echo json_encode(array("error" => "Prepare statement error: " . $conn->error));
    }

    // Close the connection
    $conn->close();
}
?>
