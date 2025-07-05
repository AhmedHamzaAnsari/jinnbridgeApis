<?php
include("../config.php");
session_start();
if (isset($_POST)) {
    $user_id = $_POST['user_id'];
    $date = date('Y-m-d H:i:s');
    $userData = count($_POST["product_qty"]);
    // echo 'HAmza';
    
    $total_amount = 0;
    $mains_id = '';

        for ($i = 0; $i < $userData; $i++) {

            $product_name = $_POST['product_name'][$i];
            $product_qty = $_POST['product_qty'][$i];
            $product_id = $_POST['product_id'][$i];
            $product_old_qty = $_POST['product_old_qty'][$i];
            $main_id = $_POST['main_id'][$i];
            $sub_id = $_POST['sub_id'][$i];
            $rate = $_POST['rate'][$i];
            $amount = $rate * $product_qty;
            $total_amount += $amount;
            $mains_id = $main_id;

            $query_count = "UPDATE `order_detail`
            SET
            `quantity` = '$product_qty',
            `amount` = '$amount'
            WHERE `id` = '$sub_id' and main_id = '$main_id';";

            if (mysqli_query($db, $query_count)) {
                $insert_log = "INSERT INTO `order_qty_update_log`
                (`order_main_id`,
                `order_sub_id`,
                `product_id`,
                `product_name`,
                `old_qty`,
                `new_qty`,
                `created_at`,
                `created_by`)
                VALUES
                ('$main_id',
                '$sub_id',
                '$product_id',
                '$product_name',
                '$product_old_qty',
                '$product_qty',
                '$date',
                '$user_id');";
                if (mysqli_query($db, $insert_log)) {
                    $output = 1;
    
                }else {
                    $output = 'Error' . mysqli_error($db) . '<br>' . $insert_log;
        
                }

            }else {
                $output = 'Error' . mysqli_error($db) . '<br>' . $query_count;
    
            }

            $main_update = "UPDATE `order_main`
            SET
            `total_amount` = '$total_amount'
            WHERE `id` = '$mains_id';";
            if (mysqli_query($db, $main_update)) {
                $output = 1;

            }else {
                $output = 'Error' . mysqli_error($db) . '<br>' . $main_update;
    
            }

        }

        
    



    echo $output;
}
?>