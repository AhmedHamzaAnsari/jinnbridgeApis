<?php
//fetch.php  
include ("../config.php");
set_time_limit(500); // 

// $url1 = $_SERVER['REQUEST_URI'];
// header("Refresh: 120; URL=$url1");
$access_key = '03201232927';

$pass = $_GET["key"];
$date = date('Y-m-d H:i:s');
if ($pass != '') {
    if ($pass == $access_key) {
        $sql_query1 = "SELECT * FROM import_coco";

        $result1 = $db->query($sql_query1) or die("Error :" . mysqli_error());

        $thread = array();
        while ($user = $result1->fetch_assoc()) {

            $Code = $user['Code'];
            $PMG = $user['PMG'];
            $HSD = $user['HSD'];
            $HASRON = $user['HASRON'];


            $sql_query2 = "SELECT pd.* FROM dealers_products as pd 
            join dealers as dl on dl.id=pd.dealer_id where dl.sap_no='$Code'";

            $result2 = $db->query($sql_query2) or die("Error :" . mysqli_error());

            $first_id = '';
            $pre = 'Dealer';
            $i = 0;

            while ($user2 = $result2->fetch_assoc()) {

                $id = $user2['id'];
                $dealer_id = $user2['dealer_id'];
                $name = $user2['name'];


                $date = date('Y-m-d H:i:s');

                if ($name == 'PMG') {
                    $update = "UPDATE `dealers_products`
                        SET
                        `from` = '2024-06-01 00:00:00',
                        `to` = '2024-06-15 23:59:59',
                        `indent_price` = 0,
                        `nozel_price` = '$PMG',
                        `freight_value` = 0,
                        `update_time` = '$date'
                        WHERE `id` = '$id' and name='$name';";
                    if (mysqli_query($db, $update)) {
                        $backlog = "INSERT INTO `dealer_nozel_price_log`
                            (`dealer_id`,
                            `product_id`,
                            `indent_price`,
                            `nozel_price`,
                            `freight_value`,
                            `from`,
                            `to`,
                            `description`,
                            `created_at`,
                            `created_by`)
                            VALUES
                            ('$dealer_id',
                            '$id',
                            '0',
                            '$PMG',
                            '0',
                            '2024-06-01 00:00:00',
                            '2024-06-15 23:59:59',
                            '',
                            '$date',
                            '1');";
                        if (mysqli_query($db, $backlog)) {
                            $output = 1;

                        } else {

                            $output = 'Error' . mysqli_error($db) . '<br>' . $backlog;
                        }


                    }

                } else if ($name == 'HSD') {
                    $update = "UPDATE `dealers_products`
                        SET
                        `from` = '2024-06-01 00:00:00',
                        `to` = '2024-06-15 23:59:59',
                        `indent_price` = 0,
                        `nozel_price` = '$HSD',
                        `freight_value` = 0,
                        `update_time` = '$date'
                        WHERE `id` = '$id' and name='$name';";
                    if (mysqli_query($db, $update)) {
                        $backlog = "INSERT INTO `dealer_nozel_price_log`
                            (`dealer_id`,
                            `product_id`,
                            `indent_price`,
                            `nozel_price`,
                            `freight_value`,
                            `from`,
                            `to`,
                            `description`,
                            `created_at`,
                            `created_by`)
                            VALUES
                            ('$dealer_id',
                            '$id',
                            '0',
                            '$HSD',
                            '0',
                            '2024-06-01 00:00:00',
                            '2024-06-15 23:59:59',
                            '',
                            '$date',
                            '1');";
                        if (mysqli_query($db, $backlog)) {
                            $output = 1;

                        } else {

                            $output = 'Error' . mysqli_error($db) . '<br>' . $backlog;
                        }


                    }
                } else if ($name == 'HASRON') {
                    $update = "UPDATE `dealers_products`
                        SET
                        `from` = '2024-06-01 00:00:00',
                        `to` = '2024-06-15 23:59:59',
                        `indent_price` = 0,
                        `nozel_price` = '$HASRON',
                        `freight_value` = 0,
                        `update_time` = '$date'
                        WHERE `id` = '$id' and name='$name';";
                    if (mysqli_query($db, $update)) {
                        $backlog = "INSERT INTO `dealer_nozel_price_log`
                            (`dealer_id`,
                            `product_id`,
                            `indent_price`,
                            `nozel_price`,
                            `freight_value`,
                            `from`,
                            `to`,
                            `description`,
                            `created_at`,
                            `created_by`)
                            VALUES
                            ('$dealer_id',
                            '$id',
                            '0',
                            '$HASRON',
                            '0',
                            '2024-06-01 00:00:00',
                            '2024-06-15 23:59:59',
                            '',
                            '$date',
                            '1');";
                        if (mysqli_query($db, $backlog)) {
                            $output = 1;

                        } else {

                            $output = 'Error' . mysqli_error($db) . '<br>' . $backlog;
                        }


                    }
                }


               



                echo $output;
                echo 'COCO Price Update <br>';
                echo date('Y-m-d H:i:s');

            }

        }


    } else {
        echo 'Wrong Key...';
    }

} else {
    echo 'Key is Required';
}


?>