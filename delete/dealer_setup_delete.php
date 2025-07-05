<?php
// fetch.php  
include("../config.php");

// Access key for validation
$access_key = '03201232927';

// Get key and dealer_id from the GET request
$pass = isset($_GET["key"]) ? $_GET["key"] : '';
$dealer_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Check if access key is provided and valid
if (!empty($pass)) {
    if ($pass === $access_key) {
        // Ensure the dealer_id is valid
        if ($dealer_id != "") {

            // Define all SQL queries in an array for easier handling
            $queries = [
                "DELETE FROM `dealers_lorries` WHERE dealer_id = $dealer_id",
                "DELETE FROM `dealers_dispenser` WHERE dealer_id = $dealer_id",
                "DELETE FROM `dealers_nozzel` WHERE dealer_id = $dealer_id",
                "DELETE FROM `dealers_nozzel_readings` WHERE dealer_id = $dealer_id",
                "DELETE FROM `dealer_dip_log` WHERE dealer_id = $dealer_id",
                "DELETE FROM `dealer_stock_recon_new` WHERE dealer_id = $dealer_id",
                "DELETE FROM `inspector_task` WHERE dealer_id = $dealer_id",
                "DELETE FROM `survey_response` WHERE dealer_id = $dealer_id",
                "DELETE FROM `survey_response_files` WHERE dealer_id = $dealer_id",
                "DELETE FROM `survey_response_main` WHERE dealer_id = $dealer_id"
            ];

            // Execute all queries and check for errors
            $allSuccessful = true;
            foreach ($queries as $query) {
                if (!mysqli_query($db, $query)) {
                    $allSuccessful = false;
                    error_log('SQL Error: ' . mysqli_error($db)); // Log the error
                    break;
                }
            }

            // Output result based on the execution status
            if ($allSuccessful) {
                echo 1; // Success
            } else {
                echo 0;
            }

        } else {
            echo 0;
        }
    } else {
        echo 'Wrong Key.';
    }
} else {
    echo 'Key is Required.';
}
?>