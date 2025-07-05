<?php
//fetch.php  
include("../config.php");

$access_key = '03201232927';
$pass = $_GET["key"];

if ($pass != '') {
    if ($pass == $access_key) {
        $month = $_GET["month"];
        $tm_id = $_GET["tm_id"];

        // Initialize an array to store the data
        $data = [];
      
        // SQL query to retrieve product and target data
        $sql = "SELECT 
                    us.name AS tm_name,
                    pp.name AS product,
                    tm.tm_id,
                    tm.date_month,
                    IFNULL(tm.target_amount, 'target not assigned') AS target_amount,
                    tm.created_at 
                FROM all_products AS pp
                LEFT JOIN tm_monthly_targets AS tm 
                    ON pp.name = tm.product_id
                LEFT JOIN users AS us 
                    ON us.id = tm.tm_id
                WHERE tm.date_month = '$month' and tm.tm_id=$tm_id";

        $result = $db->query($sql);

        // Process each row in the result set
        while ($row = $result->fetch_assoc()) {
            $tm_id = $row["tm_id"];
            $product = $row["product"];
            $date_month = $row["date_month"];
            $target_amount = $row["target_amount"];
            $created_at = $row["created_at"];
            $tm_name = $row["tm_name"];

            // Query to get the total sales for the current product and month
            $get_orders = "SELECT SUM(si.quantity) AS month_sales 
            FROM order_info as si 
            join order_sales_invoice as ss on ss.order_no=si.order_no
            join all_products as pp on pp.sap_no=si.item
            join dealers as dl on dl.sap_no=si.customer_id
            WHERE ss.status!=3 and DATE_FORMAT(STR_TO_DATE(si.order_date, '%d-%b-%y'), '%Y-%m') = '$month' 
            AND pp.name = '$product' 
            and dl.asm = $tm_id";

            $result_orders = $db->query($get_orders);
            $t_sales = 0;

            // Fetch sales data if available
            if ($result_orders && $row_2 = $result_orders->fetch_assoc()) {
                $t_sales = $row_2['month_sales'] ?: 0; // Set to 0 if no sales data found
            }

            // Build the data array for each product
            $data[] = [
                "tm_id" => strval($tm_id),
                "tm_name" => strval($tm_name),
                "product" => strval($product),
                "date_month" => strval($date_month),
                "target" => strval($target_amount),
                "target_achieve" => strval($t_sales),
                "target_assign_time" => strval($created_at),
            ];
            
        }

        // Convert the array to a JSON string and output
        echo json_encode($data);

    } else {
        echo 'Wrong Key...';
    }
} else {
    echo 'Key is Required';
}
?>
