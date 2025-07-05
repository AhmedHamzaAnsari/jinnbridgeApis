<?php
include("../config.php");
session_start();
if (isset($_POST)) {
    $user_id = $_POST['user_id'];
    $dealer_id = mysqli_real_escape_string($db, $_POST["dealer_id"]);


    $comp_no = mysqli_real_escape_string($db, $_POST["comp_no"]);
    $comp_name = mysqli_real_escape_string($db, $_POST["comp_name"]);
    $comp_email = mysqli_real_escape_string($db, $_POST["comp_email"]);
    $comp_phone = mysqli_real_escape_string($db, $_POST["comp_phone"]);
    $comp_priority = mysqli_real_escape_string($db, $_POST["comp_priority"]);
    $comp_subject = mysqli_real_escape_string($db, $_POST["comp_subject"]);
    $comp_message = mysqli_real_escape_string($db, $_POST["comp_message"]);

    $date = date('Y-m-d H:i:s');

    // echo 'HAmza';
    if ($_POST["row_id"] != '') {


    } else {

        $query = "INSERT INTO `complaints`
        (`name`,
        `email`,
        `phone`,
        `priority`,
        `subject`,
        `message`,
        `created_at`,
        `created_by`,
        `complaint_no`)
        VALUES
        ('$comp_name',
        '$comp_email',
        '$comp_phone',
        '$comp_priority',
        '$comp_subject',
        '$comp_message',
        '$date',
        '$dealer_id',
        '$comp_no');";


        if (mysqli_query($db, $query)) {


            $output = 1;

        } else {
            $output = 'Error' . mysqli_error($db) . '<br>' . $query;

        }
    }



    echo $output;
}
?>