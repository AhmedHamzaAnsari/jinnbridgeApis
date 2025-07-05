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
        $sql_query1 = "SELECT * FROM import_pmg";

        $result1 = $db->query($sql_query1) or die("Error :" . mysqli_error());

        $thread = array();
        while ($user = $result1->fetch_assoc()) {

            $Code = $user['JD Code'];
            $indent_price = $user['Indent Price'];
            $invoice_price = $user['Invoice Price'];
            $nozzle_price = $user['Nozzel Price'];


            $sql_query2 = "SELECT pd.* FROM dealers_products as pd 
            join dealers as dl on dl.id=pd.dealer_id where dl.sap_no='$Code' and pd.name = 'PMG'";

            $result2 = $db->query($sql_query2) or die("Error :" . mysqli_error());

            $first_id = '';
            $pre = 'Dealer';
            $i = 0;

            while ($user2 = $result2->fetch_assoc()) {

                $id = $user2['id'];
                $dealer_id = $user2['dealer_id'];
                $name = $user2['name'];


                $date = date('Y-m-d H:i:s');


                $update = "UPDATE `dealers_products`
                        SET
                        `from` = '2024-06-01 00:00:00',
                        `to` = '2024-06-15 23:59:59',
                        `indent_price` = '$indent_price',
                        `nozel_price` = '$nozzle_price',
                        `freight_value` = '$invoice_price',
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
                            '$indent_price',
                            '$nozzle_price',
                            '$invoice_price',
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



                echo $output;


            }

        }


    } else {
        echo 'Wrong Key...';
    }

} else {
    echo 'Key is Required';
}
echo 'PMG Price Update <br>';
echo date('Y-m-d H:i:s');

?>