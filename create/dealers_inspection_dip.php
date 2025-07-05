<?php
include("../config.php");
session_start();
if (isset($_POST)) {
    $user_id = $_POST['user_id'];
    $dealer_id = $_POST['dealer_id'];
    $task_id = $_POST['task_id'];
    $product_id = $_POST['product_id'];
    $dip_new = $_POST['dip_new'];
    $dip_old = $_POST['dip_old'];
    $tank_id = $_POST['tank_id'];




    $datetime = date('Y-m-d H:i:s');


    // echo 'HAmza';
    if ($_POST["row_id"] != '') {


    } else {

        $query = "INSERT INTO `dealer_wet_stock`
        (`dealer_id`,
        `tank_id`,
        `task_id`,
        `product_id`,
        `dip_old`,
        `dip_new`,
        `created_at`,
        `created_by`)
        VALUES
        ('$dealer_id',
        '$tank_id',
        '$task_id',
        '$product_id',
        '$dip_old',
        '$dip_new',
        '$datetime',
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