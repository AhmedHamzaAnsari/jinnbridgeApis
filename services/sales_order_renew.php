<?php
//fetch.php  
include("../config.php");
set_time_limit(500); // Increase the maximum execution time

ini_set('max_execution_time', -1);
date_default_timezone_set("Asia/Karachi");

// Refresh page every 30 seconds
$url1 = $_SERVER['REQUEST_URI'];
header("Refresh: 30; URL=$url1");
echo 'Renew Sales order start time => ' . date('Y-m-d H:i:s') . '<br>';
$access_key = '03201232927';
$pass = $_GET["key"];
$date = date('Y-m-d H:i:s');

if (!empty($pass)) {
    if ($pass === $access_key) {
        // Query to select records with status = 0
        $sql_query1 = "SELECT * FROM order_sales_invoice_cancel_new WHERE status = 0";
        $result1 = $db->query($sql_query1) or die("Error: " . mysqli_error($db)); // Fixed mysqli_error

        while ($user = $result1->fetch_assoc()) {
            $id = $user['id'];
            $org_order_no = $user['org_order_no'];
            $order_no = $user['order_no'];

            // Query to select sales invoice related to the original order number
            $sql_query2 = "SELECT * FROM order_sales_invoice WHERE order_no = '$org_order_no'";
            $result2 = $db->query($sql_query2) or die("Error: " . mysqli_error($db)); // Fixed mysqli_error

            while ($user2 = $result2->fetch_assoc()) {
                $salesid = $user2['id'];

                // Update query to set the new order number and change the status
                $updates = "UPDATE `order_sales_invoice`
                            SET `re_new_order_no` = '$order_no',
                                `status` = '3'
                            WHERE `id` = '$salesid'";

                // Execute the update query
                if (mysqli_query($db, $updates)) {
                    echo "Updated order sales invoice with ID: $salesid<br>";

                    // Update query to change status of the canceled order
                    $updates_renew = "UPDATE `order_sales_invoice_cancel_new`
                                      SET `status` = '1'
                                      WHERE `id` = '$id'";

                    mysqli_query($db, $updates_renew) or die("Error: " . mysqli_error($db)); // Fixed mysqli_error
                } else {
                    echo 'Error updating sales invoice: ' . mysqli_error($db) . '<br>' . $updates;
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
