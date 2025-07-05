<?php
include ("../../config.php");
session_start();
if (isset($_POST)) {
    $user_id = $_POST['user_id'];
    $dealer_id = mysqli_real_escape_string($db, $_POST["dealer_id"]);
    $dealer_products = mysqli_real_escape_string($db, $_POST["products"]);
    $lorry_no = mysqli_real_escape_string($db, $_POST["lorry_no"]);
    $min_limit = mysqli_real_escape_string($db, $_POST["min_limit"]);
    $max_limit = mysqli_real_escape_string($db, $_POST["max_limit"]);
    $current_dip = mysqli_real_escape_string($db, $_POST["current_dip"]);
    $current_reading = mysqli_real_escape_string($db, $_POST["current_reading"]);
    $current_temperature = mysqli_real_escape_string($db, $_POST["current_temperature"]);
    $current_available_stock = mysqli_real_escape_string($db, $_POST["current_available_stock"]);



    $date = date('Y-m-d H:i:s');

    // echo 'HAmza';
    if ($_POST["row_id"] != '') {


    } else {

        $query = "INSERT INTO `depots_tanks`
                        (`depot_id`,
                        `name`,
                        `product`,
                        `min_limit`,
                        `max_limit`,
                        `current_dip`,
                        `current_reading`,
                        `temperature`,
                        `actual_stock`,
                        `created_at`,
                        `created_by`)
                        VALUES
                        ('$dealer_id',
                        '$lorry_no',
                        '$dealer_products',
                        '$min_limit',
                        '$max_limit',
                        '$current_dip',
                        '$current_reading',
                        '$current_temperature',
                        '$current_available_stock',
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