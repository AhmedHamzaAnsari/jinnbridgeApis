<?php
header("Content-Type: application/json");

// Connect to your MySQL database
include("../../hacol_conif_post.php");

// Check if the request method is POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get the raw POST data and decode the JSON
    $jsonData = file_get_contents('php://input');
    $data = json_decode($jsonData, true);

    // Check if decoding was successful
    if ($data === null) {
        echo json_encode(["error" => "Invalid JSON format"]);
        exit;
    }

    // Prepare the necessary fields
    $created_at = date('Y-m-d H:i:s');
    $created_by = '1';  // Assuming this value is hardcoded
    $status = '0';      // Default status as '0'

    // Prepare the INSERT statement
    $stmt = $conn->prepare("INSERT INTO dealers_jd_product_prices_data (
            json_data,
            status,
            created_at,
            created_by
        ) VALUES (?, ?, ?, ?)
    ");

    // Check if the prepare statement was successful
    if ($stmt) {
        // Bind the parameters to the prepared statement
        $stmt->bind_param(
            "ssss", 
            $jsonData,  // Raw JSON data
            $status, 
            $created_at, 
            $created_by
        );

        // Execute the statement
        if ($stmt->execute() === TRUE) {
            echo json_encode(["message" => "Record created successfully"]);
        } else {
            echo json_encode(["error" => "Error: " . $conn->error]);
        }

        // Close the statement
        $stmt->close();
    } else {
        echo json_encode(["error" => "Prepare statement error: " . $conn->error]);
    }
} else {
    echo json_encode(["error" => "Invalid request method"]);
}

// Close the connection
$conn->close();
?>
