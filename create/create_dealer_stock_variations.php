<?php
include("../config.php");
session_start();
if (isset($_POST)) {
    $user_id = $_POST['user_id'];
    $dealer_id = mysqli_real_escape_string($db, $_POST["dealer_id"]);
    $product_id = mysqli_real_escape_string($db, $_POST["product_id"]);
    $opening_stock = mysqli_real_escape_string($db, $_POST["opening_stock"]);
    $purchase_during_inspection_period = mysqli_real_escape_string($db, $_POST["purchase_during_inspection_period"]);
    $total_product_available_for_sale = mysqli_real_escape_string($db, $_POST["total_product_available_for_sale"]);
    $sales_as_per_meter_reading = mysqli_real_escape_string($db, $_POST["sales_as_per_meter_reading"]);
    $book_stock = mysqli_real_escape_string($db, $_POST["book_stock"]);
    $current_physical_stock = mysqli_real_escape_string($db, $_POST["current_physical_stock"]);
    $gain_loss = mysqli_real_escape_string($db, $_POST["gain_loss"]);
    $task_id = mysqli_real_escape_string($db, $_POST["task_id"]);
    $date = date('Y-m-d H:i:s');

    // echo 'HAmza';
    if ($_POST["row_id"] != '') {


    } else {

        $query = "INSERT INTO `dealers_stock_variations`
        (`dealer_id`,
        `task_id`,
        `product_id`,
        `opening_stock`,
        `purchase_during_inspection_period`,
        `total_product_available_for_sale`,
        `sales_as_per_meter_reading`,
        `book_stock`,
        `current_physical_stock`,
        `gain_loss`,
        `created_at`,
        `created_by`)
        VALUES
        ('$dealer_id',
        '$task_id',
        '$product_id',
        '$opening_stock',
        '$purchase_during_inspection_period',
        '$total_product_available_for_sale',
        '$sales_as_per_meter_reading',
        '$book_stock',
        '$current_physical_stock',
        '$gain_loss',
        '$date',
        '$user_id');";


        if (mysqli_query($db, $query)) {


            $output = 1;

        } else {
            $output = 'Error' . mysqli_error($db) . '<br>' . $query;

        }
    }



    echo $output;
}
?>