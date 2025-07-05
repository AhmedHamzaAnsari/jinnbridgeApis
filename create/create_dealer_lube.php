<?php
include("../config.php");
session_start();
if (isset($_POST)) {
    $dealer_id = mysqli_real_escape_string($db, $_POST["dealer_id"]);
    $grade_id = mysqli_real_escape_string($db, $_POST["grade_id"]);
    $code = mysqli_real_escape_string($db, $_POST["code"]);
    $pack_size = mysqli_real_escape_string($db, $_POST["pack_size"]);
    $ctn_size = mysqli_real_escape_string($db, $_POST["ctn_size"]);
    $pack_ctn = mysqli_real_escape_string($db, $_POST["pack_ctn"]);
    $total_pack = mysqli_real_escape_string($db, $_POST["total_pack"]);
    $total_order = mysqli_real_escape_string($db, $_POST["total_order"]);

    $date = date('Y-m-d H:i:s');

    // echo 'HAmza';
    if ($_POST["row_id"] != '') {


    } else {

        $query = "INSERT INTO `lube_order`
        (`grade_id`,
        `code`,
        `pack_size`,
        `ctn_size`,
        `pack_ctn`,
        `total_pack`,
        `total_order`,
        `created_at`,
        `created_by`)
        VALUES
        ('$grade_id',
        '$code',
        '$pack_size',
        '$ctn_size',
        '$pack_ctn',
        '$total_pack',
        '$total_order',
        '$date',
        '$dealer_id');";


        if (mysqli_query($db, $query)) {


            $output = 1;

        } else {
            $output = 'Error' . mysqli_error($db) . '<br>' . $query;

        }
    }



    echo $output;
}
?>