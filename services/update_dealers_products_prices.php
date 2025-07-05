<?php
// fetch.php
include("../config.php");
set_time_limit(500); // Extend the script execution time
ini_set('max_execution_time', '0');
$url1 = $_SERVER['REQUEST_URI'];
header("Refresh: 60; URL=$url1");
$access_key = '03201232927';
$pass = $_GET["key"];
echo $date = date('Y-m-d H:i:s');

if (!empty($pass)) {
    // if (date('H:i') == '03:00') {
    if ($pass === $access_key) {
        $sql_query1 = "SELECT dl.id, dl.sap_no, ap.sap_no AS product_sap, pp.id AS dp_id,dl.rettype,dl.rettype_desc
            FROM dealers AS dl
            JOIN dealers_products AS pp ON pp.dealer_id = dl.id
            JOIN all_products AS ap ON ap.name = pp.name WHERE dl.indent_price = 1 and dl.rettype!='' order by dl.id asc";

        $result1 = $db->query($sql_query1) or die("Error: " . mysqli_error($db));

        while ($user = $result1->fetch_assoc()) {
            $dealer_id = $user['id'];
            $dealer_sap_no = $user['sap_no'];
            $product_sap = $user['product_sap'];
            $dp_id = $user['dp_id'];
            $rettype = $user['rettype'];
            $rettype_desc = $user['rettype_desc'];

            $sql_query2 = "SELECT * FROM dealers_jd_product_prices
                           WHERE dealer_sap = '$dealer_sap_no' 
                           AND product_sap = '$product_sap' 
                           AND status = 0 
                           ORDER BY id DESC 
                           LIMIT 1";

            $result2 = $db->query($sql_query2) or die("Error: " . mysqli_error($db));

            while ($user2 = $result2->fetch_assoc()) {
                $id = $user2['id'];
                $from = $user2['from'];
                $to = $user2['to'];
                $net_price = $user2['net_price'];
                $cartage = $user2['cartage'];
                $update = "";
                if ($rettype_desc == 'Retail/3rd Party Site         ') {
                    $update = "UPDATE dealers_products
                        SET
                        `from` = '$from',
                        `to` = '$to',
                        `indent_price` = '$net_price',
                        `nozel_price` = 0,
                        `freight_value` = '$cartage',
                        `update_time` = '$date'
                        WHERE `id` = '$dp_id' AND dealer_id = '$dealer_id'";
                } else if ($rettype_desc == 'COCO site                     ') {
                    $update = "UPDATE dealers_products
                        SET
                        `from` = '$from',
                        `to` = '$to',
                        `indent_price` = 0,
                        `nozel_price` = '$net_price',
                        `freight_value` = '$cartage',
                        `update_time` = '$date'
                        WHERE `id` = '$dp_id' AND dealer_id = '$dealer_id'";
                }



                if ($db->query($update)) {
                    $backlog = "INSERT INTO dealer_nozel_price_log
                                (`dealer_id`, `product_id`, `indent_price`, `nozel_price`, `freight_value`, `from`, `to`, `description`, `created_at`, `created_by`)
                                VALUES
                                ('$dealer_id', '$dp_id', 0, '$net_price', 0, '$from', '$to', '', '$date', '1')";

                    if ($db->query($backlog)) {
                        $jd_update = "UPDATE dealers_jd_product_prices
                                      SET `status` = 1
                                      WHERE `id` = '$id'";
                        $db->query($jd_update);

                        echo "COCO Price Update Successful<br>";
                        echo date('Y-m-d H:i:s') . "<br>";
                    } else {
                        echo "Error: " . $db->error . "<br>" . $backlog;
                    }
                } else {
                    echo "Error: " . $db->error . "<br>" . $update;
                }
            }
        }
        // } else {
        //     echo "Wrong Key...";
        // }
    } else {
        echo 'Current TIme ' . date('H:i');
    }


} else {
    echo "Key is Required";
}

?>

<h4>Product Price serice</h4>