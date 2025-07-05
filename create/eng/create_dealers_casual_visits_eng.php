<?php
include("../../config.php");
session_start();
if (isset($_POST)) {
    $user_id = $_POST['user_id'];
    $dealer_id = mysqli_real_escape_string($db, $_POST["dealer_id"]);


    $description = mysqli_real_escape_string($db, $_POST["description"]);

    $date = date('Y-m-d H:i:s');

    // echo 'HAmza';
    if ($_POST["row_id"] != '') {


    } else {

        $query = "INSERT INTO `eng_dealer_casual_visits`
        (`dealer_id`,
        `users_id`,
        `visit_time`,
        `description`,
        `created_at`,
        `created_by`)
        VALUES
        ('$dealer_id',
        '$user_id',
        '$date',
        '$description',
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