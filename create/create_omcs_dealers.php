<?php
include("../config.php");
session_start();
if (isset($_POST)) {

    $user_id = $_POST['user_id'];
    $omcs_id = mysqli_real_escape_string($db, $_POST["omcs_id"]);
    $name = mysqli_real_escape_string($db, $_POST["name"]);
    $coordinates = mysqli_real_escape_string($db, $_POST["coordinates"]);
    $old_dealer_id = mysqli_real_escape_string($db, $_POST["old_dealer_id"]);

    $date = date('Y-m-d H:i:s');

    // echo 'HAmza';
    if ($_POST["row_id"] != '') {


    } else {

        $query = "INSERT INTO `omcs_dealers`
        (`omcs_id`,
        `old_dealer_id`,
        `name`,
        `coordinates`,
        `created_at`,
        `created_by`)
        VALUES
        ('$omcs_id',
        '$old_dealer_id',
        '$name',
        '$coordinates',
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