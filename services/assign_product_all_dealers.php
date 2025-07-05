<?php
//fetch.php  
include("../config.php");


$access_key = '03201232927';

$pass = $_GET["key"];
$date = date('Y-m-d H:i:s');
if ($pass != '') {
    if ($pass == $access_key) {
        $sql_query1 = "SELECT * FROM dealers where privilege='Dealer' order by id desc ;";

        $result1 = $db->query($sql_query1) or die("Error :" . mysqli_error());

        $thread = array();
        while ($user = $result1->fetch_assoc()) {

            $dealers_id = $user['id'];

            $sql_query = "SELECT * FROM all_products ;";

            $result = $db->query($sql_query) or die("Error :" . mysqli_error());


            while ($row = $result->fetch_assoc()) {

                $product = $row['name'];

                echo $dealers_id . ' - ' . $product . '<br>';

                if($product=='PMG'){
                    $query = "INSERT INTO `dealers_products`
                    (`dealer_id`,
                    `name`,
                    `from`,
                    `to`,
                    `indent_price`,
                    `nozel_price`,
                    `created_at`,
                    `update_time`,

                    `created_by`)
                    VALUES
                    ('$dealers_id',
                    '$product',
                    '2023-12-01T00:00',
                    '2023-12-15T23:59',
                    '275',
                    '283',
                    '$date',
                    '$date',
                    '1');";
                    mysqli_query($db, $query);

                }else{
                    $query = "INSERT INTO `dealers_products`
                    (`dealer_id`,
                    `name`,
                    `from`,
                    `to`,
                    `indent_price`,
                    `nozel_price`,
                    `created_at`,
                    `update_time`,

                    `created_by`)
                    VALUES
                    ('$dealers_id',
                    '$product',
                    '2023-12-01T00:00',
                    '2023-12-15T23:59',
                    '285',
                    '288',
                    '$date',
                    '$date',
                    '1');";
                    mysqli_query($db, $query);
                }

                


            }

        }


    } else {
        echo 'Wrong Key...';
    }

} else {
    echo 'Key is Required';
}


?>