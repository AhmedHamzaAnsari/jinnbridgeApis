<?php
include("../config.php");
session_start();
if (isset($_GET)) {
    $user_id = $_GET['dealer_id'];
    $datetime = date('Y-m-d H:i:s');

    $depot = $_GET["depot"];
    $type = $_GET["type"];
    $tl_no = $_GET["tl_no"];
    $total = $_GET["total"];
    $product = $_GET["product"];
    $legder_balance = $_GET["legder_balance"];
    // $product = '[{"p_id":"1","quantity":"25","indent_price":"292","product_name":"HSD","amount":7300},{"p_id":"2","quantity":"32","indent_price":"280","product_name":"PMG","amount":8960},{"p_id":"3","quantity":"12","indent_price":"300","product_name":"HOBC","amount":3600},{"p_id":"4","quantity":"25","indent_price":"305","product_name":"HOD","amount":7625},{"p_id":"5","quantity":"36","indent_price":"200","product_name":"KOC","amount":7200}]';

    if($total!=0){
        if ($_GET["row_id"] != '') {
    
    
        } else {
    

            $query_main = "INSERT INTO `order_datapool_main`
            (`depot`,
            `type`,
            `tl_no`,
            `total_amount`,
            `product_json`,
            `legder_balance`,
    
            `created_at`,
            `created_by`)
            VALUES
            ('$depot',
            '$type',
            '$tl_no',
            '$total',
            '$product',
            '$legder_balance',
            '$datetime',
            '$user_id');";
    
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
    
                        if($quantity>0){
                            $sql1 = "INSERT INTO `order_datapool_detail`
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
                            `vehicle`)
                            VALUES
                            ('$type',
                            '$quantity',
                            '$indent_price',
                            '$active',
                            '$depot',
                            '$datetime',
                            '$user_id',
                            '$product_id',
                            '$amount',
                            '0',
                            '$tl_no');";
        
                            if (mysqli_query($db, $sql1)) {
                                $output = 1;
        
                            }
    
                        }
    
                    }
                } else {
                    echo "Failed to decode JSON string.";
                }
    
    
    
    
            } else {
                $output = 'Error' . mysqli_error($db) . '<br>' . $query_main;
    
            }
        }

    }else{
        $output = 0;
    }




    echo $output;
}
?>