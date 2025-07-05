<?php
include ("../../config.php");
session_start();
if (isset($_POST)) {
    $user_id = $_POST['user_id'];
    $datetime = date('Y-m-d H:i:s');
    $allo_depot = $_POST["allo_depot"];
    $products = $_POST['products'];
    $product_qtyS = $_POST['product_qtyS'];

    $tdate = date('Y-m-d H:i:s');
    $num = mt_rand(100000, 999999);


    if ($_POST["row_id"] != '') {
        // echo 'HAmza';


    } else {



        $query_main = "INSERT INTO `depots_order_main`
        (`depot_id`,
        `status`,
        `created_at`,
        `created_by`)
        VALUES
        ('$allo_depot',
        '0',
        '$tdate',
        '$user_id');";



        if (mysqli_query($db, $query_main)) {
            $active = mysqli_insert_id($db);


            $start_time = date("Y-m-d H:i:s");
            for ($i = 0; $i < count($products); $i++) {
                $product_id = $products[$i];
                $product_qty = $product_qtyS[$i];

                if ($product_qty > 0) {
                    $sql1 = "INSERT INTO `depots_order_sub`
                    (`main_id`,
                    `porduct_id`,
                    `qty`,
                    `status`,
                    `created_at`,
                    `created_by`)
                    VALUES
                    ('$active',
                    '$product_id',
                    '$product_qty',
                    '0',
                    '$tdate',
                    '$user_id');";

                    if (mysqli_query($db, $sql1)) {
                        $output = 1;



                    }

                }

            }



            $order_log = "INSERT INTO `depot_order_log`
            (`order_id`,
            `status`,
            `created_at`,
            `created_by`)
            VALUES
            ('$active',
            '0',
            '$tdate',
            '$user_id');";
            mysqli_query($db, $order_log);

        } else {
            $output = 'Error' . mysqli_error($db) . '<br>' . $query_main;

        }
    }



    echo $output;
}
?>