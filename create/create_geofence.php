<?php
include("../config.php");
session_start();
if (isset($_POST)) {
    $user_id = $_POST['user_id'];
    $code = mysqli_real_escape_string($db, $_POST["code"]);
    $name = mysqli_real_escape_string($db, $_POST["name"]);
    $lati = mysqli_real_escape_string($db, $_POST["lati"]);
    $geotype = mysqli_real_escape_string($db, $_POST["geotype"]);
    $radius = mysqli_real_escape_string($db, $_POST["radius"]);
    $type = mysqli_real_escape_string($db, $_POST["type"]);
    $date = date('Y-m-d H:i:s');

    // echo 'HAmza';
    if ($_POST["row_id"] != '') {


    } else {

        $query = "INSERT INTO `geofenceing`
        (`code`,
        `consignee_name`,
        `Coordinates`,
        `radius`,
        `userid`,
        `type`,
        `geotype`)
        VALUES
        ('$code',
        '$name',
        '$lati',
        '$radius',
        '$user_id',
        '$type',
        '$geotype');";


        if (mysqli_query($db, $query)) {


            $output = 1;

        } else {
            $output = 'Error' . mysqli_error($db) . '<br>' . $query;

        }
    }



    echo $output;
}
?>