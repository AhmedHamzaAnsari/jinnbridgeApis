<?php
include ("../config.php");
session_start();
if (isset($_POST)) {
    function send_email($order_id) {
        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_PORT => "8080",
            CURLOPT_URL => "http://110.38.69.114:5003/jinnbridgeApis/emailer/order_emailer.php?order_id=" . $order_id,
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
    $dealer_id = $_POST['dealer_id'];
    $dealer_sap = $_POST['dealer_sap'];
    // $product_sap = $_POST['product_sap'];
    // $dealer_order_type = $_POST['dealer_order_type'];
    $datetime = date('Y-m-d H:i:s');

    $depot = $_POST["depot"];
    $type = $_POST["type"];
    $tl_no = $_POST["tl_no"];
    $total = $_POST["total"];
    $product = $_POST["product"];
    $legder_balance = $_POST["legder_balance"];
    // $product = '[{"p_id":"1","quantity":"25","indent_price":"292","product_name":"HSD","amount":7300},{"p_id":"2","quantity":"32","indent_price":"280","product_name":"PMG","amount":8960},{"p_id":"3","quantity":"12","indent_price":"300","product_name":"HOBC","amount":3600},{"p_id":"4","quantity":"25","indent_price":"305","product_name":"HOD","amount":7625},{"p_id":"5","quantity":"36","indent_price":"200","product_name":"KOC","amount":7200}]';

    if ($total != 0) {
        if ($_POST["row_id"] != '') {


        } else {


            $query_main = "INSERT INTO `order_main`
            (`depot`,
            `type`,
            `dealer_sap`,
            `tl_no`,
            `total_amount`,
            `product_json`,
            `legder_balance`,
            `created_at`,
            `user_id`,
            `created_by`)
            VALUES
            ('$depot',
            '$type',
            '$dealer_sap',
            '$tl_no',
            '$total',
            '$product',
            '$legder_balance',
            '$datetime',
            '$user_id',
            '$dealer_id');";

            if (mysqli_query($db, $query_main)) {
                $active = mysqli_insert_id($db);

                $dataArray = json_decode($product, true);

                // Check if the decoding was successful
                if (is_array($dataArray)) {
                    // Iterate through the array using a foreach loop
                    foreach ($dataArray as $item) {
                        // echo "Product ID: " . $item['p_id'] . "<br>";
                        // echo "Quantity: " . $item['quantity'] . "<br>";
                        // echo "Indent Price: " . $item['indent_price'] . "<br>";
                        // echo "Product Name: " . $item['product_name'] . "<br>";
                        // echo "Amount: " . $item['amount'] . "<br>";
                        // echo "<hr>";

                        $product_id = $item['p_id'];
                        $quantity = $item['quantity'];
                        $indent_price = $item['indent_price'];
                        $product_name = $item['product_name'];
                        $amount = $item['amount'];

                        if ($quantity > 0) {
                            $sql1 = "INSERT INTO `order_detail`
                            (`delivery_based`,
                            `quantity`,
                            `rate`,
                            `main_id`,
                            `depot`,
                            `date`,
                            `cus_id`,
                            `product_type`,
                            `amount`,
                            `status`,
                            `created_by`,
                            `vehicle`)
                            VALUES
                            ('$type',
                            '$quantity',
                            '$indent_price',
                            '$active',
                            '$depot',
                            '$datetime',
                            '$dealer_id',
                            '$product_id',
                            '$amount',
                            '0',
                            '$user_id',
                            '$tl_no');";

                            if (mysqli_query($db, $sql1)) {
                                $output = 1;

                            } else {
                                $output = 'Error' . mysqli_error($db) . '<br>' . $sql1;

                            }

                        }

                    }
                } else {
                    $output = "Failed to decode JSON string.";
                }

                send_email($active);


            } else {
                $output = 'Error' . mysqli_error($db) . '<br>' . $query_main;

            }
        }

    } else {
        $output = 0;
    }




    echo $output;

}
?>