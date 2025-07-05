<?php
include("../config.php");
session_start();
if (isset($_POST)) {
    $user_id = $_POST['user_id'];
    $datetime = date('Y-m-d H:i:s');
    $dealer_name = $_POST["dealer_name"];
    $emails = $_POST["emails"];
    $call_no = $_POST["call_no"];
    $location = $_POST["location"];
    $lati = $_POST["lati"];
    $housekeeping = $_POST["housekeeping"];
    $password = $_POST["password"];
    $type = $_POST["type"];
    $dealer_sap_no = $_POST['dealer_sap_no'];
    $account_balanced = $_POST["account_balanced"];
    // $carss = $_POST['depots'];
    $district = $_POST['district'];
    $city = $_POST['city'];
    $province = $_POST['province'];
    $region = $_POST['region'];


    $file = rand(1000, 100000) . "-" . $_FILES['banner_img']['name'];
    $file_loc = $_FILES['banner_img']['tmp_name'];
    $file_size = $_FILES['banner_img']['size'];
    //  $file_type = $_FILES['file']['type'];
    $folder = "../../jinnBridge_files/uploads/";
    move_uploaded_file($file_loc, $folder . $file);

    $file1 = rand(1000, 100000) . "-" . $_FILES['logo_img']['name'];
    $file_loc1 = $_FILES['logo_img']['tmp_name'];
    $file_size1 = $_FILES['logo_img']['size'];
    //  $file_type = $_FILES['file']['type'];
    $folder1 = "../../jinnBridge_files/uploads/";
    move_uploaded_file($file_loc1, $folder1 . $file1);

    $tdate = date('Y-m-d H:i:s');
    $num = mt_rand(100000, 999999);


    if ($_POST["row_id"] != '') {
        // echo 'HAmza';


    } else {



        $query_main = "INSERT INTO `depots`
            (`name`,
            `contact`,
            `email`,
            `password`,
            `location`,
            `co-ordinates`,
            `housekeeping`,
            `no_lorries`,
            `sap_no`,
            `type`,
            `zm`,
            `tm`,
            `asm`,
            `district`,
            `city`,
            `region`,
            `province`,
            `banner`,
            `logo`,
            `acount`,
            `created_at`,
            `created_by`)
            VALUES
            ('$dealer_name',
            '$call_no',
            '$emails',
            '$password',
            '$location',
            '$lati',
            '$housekeeping',
            '0',
            '$dealer_sap_no',
            '$type',
            '',
            '',
            '',
            '$district',
            '$city',
            '$region',
            '$province',
            '$file',
            '$file1',
            '$account_balanced',
            '$datetime',
            '$user_id');";



        if (mysqli_query($db, $query_main)) {
            $active = mysqli_insert_id($db);
            $output = 1;





        } else {
            $output = 'Error' . mysqli_error($db) . '<br>' . $query_main;

        }
    }



    echo $output;
}
?>