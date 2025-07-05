<?php
// fetch.php  
include("../config.php");
set_time_limit(500); // Increase the maximum execution time

ini_set('max_execution_time', -1); // Allow infinite execution time (not recommended for production)
date_default_timezone_set("Asia/Karachi");

// Access key for validation
$access_key = '03201232927';  
$pass = $_GET["key"];  // Get the key from the query string
$date = date('Y-m-d H:i:s');  // Current date and time

// Check if the key is provided
if (!empty($pass)) {
    // Validate the key
    if ($pass === $access_key) {
        // Query to select records where JD is not zero
        $sql_query1 = "SELECT * FROM dealers_with_rm_tm WHERE JD != 0";
        $result1 = mysqli_query($db, $sql_query1);

        // Check if query execution was successful
        if ($result1 === false) {
            echo "Error: " . mysqli_error($db);
        } else {
            // Process each row from the result
            while ($user = mysqli_fetch_assoc($result1)) {
                $JD = $user['JD'];
                $Region = $user['Region'];
                $RM = $user['RM'];
                $TM = $user['TM'];
                
                echo "Processing JD: " . $JD . '<br>';

                // Query to update dealers table with RM, TM, and Region info
                $sql = "UPDATE `dealers`
                        SET
                        `tm` = '$RM',
                        `asm` = '$TM',
                        `region` = '$Region'
                        WHERE `sap_no` = '$JD';";

                $result = mysqli_query($db, $sql);

                // Check if the update query was successful
                if ($result === false) {
                    echo "Error updating dealer (JD: $JD): " . mysqli_error($db) . '<br>';
                } else {
                    echo "Dealer updated: JD - " . $JD . '<br>';
                }
            }
        }
    } else {
        echo 'Wrong Key...';
    }
} else {
    echo 'Key is Required';
}
?>
