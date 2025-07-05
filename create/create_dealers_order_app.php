<?php
include ("../config.php");
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_POST['user_id'];
    $datetime = date('Y-m-d H:i:s');
    $dealers = $_POST["dealers"];
    // $order_date = $_POST["order_date"];
    $products = $_POST['products'];
    $products_id = $_POST['products_id'];
    $rates = $_POST['rates'];
    $product_qtys = $_POST['product_qtys'];
    $order_type = $_POST['product_type']; // Assuming product_type is also an array

    $tdate = date('Y-m-d H:i:s');
    $num = mt_rand(100000, 999999);

    $time = $_POST["order_date"];
    $dateTime = new DateTime($time);
    $order_date = $dateTime->format('Y-m-d H:i:s');

    // Check if the row_id is empty or not
    if (!empty($_POST["row_id"])) {
        // Code for updating an existing order (if needed)
    } else {
        $sql = "SELECT * FROM dealers WHERE id='$dealers';";
        $result = mysqli_query($db, $sql);
        $row = mysqli_fetch_array($result);
        $legder_balance = $row['acount'];
        $dealer_sap = $row['sap_no'];

        // Insert into order_main table
        $query_main = "INSERT INTO `order_main` 
                       (`depot`, `type`, `dealer_sap`, `tl_no`, `total_amount`, 
                        `product_json`, `legder_balance`, `created_at`, 
                        `user_id`, `created_by`,`web_order`,`web_order_time`,`status`,`status_value`) 
                       VALUES 
                       ('', '$order_type', '$dealer_sap', '', '', '', 
                        '$legder_balance', '$order_date', '$user_id', '$dealers', '1', '$tdate','5','Forwarded');";

        if (mysqli_query($db, $query_main)) {
            $active = mysqli_insert_id($db);
            $total_order_amount = 0;
            $products_array = [];
            for ($i = 0; $i < count($products); $i++) {
                $product_name = $products[$i];
                $product_id = $products_id[$i];
                $rate = $rates[$i];
                $product_qty = $product_qtys[$i];
                $total_amount = $product_qty * $rate;
                $total_order_amount += $total_amount;

                $product_obj = [
                    "p_id" => $product_id,
                    "quantity" => $product_qty,
                    "indent_price" => $rate,
                    "product_name" => $product_name,
                    "amount" => $total_amount
                ];
                array_push($products_array, $product_obj);
                if ($product_qty > 0) {
                    // Insert into order_detail table
                    $sql1 = "INSERT INTO `order_detail`
                             (`delivery_based`, `quantity`, `rate`, `main_id`, 
                              `depot`, `date`, `cus_id`, `product_type`, `amount`, 
                              `status`, `created_by`, `vehicle`,`created_at`) 
                             VALUES 
                             ('$order_type', '$product_qty', '$rate', '$active', 
                              '', '$order_date', '$dealers', '$product_id', '$total_amount', 
                              '0', '$dealers', '','$tdate');";

                    if (mysqli_query($db, $sql1)) {
                        $output = 1;
                    } else {
                        $output = 'Error: ' . mysqli_error($db);
                        echo $output;
                        exit;
                    }
                }
            }
            $products_json = json_encode($products_array);
            // Update order_main with the total amount and order type
            $order_log = "UPDATE `order_main`
                          SET 
                              `total_amount` = '$total_order_amount' ,
                              `product_json` = '$products_json'
                          WHERE `id` = '$active';";
            mysqli_query($db, $order_log);
        } else {
            $output = 'Error: ' . mysqli_error($db) . '<br>' . $query_main;
            echo $output;
            exit;
        }
    }
    echo $output;
}
?>