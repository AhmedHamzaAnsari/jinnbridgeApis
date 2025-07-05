<?php

include("../../hacol_conif_post.php");


// Check if the request method is POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get the POST data
    $customer_id = $_POST['customer_id'];
    $name = $_POST['name'];
    $balance = $_POST['balance'];
    $lastbalance = $_POST['lastbalance'];

    // Prepare an SQL statement to insert data into the table
    // $sql = "INSERT INTO dealers (sap_no, name, acount, last_baldate) VALUES (?, ?, ?, ?) 
    // ON DUPLICATE KEY UPDATE 
    // name = VALUES(name), 
    // acount = VALUES(acount), 
    // last_baldate = VALUES(last_baldate)";

    $sql = "INSERT INTO customer_bal (customer_id, name, balance, lastbalance) 
    VALUES (?, ?, ?, ?) 
    ON DUPLICATE KEY UPDATE 
    name = VALUES(name), 
    balance = VALUES(balance), 
    created_at = NOW(), 
    lastbalance = VALUES(lastbalance)";
    
    // Prepare the statement
    $stmt = $conn->prepare($sql);
    
    // Bind parameters to the statement
    $stmt->bind_param("ssss", $customer_id, $name, $balance, $lastbalance);
    
    // Execute the statement
    if ($stmt->execute()) {
        echo "Balance inserted successfully.";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
    
    // Close the statement
    $stmt->close();
}

// Close the connection
$conn->close();
?>
