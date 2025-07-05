<?php
include("../config.php");
session_start();
if (isset($_POST)) {
    $datetime = date('Y-m-d H:i:s');

    $dealer_id = $_POST['dealer_id'];
    $order_id = $_POST["order_id"];
    $invoice_no = $_POST["invoice_no"];
    $product_json = $_POST["product_json"];
   

    $file = rand(1000, 100000) . "-" . $_FILES['file']['name'];
    $file_loc = $_FILES['file']['tmp_name'];
    $file_size = $_FILES['file']['size'];
    //  $file_type = $_FILES['file']['type'];
    $folder = "../../jinnBridge_files/uploads/";
    move_uploaded_file($file_loc, $folder . $file);

    $tdate = date('Y-m-d H:i:s');
    $num = mt_rand(100000, 999999);

    $file_sign = rand(1000, 100000) . "-" . $_FILES['file_sign']['name'];
    $file_loc_sign = $_FILES['file_sign']['tmp_name'];
    $file_size_sign = $_FILES['file_sign']['size'];
    //  $file_type = $_FILES['file']['type'];

    move_uploaded_file($file_loc_sign, $folder . $file_sign);

    $dealer_sign_file = rand(1000, 100000) . "-" . $_FILES['dealer_sign']['name'];
    $file_loc_dealer_sign_file = $_FILES['dealer_sign']['tmp_name'];
    $file_size_dealer_sign_file = $_FILES['dealer_sign']['size'];
    //  $file_type = $_FILES['file']['type'];

    move_uploaded_file($file_loc_dealer_sign_file, $folder . $dealer_sign_file);



    // echo 'HAmza';
    if ($_POST["row_id"] != '') {


    } else {



        $query_main = "INSERT INTO `order_shortage`
        (`order_id`,
        `invoice_no`,
        `file`,
        `product_json`,
        `sign`,
        `dealer_sign`,
        `created_at`,
        `created_by`)
        VALUES
        ('$order_id',
        '$invoice_no',
        '$file',
        '$product_json',
        '$file_sign',
        '$dealer_sign_file',
        '$datetime',
        '$dealer_id');";



        if (mysqli_query($db, $query_main)) {
            $output = 1;
            
        } else {
            $output = 'Error' . mysqli_error($db) . '<br>' . $query_main;

        }
    }



    echo $output;
}
?>