<?php
// fetch.php  

ini_set('max_execution_time', '0');
$url1 = $_SERVER['REQUEST_URI'];
header("Refresh: 7200; URL=$url1");
include("../config.php");
set_time_limit(5000); // Set time limit for long-running queries

$access_key = '03201232927';

echo 'Start TIme ' . date('Y-m-d H:i:s');

$pass = $_GET["key"];
if ($pass != '') {
    if ($pass == $access_key) {
        // Query to get the latest record from the table
        $sql_query1 = "SELECT * FROM dealers_jd_product_prices_data WHERE status=0 ORDER BY id DESC LIMIT 1;";
        $result1 = $db->query($sql_query1) or die("Error: " . $db->error);

        if ($result1->num_rows > 0) {
            while ($user = $result1->fetch_assoc()) {
                $id = $user['id'];
                $json_data = $user['json_data'];

                // Decode the JSON data
                $dataArray = json_decode($json_data, true);

                // Check if decoding was successful
                if (json_last_error() === JSON_ERROR_NONE) {
                    // Array to track inserted records to avoid duplicates
                    $unique_records = array();

                    // Iterate over each record in the array
                    foreach ($dataArray as $item) {
                        // Create a unique key for each record based on dealer_sap and product_sap


                        // Prepare the insert statement
                        $insert = "INSERT INTO `dealers_jd_product_prices`
                                        (`dealer_sap`, `product_sap`, `product_name`, `dealer_name`, 
                                         `price_schedule`, `from`, `to`, `base_price`, 
                                         `other_comp`, `cartage`, `net_price`, `status`, 
                                         `created_at`, `created_by`)
                                        VALUES
                                        (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), ?);";

                        // Prepare the statement
                        $insert_stmt = $db->prepare($insert);

                        // Check if the statement was prepared successfully
                        if ($insert_stmt) {
                            // Define status and created_by as variables
                            $status = '0'; // Variable for status
                            $created_by = '1'; // Variable for created_by

                            // Bind the parameters
                            $insert_stmt->bind_param(
                                "sssssssssssss",
                                $item['dealer_sap'],
                                $item['product_sap'],
                                $item['product_name'],
                                $item['dealer_name'],
                                $item['price_schedule'],
                                $item['from'],
                                $item['to'],
                                $item['base_price'],
                                $item['other_comp'],
                                $item['cartage'],
                                $item['net_price'],
                                $status, // status
                                $created_by  // created_by
                            );

                            // Execute the statement
                            if ($insert_stmt->execute()) {
                                // echo "Price Update Successful<br>";
                            } else {
                                // echo "Error: " . $insert_stmt->error . "<br>" . $insert;
                            }

                            // Close the insert statement
                            $insert_stmt->close();
                        } else {
                            // echo "Error preparing insert statement: " . $db->error . "<br>";
                        }

                    }

                    // After all records are processed, update the status of the record in `dealers_jd_product_prices_data`
                    $update_status_sql = "UPDATE dealers_jd_product_prices_data SET status = 1 WHERE id = ?";
                    $update_stmt = $db->prepare($update_status_sql);

                    if ($update_stmt) {
                        // Bind the ID of the record to update
                        $update_stmt->bind_param("i", $id);

                        // Execute the update
                        if ($update_stmt->execute()) {
                            echo "Status of the processed record updated to 1.<br>";
                        } else {
                            echo "Error updating status: " . $update_stmt->error . "<br>";
                        }

                        // Close the update statement
                        $update_stmt->close();
                    } else {
                        echo "Error preparing update statement: " . $db->error . "<br>";
                    }
                } else {
                    echo "Error decoding JSON: " . json_last_error_msg();
                }
            }
        } else {
            echo "No data found.";
        }
    } else {
        echo 'Wrong Key...';
    }
} else {
    echo 'Key is Required';
}
echo 'End TIme ' . date('Y-m-d H:i:s');

?>