<?php
include("../../config.php");
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = mysqli_real_escape_string($db, $_POST["user_id"]);
    $dealer_id = mysqli_real_escape_string($db, $_POST["dealer_id"]);
    $total = mysqli_real_escape_string($db, $_POST["total"]);
    $response = $_POST["product"];  // JSON input

    $datetime = date('Y-m-d H:i:s');

    // Insert into `lubes_order_main`
    $query_main = "INSERT INTO `lubes_order_main` 
        (`dealer_id`, `total_amount`, `created_at`, `created_by`,`json_data`) 
        VALUES ('$dealer_id', '$total', '$datetime', '$user_id','$response')";

    if (mysqli_query($db, $query_main)) {
        $main_id = mysqli_insert_id($db);  // Get last inserted ID for main table

        $data = json_decode($response, true);  // Decode JSON string into associative array

        // Check if JSON decoding was successful
        if ($data === null && json_last_error() !== JSON_ERROR_NONE) {
            echo "Error decoding JSON: " . json_last_error_msg();
            exit;
        }

        // Prepare to insert into `lubes_order_sub`
        $output = 0;  // Default output in case of failure

        foreach ($data as $item) {
            $product_id = mysqli_real_escape_string($db, $item['product_id']);
            $cat_id = mysqli_real_escape_string($db, $item['cat_id']);
            $size_id = mysqli_real_escape_string($db, $item['size_id']);
            $price = mysqli_real_escape_string($db, $item['price']);
            $qty = mysqli_real_escape_string($db, $item['qty']);
            $product_code = mysqli_real_escape_string($db, $item['product_code']);

            $sql_sub = "INSERT INTO `lubes_order_sub` 
                (`main_id`, `product_id`, `cat_id`, `size_id`, `price`, `qty`, `dealers_id`, `created_at`, `created_by`) 
                VALUES ('$main_id', '$product_id', '$cat_id', '$size_id', '$price', '$qty', '$dealer_id', '$datetime', '$user_id')";

            // Execute insert query for each product
            if (mysqli_query($db, $sql_sub)) {
                $output = 1;  // Success flag
            } else {
                $output = 0;  // Failure flag
                break;  // Stop further execution if an error occurs
            }
        }

        echo $output;  // Output success (1) or failure (0)
    } else {
        echo "Error: " . mysqli_error($db);  // Error in inserting main record
    }
}
?>