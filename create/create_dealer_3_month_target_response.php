<?php
include("../config.php");
session_start();
if (isset($_POST)) {
    $user_id = $_POST['user_id'];
    $task_id = mysqli_real_escape_string($db, $_POST["task_id"]);
    $dealer_id = mysqli_real_escape_string($db, $_POST["dealer_id"]);
    $product_id = mysqli_real_escape_string($db, $_POST["product_id"]);
    $month_1 = mysqli_real_escape_string($db, $_POST["month_1"]);
    $month_2 = mysqli_real_escape_string($db, $_POST["month_2"]);
    $month_3 = mysqli_real_escape_string($db, $_POST["month_3"]);
    $month_target_1 = mysqli_real_escape_string($db, $_POST["month_target_1"]);
    $month_target_2 = mysqli_real_escape_string($db, $_POST["month_target_2"]);
    $month_target_3 = mysqli_real_escape_string($db, $_POST["month_target_3"]);
    $comment = mysqli_real_escape_string($db, $_POST["comment"]);
    $date = date('Y-m-d H:i:s');

    // echo 'HAmza';
    if ($_POST["row_id"] != '') {


    } else {

        $query = "INSERT INTO `dealer_3_month_target`
        (`task_id`,
        `dealer_id`,
        `product_id`,
        `month_1`,
        `month_2`,
        `month_3`,
        `month_target_1`,
        `month_target_2`,
        `month_target_3`,
        `comment`,
        `created_at`,
        `created_by`)
        VALUES
        ('$task_id',
        '$dealer_id',
        '$product_id',
        '$month_1',
        '$month_2',
        '$month_3',
        '$month_target_1',
        '$month_target_2',
        '$month_target_2',
        '$comment',
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