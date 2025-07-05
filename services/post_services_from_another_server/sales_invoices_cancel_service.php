<?php
ini_set('max_execution_time', '0');
header("Refresh: 20");

include("../../hacol_conif_post.php");

echo "<h1>Cancel Sales Invoices Table</h1><br>";

// Query to select orders with `is_update_status` set to 0
$sql = "SELECT * FROM order_sales_invoice_cancel WHERE is_update_status = 0";
$result = mysqli_query($conn, $sql);

if (!$result) {
    die("Query failed: " . mysqli_error($conn));
}

$count = mysqli_num_rows($result);

if ($count > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        $id = $row['id'];
        $order_no = $row['order_no'];

        // Check if order exists in `order_info` table
        $check_invoice = "SELECT * FROM order_info WHERE order_no = $order_no";
        $result_check_invoice = mysqli_query($conn, $check_invoice);

        if (!$result_check_invoice) {
            echo "Error in check invoice query: " . mysqli_error($conn);
            continue;
        }

        $count_check_invoice = mysqli_num_rows($result_check_invoice);

        if ($count_check_invoice == 0) {
            // If order doesn't exist in `order_info`, update `is_update_status` in `order_sales_invoice_cancel` to 1
            $sql_update_cancel = "UPDATE order_sales_invoice_cancel SET is_update_status = '1' WHERE id = '$id'";
            if (mysqli_query($conn, $sql_update_cancel)) {
                echo "Order canceled in `order_sales_invoice_cancel` table.<br>";
            } else {
                echo "Error updating `order_sales_invoice_cancel`: " . mysqli_error($conn) . "<br>";
            }

            // Update `status` in `order_sales_invoice` table to 3
            $sql_update_invoice = "UPDATE order_sales_invoice SET status = '3' WHERE order_no = '$order_no'";
            if (mysqli_query($conn, $sql_update_invoice)) {
                echo "Order canceled in `order_sales_invoice` table.<br>";
            } else {
                echo "Error updating `order_sales_invoice`: " . mysqli_error($conn) . "<br>";
            }
        } else {
            // If order exists in `order_info`, update `is_update_status` in `order_sales_invoice_cancel` to 2
            $sql_update_existing = "UPDATE order_sales_invoice_cancel SET is_update_status = '2' WHERE id = '$id'";
            if (mysqli_query($conn, $sql_update_existing)) {
                echo "Order marked as already existing in `order_sales_invoice_cancel`.<br>";
            } else {
                echo "Error updating `order_sales_invoice_cancel`: " . mysqli_error($conn) . "<br>";
            }
        }
    }
} else {
    echo '<h1>No Records Found to Send Message</h1>';
}

mysqli_close($conn);
echo "Last Run: " . date('Y-m-d H:i:s');
?>
