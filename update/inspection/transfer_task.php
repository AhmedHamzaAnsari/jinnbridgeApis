<?php
include("../../config.php");
session_start();
if (isset($_POST)) {


    $user_id = $_POST['user_id'];

    $task_id = $_POST['task_id'];
    $transfer_to = $_POST['transfer_to'];
    $reason = $_POST['reason'];



    $datetime = date('Y-m-d H:i:s');

    // echo 'HAmza';

    $query = "UPDATE `inspector_task` SET 
    `user_id`='$transfer_to'
    WHERE id=$task_id";


    if (mysqli_query($db, $query)) {


        $log = "INSERT INTO `inspector_task_transfer_log`
        (`task_id`,
        `tranfer_from`,
        `transfer_to`,
        `reason`,
        `created_at`,
        `created_by`)
        VALUES
        ('$task_id',
        '$user_id',
        '$transfer_to',
        '$reason',
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