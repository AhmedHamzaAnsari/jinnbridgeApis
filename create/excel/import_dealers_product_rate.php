<?php
include("../../config.php");
session_start();
set_time_limit(0); // Set execution time limit to unlimited

header('Content-Type: application/json'); // Set the content type to JSON

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_id = $_POST['user_id'];
    $date = date('Y-m-d H:i:s');

    // Check if the CSV file is uploaded
    if (isset($_FILES['csv_file']) && $_FILES['csv_file']['error'] == 0) {

        // Get the uploaded file's temporary path
        $fileName = $_FILES['csv_file']['tmp_name'];

        // Check if the uploaded file is a CSV file
        if ($_FILES['csv_file']['type'] === 'text/csv' || mime_content_type($fileName) === 'text/plain') {

            // Open the CSV file
            if (($handle = fopen($fileName, "r")) !== FALSE) {

                // Skip the header row
                fgetcsv($handle);

                $success = true; // Initialize success flag
                $errors = []; // Initialize errors array

                // Loop through each row in the CSV file
                while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                    // Extract data from CSV row
                    $dealerr_code = $data[0];
                    $dealer_name = $data[1];
                    $product = $data[2];
                    $freight = $data[3];
                    $indent_price = $data[4];
                    $nozel_price = $data[5];
                    $from_date = $data[6];
                    $to_date = $data[7];

                    // SQL query to fetch dealer ID based on SAP number
                    $get_dealer_id = "SELECT id FROM dealers WHERE sap_no='$dealerr_code'";
                    $result_get_dealer_id = mysqli_query($db, $get_dealer_id);

                    if ($result_get_dealer_id && mysqli_num_rows($result_get_dealer_id) > 0) {
                        $row_get_dealer_id = mysqli_fetch_array($result_get_dealer_id);
                        $dealer_db_id = $row_get_dealer_id['id'];

                        // SQL query to check if the dealer-product record already exists
                        $sql = "SELECT dl.id AS dealer_id, dp.id AS dealer_product_id 
                                FROM dealers_products AS dp
                                JOIN dealers AS dl ON dl.id = dp.dealer_id 
                                JOIN all_products AS pp ON pp.name = dp.name
                                WHERE dl.sap_no = '$dealerr_code' AND pp.name = '$product'";

                        $result = mysqli_query($db, $sql);

                        if ($result) {
                            $count = mysqli_num_rows($result);

                            if ($count > 0) {
                                // If record exists, update it
                                $row = mysqli_fetch_array($result);
                                $dealer_product_id = $row['dealer_product_id'];

                                // Update query for dealers_products
                                $update_query = "UPDATE `dealers_products`
                                                 SET 
                                                 `from` = '$from_date',
                                                 `to` = '$to_date',
                                                 `freight_value` = '$freight',
                                                 `indent_price` = '$indent_price',
                                                 `nozel_price` = '$nozel_price',
                                                 `update_time` = '$date'
                                                 WHERE `id` = $dealer_product_id";

                                // Execute the update query
                                if (!mysqli_query($db, $update_query)) {
                                    $errors[] = 'Error updating product ID ' . $dealer_product_id . ': ' . mysqli_error($db);
                                    $success = false; 
                                }
                            } else {
                                // If no record exists, insert a new one
                                $insert_query = "INSERT INTO `dealers_products` (`dealer_id`, `name`, `freight_value`, `indent_price`, `nozel_price`, `from`, `to`, `created_at`) 
                                                 VALUES 
                                                 ('$dealer_db_id', '$product', '$freight', '$indent_price', '$nozel_price', '$from_date', '$to_date', '$date')";

                                // Execute the insert query
                                if (!mysqli_query($db, $insert_query)) {
                                    $errors[] = 'Error inserting new dealer product: ' . mysqli_error($db);
                                    $success = false;
                                } else {
                                    $dealer_product_id = mysqli_insert_id($db); // Get the inserted ID for logging
                                }
                            }

                            // Log all updates and inserts
                            $log_query = "INSERT INTO `dealer_nozel_price_log`
                                          (`dealer_id`, `product_id`, `indent_price`, `nozel_price`, `freight_value`, `from`, `to`, `description`, `created_at`, `created_by`)
                                          VALUES
                                          ('$dealer_db_id', '$dealer_product_id', '$indent_price', '$nozel_price', '$freight', '$from_date', '$to_date', 'Updated/Inserted via importer', '$date', '$user_id')";

                            if (!mysqli_query($db, $log_query)) {
                                $errors[] = 'Error logging entry for dealer product ID ' . $dealer_product_id . ': ' . mysqli_error($db);
                                $success = false;
                            }
                        } else {
                            $errors[] = 'Error executing query: ' . mysqli_error($db);
                            $success = false; 
                        }
                    } else {
                        $errors[] = 'Dealer not found for SAP number ' . $dealerr_code;
                        $success = false;
                    }
                }

                // Close the file handle
                fclose($handle);

                // Prepare response
                if ($success) {
                    echo json_encode(["status" => "success", "message" => "File uploaded and data processed successfully!"]);
                } else {
                    echo json_encode(["status" => "error", "errors" => $errors]);
                }

            } else {
                echo json_encode(["status" => "error", "message" => "Error opening the file."]);
            }
        } else {
            echo json_encode(["status" => "error", "message" => "Please upload a valid CSV file."]);
        }
    } else {
        echo json_encode(["status" => "error", "message" => "No file uploaded or there was an error uploading the file."]);
    }

    // Close the database connection
    $db->close();
}
?>
