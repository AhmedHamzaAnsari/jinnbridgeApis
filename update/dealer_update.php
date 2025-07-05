<?php
include("../config.php");
session_start();

if (!empty($_POST)) {
    $logo = "";
    $banner = "";
    
    // Secure inputs
    $logo_img_hidden = mysqli_real_escape_string($db, $_POST['logo_img_hidden']);
    $banner_img_hidden = mysqli_real_escape_string($db, $_POST['banner_img_hidden']);
    $dealer_id = mysqli_real_escape_string($db, $_POST['row_id']);
    $user_id = mysqli_real_escape_string($db, $_POST['user_id']);
    $dealer_name = mysqli_real_escape_string($db, $_POST['dealer_name']);
    $dealer_sap_no = mysqli_real_escape_string($db, $_POST['dealer_sap_no']);
    $emails = mysqli_real_escape_string($db, $_POST['emails']);
    $password = mysqli_real_escape_string($db, $_POST['password']); // Consider hashing this
    $call_no = mysqli_real_escape_string($db, $_POST['call_no']);
    $location = mysqli_real_escape_string($db, $_POST['location']);
    $lati = mysqli_real_escape_string($db, $_POST['lati']);
    $account_balanced = mysqli_real_escape_string($db, $_POST['account_balanced']);
    $housekeeping = mysqli_real_escape_string($db, $_POST['housekeeping']);
    $zm = mysqli_real_escape_string($db, $_POST['zm']);
    $tm = mysqli_real_escape_string($db, $_POST['tm']);
    $asm = mysqli_real_escape_string($db, $_POST['asm']);
    $depots = $_POST['depots']; // Assuming depots is an array
    $district = mysqli_real_escape_string($db, $_POST['district']);
    $city = mysqli_real_escape_string($db, $_POST['city']);
    $province = mysqli_real_escape_string($db, $_POST['province']);
    $region = mysqli_real_escape_string($db, $_POST['region']);

    // Handle banner image
    $bannercheck = $_FILES['banner_img']['name'];
    if ($bannercheck == "") {
        $banner = $banner_img_hidden;
    } else {
        $banner = rand(1000, 100000) . "-" . $_FILES['banner_img']['name'];
        $file_loc = $_FILES['banner_img']['tmp_name'];
        $folder = "../../jinnBridge_files/uploads/";
        if (move_uploaded_file($file_loc, $folder . $banner)) {
            // Successfully uploaded banner
        } else {
            // Handle upload error
            $banner = $banner_img_hidden;
        }
    }

    // Handle logo image
    $logocheck = $_FILES['logo_img']['name'];
    if ($logocheck == "") {
        $logo = $logo_img_hidden;
    } else {
        $logo = rand(1000, 100000) . "-" . $_FILES['logo_img']['name'];
        $file_loc1 = $_FILES['logo_img']['tmp_name'];
        $folder1 = "../../jinnBridge_files/uploads/";
        if (move_uploaded_file($file_loc1, $folder1 . $logo)) {
            // Successfully uploaded logo
        } else {
            // Handle upload error
            $logo = $logo_img_hidden;
        }
    }

    // Update dealer details
    $query = "UPDATE `dealers` SET 
        `name`='$dealer_name',
        `sap_no`='$dealer_sap_no',
        `contact`='$call_no',
        `email`='$emails',
        `password`='$password', 
        `location`='$location',
        `co-ordinates`='$lati',
        `housekeeping`='$housekeeping',
        `zm`='$zm',
        `tm`='$tm',
        `asm`='$asm',
        `district`='$district',
        `city`='$city',
        `region`='$region',
        `province`='$province',
        `banner`='$banner',
        `logo`='$logo',
        `acount`='$account_balanced'
        WHERE id='$dealer_id'";

    $start_time = date("Y-m-d H:i:s");
    $output = '';

    if (mysqli_query($db, $query)) {
        if (!empty($depots)) {
            $delete_depot = "DELETE FROM `dealers_depots` WHERE dealers_id='$dealer_id'";
            mysqli_query($db, $delete_depot);

            foreach ($depots as $assign) {
                $sql1 = "INSERT INTO `dealers_depots`
                    (`dealers_id`, `depot_id`, `created_at`, `created_by`)
                    VALUES
                    ('$dealer_id', '$assign', '$start_time', '$user_id')";

                if (mysqli_query($db, $sql1)) {
                    $output = 1;
                } else {
                    $output = 'Error: ' . mysqli_error($db) . '<br>' . $sql1;
                }
            }
        } else {
            $output = 1;
        }
    } else {
        $output = 'Error: ' . mysqli_error($db) . '<br>' . $query;
    }

    echo $output;
}
?>
