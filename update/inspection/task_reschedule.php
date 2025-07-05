<?php
include("../../config.php");
session_start();
if (isset($_POST)) {


    $user_id = $_POST['user_id'];

    $task_id = $_POST['task_id'];
    $old_date = $_POST['old_date'];
    $new_date = $_POST['new_date'];
    $description = $_POST['description'];



    $datetime = date('Y-m-d H:i:s');

    // echo 'HAmza';

    $query = "UPDATE `inspector_task` SET 
    `time`='$new_date'
    WHERE id=$task_id";


    if (mysqli_query($db, $query)) {


        $log = "INSERT INTO `inspector_task_reschedule`
        (`task_id`,
        `old_time`,
        `new_time`,
        `description`,
        `created_at`,
        `created_by`)
        VALUES
        ('$task_id',
        '$old_date',
        '$new_date',
        '$description',
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