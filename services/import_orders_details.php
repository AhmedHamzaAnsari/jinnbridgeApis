<?php
//fetch.php  
include ("../config.php");
set_time_limit(500); // 

$url1 = $_SERVER['REQUEST_URI'];
header("Refresh: 120; URL=$url1");
$access_key = '03201232927';

$pass = $_GET["key"];
$date = date('Y-m-d H:i:s');
if ($pass != '') {
    if ($pass == $access_key) {
        $sql_query1 = "SELECT distinct(invoice) FROM order_info where status=0";

        $result1 = $db->query($sql_query1) or die("Error :" . mysqli_error());

        $thread = array();
        while ($user = $result1->fetch_assoc()) {

            $invoice = $user['invoice'];


            $sql_query2 = "SELECT oi.*,dc.id as vehicle_id,dc.name as dealer_name,dl.acount,dl.id as dealer_id FROM order_info as oi 
           left join devicesnew as dc on SUBSTRING_INDEX(dc.name, ' ', 1) = oi.vehicle 
            join dealers as dl on dl.sap_no=oi.customer_id where oi.invoice='$invoice'";

            $result2 = $db->query($sql_query2) or die("Error :" . mysqli_error());

            $first_id = '';
            $pre = 'Dealer';
            $i = 0;

            while ($user2 = $result2->fetch_assoc()) {

                $id = $user2['id'];
                $customer_id = $user2['customer_id'];
                $customer_name = $user2['customer_name'];
                $order_no = $user2['order_no'];
                $order_type = $user2['order_type'];
                $invoice = $user2['invoice'];
                $invoicetype = $user2['invoicetype'];
                $item = $user2['item'];
                $quantity = $user2['quantity'];
                $unitmeasure = $user2['unitmeasure'];
                $order_date = $user2['order_date'];
                $newDateString = date("Y-m-d", strtotime($order_date));
                $load_status = $user2['load_status'];
                $vehicle = $user2['vehicle'];
                $carrier_code = $user2['carrier_code'];
                $carrier_desc = $user2['carrier_desc'];
                $vehicle_id = $user2['vehicle_id'];
                $acount = $user2['acount'];
                $dealer_id = $user2['dealer_id'];


                $date = date('Y-m-d H:i:s');

                $insert = "INSERT INTO `order_main`
                (`depot`,
                `dealer_sap`,
                `type`,
                `tl_no`,
                `total_amount`,
                `legder_balance`,
                `product_json`,
                `created_at`,
                `created_by`,
                `user_id`)
                VALUES
                ('0',
                '$customer_id',
                '$order_type',
                '$vehicle_id',
                '',
                '$acount',
                '',
                '$date',
                '$dealer_id',
                '$dealer_id');";

                if (mysqli_query($db, $insert)) {
                    $first_id = mysqli_insert_id($db);

                    $sub = "INSERT INTO `order_detail`
                    (`main_id`,
                    `delivery_based`,
                    `rate`,
                    `quantity`,
                    `amount`,
                    `depot`,
                    `date`,
                    `cus_id`,
                    `product_type`,
                    `status`,
                    `created_by`,
                    `created_at`,
                    `vehicle`)
                    VALUES
                    ('$first_id',
                    '$order_type',
                    '',
                    '$quantity',
                    '',
                    '0',
                    '$newDateString',
                    '$dealer_id',
                    '0',
                    '0',
                    '$dealer_id',
                    '$date',
                    '$vehicle_id');";
                    if (mysqli_query($db, $sub)) {

                        $update = "UPDATE `order_info`
                        SET
                        `status` = '1'
                        WHERE `id` = '$id';";
                        mysqli_query($db, $update);

                    } else {
                        echo 'Error' . mysqli_error($db) . '<br>' . $sub;
                    }
                } else {
                    echo 'Error' . mysqli_error($db) . '<br>' . $insert;
                }



                // echo $output;

            }

        }


    } else {
        echo 'Wrong Key...';
    }

} else {
    echo 'Key is Required';
}


?>