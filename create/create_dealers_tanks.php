<?php
include ("../config.php");
session_start();
if (isset($_POST)) {
    $user_id = $_POST['user_id'];
    $dealer_id = mysqli_real_escape_string($db, $_POST["dealer_id"]);
    $lorry_no = mysqli_real_escape_string($db, $_POST["lorry_no"]);
    $products = mysqli_real_escape_string($db, $_POST["products"]);
    $min_limit = mysqli_real_escape_string($db, $_POST["min_limit"]);
    $max_limit = mysqli_real_escape_string($db, $_POST["max_limit"]);
    $current_reading = mysqli_real_escape_string($db, $_POST["current_reading"]);
    $current_dip = mysqli_real_escape_string($db, $_POST["current_dip"]);
    $date = date('Y-m-d H:i:s');

    // echo 'HAmza';
    if ($_POST["row_id"] != '') {


    } else {

        $query = "INSERT INTO `dealers_lorries`
                        (`dealer_id`,
                        `lorry_no`,
                        `product`,
                        `min_limit`,
                        `max_limit`,
                        `current_dip`,
                        `current_reading`,
                        `update_time`,
                        `created_at`,
                        `created_by`)
                        VALUES
                        ('$dealer_id',
                        '$lorry_no',
                        '$products',
                        '$min_limit',
                        '$max_limit',
                        '$current_dip',
                        '$current_reading',
                        '$date',
                        '$date',
                        '$user_id');";


        if (mysqli_query($db, $query)) {

            $tank_id = mysqli_insert_id($db);

            $readings_tanks = "INSERT INTO `dealer_dip_log`
            (`dealer_id`,
            `tank_id`,
            `previous_dip`,
            `current_dip`,
            `old_reading`,
            `current_reading`,
            `datetime`,
            `description`,
            `created_at`,
            `created_by`)
            VALUES
            ('$dealer_id',
            '$tank_id',
            '$current_dip',
            '$current_dip',
            '$current_reading',
            '$current_reading',
            '$date',
            '---',
            '$date',
            '$user_id');";
            if (mysqli_query($db, $readings_tanks)) {

                $output = 1;


            } else {
                $output = 'Error' . mysqli_error($db) . '<br>' . $readings_tanks;

            }


        } else {
            $output = 'Error' . mysqli_error($db) . '<br>' . $query;

        }
    }



    echo $output;
}
?>