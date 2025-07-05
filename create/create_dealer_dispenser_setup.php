<?php
include("../config.php");
session_start();

if (isset($_POST)) {
    $user_id = mysqli_real_escape_string($db, $_POST["user_id"]);
    $response = $_POST["response"];
    $dealer_id = $_POST["dealer_id"];
    $no_dispensor = $_POST["no_dispensor"];
    $no_sub_motor = $_POST["no_sub_motor"];
    $no_fuel_system = $_POST["no_fuel_system"];
    $date = date('Y-m-d H:i:s');


    $datetime = date('Y-m-d H:i:s');

    $query_main = "INSERT INTO `dealer_dispenser_setup`
    (`dealer_id`,
    `no_dispensor`,
    `no_sub_motor`,
    `no_fuel_system`,
    `json_data`,
    `created_at`,
    `created_by`)
    VALUES
    ('$dealer_id',
    '$no_dispensor',
    '$no_sub_motor',
    '$no_fuel_system',
    '$response',
    '$date',
    '$user_id');";

    if (mysqli_query($db, $query_main)) {
       echo $output = 1;

    } else {
       echo $output = 0;
    }
}







?>