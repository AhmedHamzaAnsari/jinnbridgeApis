<?php
include("../config.php");
// session_start();


if (isset($_POST)) {
    $user_id = $_POST['user_id'];

    $dealer_id = mysqli_real_escape_string($db, $_POST["dealer_id"]);

    $products_name = $_POST['products_name'];
    $from_date = $_POST['from_date'];
    $to_date = $_POST['to_date'];
    $indent_price = $_POST['indent_price'];
    $nozel_price = $_POST['nozel_price'];
    $products_description = $_POST['products_description'];

    $date = date('Y-m-d H:i:s');
    $output = '';
    // echo 'HAmza';
    if ($_POST["row_id"] != '') {

        $row_id = $_POST["row_id"];

        $query = "UPDATE `dealers_products`
        SET 
        `from` = '$from_date',
        `to` = '$to_date',
        `indent_price` = '$indent_price',
        `nozel_price` = '$nozel_price',
        `update_time` = '$date'
        WHERE `id` = $row_id;";
        if (mysqli_query($db, $query)) {

            $backlog = "INSERT INTO `dealer_nozel_price_log`
            (`dealer_id`,
            `product_id`,
            `indent_price`,
            `nozel_price`,
            `from`,
            `to`,
            `description`,

            `created_at`,
            `created_by`)
            VALUES
            ('$dealer_id',
            '$row_id',
            '$indent_price',
            '$nozel_price',
            '$from_date',
            '$to_date',
            '$products_description',
            '$date',
            '$user_id');";
            if (mysqli_query($db, $backlog)) {
                $output = 1;

            }
            else{

                $output = 'Error' . mysqli_error($db) . '<br>' . $backlog;
            }


        } else {
            $output = 'Error' . mysqli_error($db) . '<br>' . $query;

        }



    } else {



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
        ('$dealer_id',
        '$products_name',
        '$from_date',
        '$to_date',
        '$indent_price',
        '$nozel_price',
        '$date',
        '$date',
        '$user_id');";
        if (mysqli_query($db, $query)) {
            $lastInsertedId = mysqli_insert_id($db);


            $backlog = "INSERT INTO `dealer_nozel_price_log`
            (`dealer_id`,
            `product_id`,
            `indent_price`,
            `nozel_price`,
            `from`,
            `to`,
            `description`,

            `created_at`,
            `created_by`)
            VALUES
            ('$dealer_id',
            '$lastInsertedId',
            '$indent_price',
            '$nozel_price',
            '$from_date',
            '$to_date',
            '$products_description',
            '$date',
            '$user_id');";
            if (mysqli_query($db, $backlog)) {
                $output = 1;

            }
            else{

                $output = 'Error' . mysqli_error($db) . '<br>' . $backlog;
            }


        } else {
            $output = 'Error' . mysqli_error($db) . '<br>' . $query;

        }





    }



    echo $output;
}
?>