<?php
include("../config.php");
session_start();
if (isset($_POST)) {
    $user_id = $_POST['user_id'];
    $dealer_id = mysqli_real_escape_string($db, $_POST["dealer_id"]);
    $name = mysqli_real_escape_string($db, $_POST["name"]);
    $nozzels_products = mysqli_real_escape_string($db, $_POST["nozzels_products"]);
    $product_tank = mysqli_real_escape_string($db, $_POST["product_tank"]);
    $product_dispenser = mysqli_real_escape_string($db, $_POST["product_dispenser"]);
    $last_reading = mysqli_real_escape_string($db, $_POST["last_reading"]);
    $date = date('Y-m-d H:i:s');

    // echo 'HAmza';
    if ($_POST["row_id"] != '') {


    } else {

        $query = "INSERT INTO `dealers_nozzel`
        (`dealer_id`,
        `name`,
        `tank_id`,
        `products`,
        `dispenser_id`,
        `created_at`,
        `created_by`)
        VALUES
        ('$dealer_id',
        '$name',
        '$product_tank',
        '$nozzels_products',
        '$product_dispenser',
        '$date',
        '$user_id');";


        if (mysqli_query($db, $query)) {
            $active = mysqli_insert_id($db);

            $query1 = "INSERT INTO `dealer_reconcilation`
            (`nozle_id`,
            `product_id`,
            `task_id`,
            `dispenser_id`,
            `dealer_id`,
            `old_reading`,
            `new_reading`,
            `created_at`,
            `created_by`)
            VALUES
            ('$active',
            '$product_tank',
            '',
            '$product_dispenser',
            '$dealer_id',
            '$last_reading',
            '$last_reading',
            '$date',
            '$user_id');";


            if (mysqli_query($db, $query1)) {


                $output = 1;

            } else {
                $output = 'Error' . mysqli_error($db) . '<br>' . $query;

            }

            // $output = 1;

        } else {
            $output = 'Error' . mysqli_error($db) . '<br>' . $query;

        }
    }



    echo $output;
}
?>