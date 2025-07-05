<?php

include("../../hacol_conif_post.php");


// Check if the request method is POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
   
    $order_no = $_POST['order_no'];
    $status = $_POST['status'];
    $date = date('Y-m-d H:i:s');


    $stmt = $conn->prepare("INSERT INTO order_sales_invoice_cancel (order_no, status, created_at) VALUES (?, ?, ?)");

    // Bind the parameters
    $stmt->bind_param("sss", $order_no, $status, $date);



    // Execute the statement
    if ($stmt->execute() === TRUE) {
        // echo json_encode(array("message" => "Record created successfully"));
        // echo "Record created successfully";
    } else {
        // echo json_encode(array("error" => "Error: " . $conn->error));
        echo "Error: " . $conn->error;
    }


    // Close the statement
    $stmt->close();
}

// Close the connection
$conn->close();
// echo 'Service Last Run => ' . date('Y-m-d H:i:s');

?>