<?php
include("../../config.php");
session_start();
if (isset($_POST)) {


    $user_id = $_POST['user_id'];

    $task_id = $_POST['task_id'];
    $status = $_POST['status'];
    $reason = $_POST['description'];
    $reason = str_replace("'", '', $reason);

    $file1 = rand(1000, 100000) . "-" . $_FILES['dealer_sign']['name'];
    $file_loc1 = $_FILES['dealer_sign']['tmp_name'];
    $file_size1 = $_FILES['dealer_sign']['size'];
    //  $file_type = $_FILES['file']['type'];
    $folder1 = "../../../jinnBridge_files/uploads/";
    move_uploaded_file($file_loc1, $folder1 . $file1);




    $datetime = date('Y-m-d H:i:s');

    // echo 'HAmza';

    $query = "UPDATE `eng_inspector_task` SET 
    `status`='$status'
    WHERE id=$task_id";


    if (mysqli_query($db, $query)) {


        $log = "INSERT INTO `inspector_task_response_eng`
        (`task_id`,
        `status`,
        `description`,
        `dealer_sign`,
        `representator_sign`,
        `created_at`,
        `created_by`)
        VALUES
        ('$task_id',
        '$status',
        '$reason',
        '$file1',
        '',
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