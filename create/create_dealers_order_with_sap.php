<?php
include("../config.php");
session_start();
if (isset($_POST)) {
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

                            }

                        }

                    }
                } else {
                    echo "Failed to decode JSON string.";
                }


                data_customer($active,$product);

            } else {
                $output = 'Error' . mysqli_error($db) . '<br>' . $query_main;

            }
        }

    } else {
        $output = 0;
    }




    echo $output;

    function data_customer($order_id,$product)
    {


        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://103.111.160.120:44300/sap/opu/odata/sap/ZP2P_TRACK_PROJ_SRV/InitialSet4(CustomerId=\'203000044\',OrderType=\'EX\',LineItem=\'000010\',Material=\'2\',Quantity=10)',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => array(
                'Authorization: Basic dG1jX2FzaW06dG1jQDEyMw==',
                'Cookie: SAP_SESSIONID_DEV_200=BuAjI9hdAhLBCEmDfya0cjQONsHMwxHut-IAUFa6fv0%3d; sap-usercontext=sap-client=200'
            ),
        )
        );

        $response = curl_exec($curl);

        curl_close($curl);
        echo $response;

    }
}
?>