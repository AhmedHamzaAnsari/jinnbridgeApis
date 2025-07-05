<?php
include("../config.php");
session_start();
if (isset($_POST)) {
    function send_email_app_order($order_id)
    {
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

    function push_order_emailer($order_id)
    {
        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_PORT => "8080",
            CURLOPT_URL => "http://110.38.69.114:5003/jinnbridgeApis/emailer/push_order_emailer.php?order_id=" . $order_id,
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
    $s_depot = mysqli_real_escape_string($db, $_POST['s_depot']);
    $sales_order_no = mysqli_real_escape_string($db, $_POST['sales_order_no']);
    // $s_depot = '';

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

    // echo 'HAmza';



    $query = "UPDATE `order_main` SET 
    `depot`='$s_depot',
    `status`='$approved_order_status',
    `status_value` = '$val',
    `SaleOrder` = '$sales_order_no',
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

            // if ($approved_order_status != 2) {

            //     update_legder($order_approval, $db, $user_id);
            // }

            // if ($approved_order_status == 5) {
            //     send_email_app_order($order_approval);

            // } else if ($approved_order_status == 1) {
            //     push_order_emailer($order_approval);

            // }

        } else {
            $output = 'Error' . mysqli_error($db) . '<br>' . $log;

        }

    } else {
        $output = 'Error' . mysqli_error($db) . '<br>' . $query;

    }




    echo $output;
}

function update_legder($order_id, $db, $user_id)
{

    $sql = "SELECT om.*,dl.id as dealer_id,dl.acount as old_legder FROM order_main as om 
    join dealers as dl on dl.sap_no=om.dealer_sap where om.id=$order_id";

    // echo $sql;

    $result = mysqli_query($db, $sql);
    $row = mysqli_fetch_array($result);

    $total_amount = $row['total_amount'];
    $dealer_sap = $row['dealer_sap'];
    $dealer_id = $row['dealer_id'];
    $old_legder = $row['old_legder'];
    $new_ledger = $row['old_legder'] - $total_amount;

    $query = "UPDATE `dealers` 
    SET `acount` = `acount` - $total_amount 
    WHERE `id` = '$dealer_id';";

    $nag = $total_amount;

    if (mysqli_query($db, $query)) {

        $datetime = date('Y-m-d H:i:s');
        $log = "INSERT INTO `dealer_ledger_log`
        (`dealer_id`,
        `type`,
        `old_ledger`,
        `new_ledger`,
        `datetime`,
        `description`,
        `created_at`,
        `created_by`)
        VALUES
        ('$dealer_id',
        'Order Transaction',
        '',
        '$nag',
        '$datetime',
        '',
        '$datetime',
        '$user_id');";
        if (mysqli_query($db, $log)) {
            $output = 1;

        } else {
            $output = 'Error' . mysqli_error($db) . '<br>' . $log;

        }

    } else {
        $output = 'Error' . mysqli_error($db) . '<br>' . $query;

    }
}
?>