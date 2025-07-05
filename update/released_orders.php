<?php
include("../config.php");
session_start();
if (isset($_POST)) {


    $user_id = $_POST['user_id'];
    $approved_order_status = 4;
    
    $allo_order_id = mysqli_real_escape_string($db, $_POST['allo_order_id']);

    $allo_vehicles = mysqli_real_escape_string($db, $_POST['allo_vehicles']);
    $allo_depot = mysqli_real_escape_string($db, $_POST['allo_depot']);
    $allo_order_description = mysqli_real_escape_string($db, $_POST['allo_order_description']);
    $user_id = mysqli_real_escape_string($db, $_POST['user_id']);

    $datetime = date('Y-m-d H:i:s');
    $val = '';

    if($approved_order_status==1){
        $val='Approved';
    }else if($approved_order_status==2){
        $val='Blocked';
    }else if($approved_order_status==3){
        $val='Special Approval';
    }else if($approved_order_status==4){
        $val='Released';
    }
    else if($approved_order_status==5){
        $val='Complete';
    }

    // echo 'HAmza';



    $query = "UPDATE `order_main` SET 
    `status`='$approved_order_status',
    `status_value` = '$val',
    `depot` = '$allo_depot',
    `tl_no` = '$allo_vehicles',
    `comment`='$allo_order_description',
    `approved_time`='$datetime' WHERE id=$allo_order_id";


    if (mysqli_query($db, $query)) {

       
        $log = "INSERT INTO `order_detail_log`
        (`order_id`,
        `status`,
        `status_value`,
        `description`,
        `created_at`,
        `created_by`)
        VALUES
        ('$allo_order_id',
        '$approved_order_status',
        '$val',
        '$allo_order_description',
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