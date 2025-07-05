<?php
include("../../config.php");
session_start();
error_reporting(0);
if (isset($_GET)) {

    $sapNo = $_GET["sapNo"];
    $sapTime = $_GET['sapTime'];
    $vehicle = $_GET["vehicle"];
    $depo = $_GET["depo"];
    $customer = $_GET["customer"];
    $productDetail = $_GET["productDetail"];
    $driverName = $_GET["driverName"];
    $driverContact = $_GET["driverContact"];
    $driverCnic = $_GET["driverCnic"];


    $date = date('Y-m-d H:i:s');

    // echo 'HAmza';
    $sql = "SELECT * FROM devicesnew WHERE name = '$vehicle'";

    // echo $sql;

    $result = mysqli_query($db, $sql);
    $row = mysqli_fetch_array($result);

    $count = mysqli_num_rows($result);
    $is_tracker = 0;
    if ($count > 0) {
        $is_tracker = 1;
    } else {
        $is_tracker = 0;
    }

    $query = "INSERT INTO `puma_sap_data`
        (`sap_no`,
        `sap_time`,
        `vehicle`,
        `is_tracker`,
        `depo`,
        `customer`,
        `product_detail`,
        `driver_name`,
        `driver_contact`,
        `driver_cnic`,
        `created_at`)
        VALUES
        ('$sapNo',
        '$sapTime',
        '$vehicle',
        '$is_tracker',
        '$depo',
        '$customer',
        '$productDetail',
        '$driverName',
        '$driverContact',
        '$driverCnic',
        '$date');";


    if (mysqli_query($db, $query)) {


        $output = 'Record Created';

    } else {
        $output = 'Error' . mysqli_error($db) . '<br>' . $query;

    }




    echo $output;
}
?>