<?php
include ("../../config.php");
session_start();
if (isset($_POST)) {


    $user_id = $_POST['user_id'];
    $dealer_id = $_POST['dealer_id'];
    $tank_id = $_POST['tank_id'];
    $old_dip = $_POST['old_dip'];
    $new_dip = $_POST['dip_input'];
    $dip_time = $_POST['date_time'];
    $dip_temprature = $_POST['dip_temprature'];

    $old_reading = $_POST['old_dip_reading'];
    $new_reading = $_POST['new_dip_reading'];
    $current_available_stock = $_POST['current_available_stock'];

    $diff_reading = $old_reading-$new_reading;

    $gain_loss = $old_reading-$new_reading;


    $dip_description = mysqli_real_escape_string($db, $_POST['dip_description']);

    $datetime = date('Y-m-d H:i:s');

    // echo 'HAmza';

    $query = "UPDATE `depots_tanks` SET 
    `current_dip`='$new_dip',
    `current_reading`='$new_reading',
    `temperature`='$dip_temprature',
    `update_time`='$datetime'
    WHERE id=$tank_id";


    if (mysqli_query($db, $query)) {


        $log = "INSERT INTO `depots_tanks_dip_log`
        (`depot_id`,
        `tank_id`,
        `previous_dip`,
        `current_dip`,
        `previous_reading`,
        `current_reading`,
        `temperature`,
        `datetime`,
        `description`,
        `gain_loss`,
        `created_at`,
        `created_by`)
        VALUES
        ('$dealer_id',
        '$tank_id',
        '$old_dip',
        '$new_dip',
        '$old_reading',
        '$new_reading',
        '$dip_temprature',
        '$dip_time',
        '$dip_description',
        '$gain_loss',
        '$datetime',
        '$user_id');";
        if (mysqli_query($db, $log)) {
            $output = 1;

        } else {
            $output = 'Error' . mysqli_error($db) . '<br>' . $log;

        }

    } else {
        $output = 'Error' . mysqli_error($db) . '<br>' . $query;

    }




    echo $output;
}
?>