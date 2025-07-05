<?php
include("../config.php");
session_start();
if (isset($_POST)) {
    $user_id = $_POST['user_id'];
    $dealer_id = mysqli_real_escape_string($db, $_POST["dealer_id"]);
    $product_id = mysqli_real_escape_string($db, $_POST["product_id"]);
    $monthly_target = mysqli_real_escape_string($db, $_POST["monthly_target"]);
    $target_achived = mysqli_real_escape_string($db, $_POST["target_achived"]);
    $differnce = mysqli_real_escape_string($db, $_POST["differnce"]);
    $task_id = mysqli_real_escape_string($db, $_POST["task_id"]);
    $reason = mysqli_real_escape_string($db, $_POST["reason"]);
    $date = date('Y-m-d H:i:s');

    // echo 'HAmza';
    if ($_POST["row_id"] != '') {


    } else {

        $query = "INSERT INTO `dealer_target_response_return`
        (`dealer_id`,
        `task_id`,
        `product_id`,
        `monthly_target`,
        `target_achived`,
        `differnce`,
        `reason`,
        `created_at`,
        `created_by`)
        VALUES
        ('$dealer_id',
        '$task_id',
        '$product_id',
        '$monthly_target',
        '$target_achived',
        '$differnce',
        '$reason',
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