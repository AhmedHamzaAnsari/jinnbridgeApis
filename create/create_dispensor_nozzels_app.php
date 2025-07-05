<?php
include ("../config.php");
session_start();

if (isset($_POST)) {
    $user_id = $_POST['user_id'];
    $dealer_id = mysqli_real_escape_string($db, $_POST["dealer_id"]);
    $dispenser_name = mysqli_real_escape_string($db, $_POST["dispenser_name"]);
    $date = date('Y-m-d H:i:s');
    $response = $_POST["data_arr"];


    $query_main = "INSERT INTO `dealers_dispenser`
    (`dealer_id`,
    `name`,
    `description`,
    `created_at`,
    `created_by`)
    VALUES
    ('$dealer_id',
    '$dispenser_name',
    '',
    '$date',
    '$user_id');";

    if (mysqli_query($db, $query_main)) {
        $active = mysqli_insert_id($db);
        $dataArray = json_decode($response, true);

        if (is_array($dataArray)) {
            // Iterate through the array using a foreach loop
            foreach ($dataArray as $item) {

                $name = $item['name'];
                $nozzels_products = $item['nozzels_products'];
                $product_tank = $item['product_tank'];
                $nozel_last_reading = $item['nozel_last_reading'];
                $last_date = $item['last_date'];

                $sql1 = "INSERT INTO `dealers_nozzel`
                (`dealer_id`,
                `name`,
                `tank_id`,
                `products`,
                `dispenser_id`,
                `last_reading`,
                `last_date`,
                `created_at`,
                `created_by`)
                VALUES
                ('$dealer_id',
                '$name',
                '$product_tank',
                '$nozzels_products',
                '$active',
                '$nozel_last_reading',
                '$last_date',
                '$date',
                '$user_id');";

                if (mysqli_query($db, $sql1)) {
                    // $output = 1;
                    $noz_id = mysqli_insert_id($db);
                    $readings = "INSERT INTO `dealers_nozzel_readings`
                    (`nozle_id`,
                    `dispenser_id`,
                    `dealer_id`,
                    `product_id`,
                    `old_reading`,
                    `new_reading`,
                    `created_at`,
                    `created_by`)
                    VALUES
                    ('$noz_id',
                    '$active',
                    '$dealer_id',
                    '$nozzels_products',
                    '$nozel_last_reading',
                    '$nozel_last_reading',
                    '$date',
                    '$user_id');";
                    if (mysqli_query($db, $readings)) {


                        $output = 1;

                    } else {
                        $output = 'Error' . mysqli_error($db) . '<br>' . $readings;

                    }

                }else {
                    $output = 'Error' . mysqli_error($db) . '<br>' . $sql1;

                }



            }
        } else {
            $output = "Failed to decode JSON string.";
        }
    } else {
        $output = 'Error' . mysqli_error($db) . '<br>' . $query_main;

    }


    echo $output;
}


?>