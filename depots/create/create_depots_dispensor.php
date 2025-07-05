<?php
include("../../config.php");
session_start();
if (isset($_POST)) {
    $user_id = $_POST['user_id'];
    $dealer_id = mysqli_real_escape_string($db, $_POST["dealer_id"]);
    $name = mysqli_real_escape_string($db, $_POST["dispenser_name"]);
    $dispenser_description = mysqli_real_escape_string($db, $_POST["dispenser_description"]);
    $product_tank = mysqli_real_escape_string($db, $_POST["product_tank"]);
    $date = date('Y-m-d H:i:s');

    // echo 'HAmza';
    if ($_POST["row_id"] != '') {


    } else {

        $query = "INSERT INTO `depots_dispenser`
        (`depot_id`,
        `name`,
        `tank_id`,
        `description`,
        `created_at`,
        `created_by`)
        VALUES
        ('$dealer_id',
        '$name',
        '$product_tank',
        '$dispenser_description',
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