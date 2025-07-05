<?php
include("../config.php");
session_start();
if (isset($_POST)) {


    $user_id = $_POST['user_id'];
    $order_approval = $_POST['spe_order'];
    $depots = $_POST['depots'];
    $vehicles = $_POST['vehicles'];
    // $approved_order_status = mysqli_real_escape_string($db, $_POST['special_order_status']);
    $approved_order_description = mysqli_real_escape_string($db, $_POST['special_order_description']);
    $datetime = date('Y-m-d H:i:s');
    $val = '';
    // echo 'HAmza';



    $query = "UPDATE `order_main` SET 
    `status`='4',
    `comment`='$approved_order_description',
    `tl_no`='$vehicles',
    `depot`='$depots',
    `approved_time`='$datetime' WHERE id=$order_approval";


    if (mysqli_query($db, $query)) {

        // if ($approved_order_status != 2) {
        //     $val = 'Cancelled';
        // } else {
        //     $val = 'Completed';

        // }

        $log = "INSERT INTO `order_detail_log`
        (`order_id`,
        `status`,
        `status_value`,
        `description`,
        `created_at`,
        `created_by`)
        VALUES
        ('$order_approval',
        '4',
        'Proceed',
        '$approved_order_description',
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