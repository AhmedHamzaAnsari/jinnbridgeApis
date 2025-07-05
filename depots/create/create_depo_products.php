<?php
include ("../../config.php");
// session_start();


if (isset($_POST)) {
    $user_id = $_POST['user_id'];

    $depo_id = mysqli_real_escape_string($db, $_POST["dealer_id"]);

    $products_name = $_POST['products_name'];

    $products_description = $_POST['products_description'];

    $date = date('Y-m-d H:i:s');
    $output = '';
    // echo 'HAmza';
    if ($_POST["row_id"] != '') {

        $row_id = $_POST["row_id"];

        $query = "UPDATE `depo_products`
        SET 
        `from` = '$from_date',
        `to` = '$to_date',
        `indent_price` = '$indent_price',
        `nozel_price` = '$nozel_price',
        `update_time` = '$date'
        WHERE `id` = $row_id;";
        if (mysqli_query($db, $query)) {


            $output = 1;




        } else {
            $output = 'Error' . mysqli_error($db) . '<br>' . $query;

        }



    } else {



        $query = "INSERT INTO `depo_products`
        (`depo_id`,
        `name`,
        `created_at`,
        `update_time`,

        `created_by`)
        VALUES
        ('$depo_id',
        '$products_name',
        '$date',
        '$date',
        '$user_id');";
        if (mysqli_query($db, $query)) {

            $output = 1;



        } else {
            $output = 'Error' . mysqli_error($db) . '<br>' . $query;

        }





    }



    echo $output;
}
?>