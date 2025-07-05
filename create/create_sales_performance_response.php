<?php
include("../config.php");
session_start();
if (isset($_POST)) {
    $user_id = $_POST['user_id'];
    $task_id = mysqli_real_escape_string($db, $_POST["task_id"]);
    $dealer_id = mysqli_real_escape_string($db, $_POST["dealer_id"]);
    $product_id = mysqli_real_escape_string($db, $_POST["product_id"]);
    $month_actual = mysqli_real_escape_string($db, $_POST["month_actual"]);
    $month_target = mysqli_real_escape_string($db, $_POST["month_target"]);
    $variance = mysqli_real_escape_string($db, $_POST["variance"]);
    $month_actual_last = mysqli_real_escape_string($db, $_POST["month_actual_last"]);
    $difference_volumn = mysqli_real_escape_string($db, $_POST["difference_volumn"]);
    $variance_percantage = mysqli_real_escape_string($db, $_POST["variance_percantage"]);
    $ytd_actual_last = mysqli_real_escape_string($db, $_POST["ytd_actual_last"]);
    $ytd_actual_current = mysqli_real_escape_string($db, $_POST["ytd_actual_current"]);
    $ytd_variance = mysqli_real_escape_string($db, $_POST["ytd_variance"]);
    $comment = mysqli_real_escape_string($db, $_POST["comment"]);
    $date = date('Y-m-d H:i:s');

    // echo 'HAmza';
    if ($_POST["row_id"] != '') {


    } else {

        $query = "INSERT INTO `dealer_sale_performance`
        (`task_id`,
        `dealer_id`,
        `product_id`,
        `month_actual`,
        `month_target`,
        `variance`,
        `month_actual_last`,
        `difference_volumn`,
        `variance_percantage`,
        `ytd_actual_last`,
        `ytd_actual_current`,
        `ytd_variance`,
        `comment`,
        `created_at`,
        `created_by`)
        VALUES
        ('$task_id',
        '$dealer_id',
        '$product_id',
        '$month_actual',
        '$month_target',
        '$variance',
        '$month_actual_last',
        '$difference_volumn',
        '$variance_percantage',
        '$ytd_actual_last',
        '$ytd_actual_current',
        '$ytd_variance',
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