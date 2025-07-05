<?php
include("../config.php");
session_start();
if (isset($_POST)) {
    $dealer_id = mysqli_real_escape_string($db, $_POST["dealer_id"]);
    $type = mysqli_real_escape_string($db, $_POST["type"]);
    $sm = mysqli_real_escape_string($db, $_POST["sm"]);
    $md = mysqli_real_escape_string($db, $_POST["md"]);
    $lg = mysqli_real_escape_string($db, $_POST["lg"]);
    $xl = mysqli_real_escape_string($db, $_POST["xl"]);

    $date = date('Y-m-d H:i:s');

    // echo 'HAmza';
    if ($_POST["row_id"] != '') {


    } else {

        $query = "INSERT INTO `uniform_order`
        (`type`,
        `sm`,
        `md`,
        `xl`,
        `lg`,
        `created_at`,
        `created_by`)
        VALUES
        ('$type',
        '$sm',
        '$md',
        '$lg',
        '$xl',
        '$date',
        '$dealer_id')";


        if (mysqli_query($db, $query)) {


            $output = 1;

        } else {
            $output = 'Error' . mysqli_error($db) . '<br>' . $query;

        }
    }



    echo $output;
}
?>