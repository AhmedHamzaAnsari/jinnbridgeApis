<?php
include("../config.php");
session_start();
if (isset($_POST)) {
    $user_id = $_POST['user_id'];
    $nozle_id = mysqli_real_escape_string($db, $_POST["nozle_id"]);
    $product_id = mysqli_real_escape_string($db, $_POST["product_id"]);
    $old_reading = mysqli_real_escape_string($db, $_POST["old_reading"]);
    $new_reading = mysqli_real_escape_string($db, $_POST["new_reading"]);
    $task_id = mysqli_real_escape_string($db, $_POST["task_id"]);
    $dealer_id = mysqli_real_escape_string($db, $_POST["dealer_id"]);
    $dispenser_id = mysqli_real_escape_string($db, $_POST["dispenser_id"]);

    $date = date('Y-m-d H:i:s');

    // echo 'HAmza';
    if ($_POST["row_id"] != '') {


    } else {

        $query = "INSERT INTO `dealer_reconcilation`
        (`nozle_id`,
        `product_id`,
        `task_id`,
        `dispenser_id`,
        `dealer_id`,
        `old_reading`,
        `new_reading`,
        `created_at`,
        `created_by`)
        VALUES
        ('$nozle_id',
        '$product_id',
        '$task_id',
        '$dispenser_id',
        '$dealer_id',
        '$old_reading',
        '$new_reading',
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