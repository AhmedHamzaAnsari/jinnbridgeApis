<?php
include("../../config.php");
session_start();
if (isset($_POST)) {
    $user_id = $_POST['user_id'];
    $name = mysqli_real_escape_string($db, $_POST["name"]);
    $ctn_sizes = mysqli_real_escape_string($db, $_POST["ctn_sizes"]);
    $pack_in_ctn = mysqli_real_escape_string($db, $_POST["pack_in_ctn"]);
    $date = date('Y-m-d H:i:s');

    // echo 'HAmza';
    if ($_POST["row_id"] != '') {


    } else {

        $query = "INSERT INTO `lubes_product_sizes`
        (`name`,
        `ctn_size`,
        `ctn_qty`,
        `created_at`,
        `created_by`)
        VALUES
        ('$name',
        '$ctn_sizes',
        '$pack_in_ctn',
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