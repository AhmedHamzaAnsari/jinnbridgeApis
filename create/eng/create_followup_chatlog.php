<?php
include("../../config.php");
session_start();
if (isset($_POST)) {
    $followup_id = mysqli_real_escape_string($db, $_POST["followup_id"]);
    $user_id = mysqli_real_escape_string($db, $_POST["user_id"]);
    $message_des = mysqli_real_escape_string($db, $_POST["message_des"]);



    $date = date('Y-m-d H:i:s');

    // echo 'HAmza';
    if ($_POST["row_id"] != '') {


    } else {

        $query = "INSERT INTO `followup_catlog_eng`
        (`followup_id`,
        `user_id`,
        `description`,
        `created_at`,
        `created_by`)
        VALUES
        ('$followup_id',
        '$user_id',
        '$message_des',
        '$date',
        '$user_id');";

        if (mysqli_query($db, $query)) {


            $output = 1;

            $notification = "INSERT INTO `followup_notification_eng`
            (`followup_id`,
            `status`,
            `created_at`,
            `created_by`)
            VALUES
            ('$followup_id',
            '0',
            '$date',
            '$user_id');";
            mysqli_query($db, $notification);
        } else {
            $output = 'Error' . mysqli_error($db) . '<br>' . $query;

        }
    }



    echo $output;
}
?>