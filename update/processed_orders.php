<?php
include ("../config.php");
session_start();
if (isset($_POST)) {

    function send_email($order_id) {
        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_PORT => "8080",
            CURLOPT_URL => "http://110.38.69.114:5003/jinnbridgeApis/emailer/tm_proceed_emailer.php?order_id=" . $order_id,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => [
                "Accept: */*",
                "User-Agent: Thunder Client (https://www.thunderclient.com)"
            ],
        ]);

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
            // echo "cURL Error #: " . $err;
        } else {
            // echo $response;
        }
    }

    function send_email_app_order($order_id) {
        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_PORT => "8080",
            CURLOPT_URL => "http://110.38.69.114:5003/jinnbridgeApis/emailer/opp_order_emailer.php?order_id=" . $order_id,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => [
                "Accept: */*",
                "User-Agent: Thunder Client (https://www.thunderclient.com)"
            ],
        ]);

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
            // echo "cURL Error #: " . $err;
        } else {
            // echo $response;
        }
    }

    $user_id = $_POST['user_id'];
    $order_approval = $_POST['order_approval'];
    $approved_order_status = mysqli_real_escape_string($db, $_POST['approved_order_status']);
    // $s_depot = mysqli_real_escape_string($db, $_POST['s_depot']);
    $s_depot = '';

    $approved_order_description = mysqli_real_escape_string($db, $_POST['approved_order_description']);
    $datetime = date('Y-m-d H:i:s');
    $val = '';

    if ($approved_order_status == 1) {
        $val = 'Pushed';
    } else if ($approved_order_status == 2) {
        $val = 'Cancelled';
    } else if ($approved_order_status == 3) {
        $val = 'Special Approval';
    } else if ($approved_order_status == 4) {
        $val = 'Released';
    } else if ($approved_order_status == 5) {
        $val = 'Forwarded';
    }
    else if ($approved_order_status == 6) {
        $val = 'Processed';
    }

    // echo 'HAmza';



    $query = "UPDATE `order_main` SET 
    `status`='$approved_order_status',
    `status_value` = '$val',
    `comment`='$approved_order_description',
    `approved_time`='$datetime' WHERE id=$order_approval";


    if (mysqli_query($db, $query)) {


        $log = "INSERT INTO `order_detail_log`
        (`order_id`,
        `status`,
        `status_value`,
        `description`,
        `created_at`,
        `created_by`)
        VALUES
        ('$order_approval',
        '$approved_order_status',
        '$val',
        '$approved_order_description',
        '$datetime',
        '$user_id');";
        if (mysqli_query($db, $log)) {
            $output = 1;

            if ($approved_order_status == 5) {
                send_email_app_order($order_approval);

            }
            else if ($approved_order_status == 6) {
                
                send_email($order_approval);
            }

        } else {
            $output = 'Error' . mysqli_error($db) . '<br>' . $log;

        }

    } else {
        $output = 'Error' . mysqli_error($db) . '<br>' . $query;

    }




    echo $output;
}


?>