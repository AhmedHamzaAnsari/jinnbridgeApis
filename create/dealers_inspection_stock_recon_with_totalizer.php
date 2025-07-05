<?php
include ("../config.php");
session_start();
if (isset($_POST)) {
    $user_id = $_POST['user_id'];
    $task_id = $_POST["task_id"];
    $form_id = $_POST["form_id"];
    $dpt_id = $_POST["dpt_id"];
    $dealer_id = $_POST["dealer_id"];
    $product_id = $_POST["product_id"];

    $tanks = $_POST["tanks"];
    $sum_of_opening = $_POST["sum_of_opening"];
    $sum_of_closing = $_POST["sum_of_closing"];
    $nozzel = $_POST["nozzel"];
    $is_totalizer_data = $_POST["is_totalizer_data"];
    $total_sales = $_POST["total_sales"];
    $total_recipt = $_POST["total_recipt"];
    $book_value = $_POST["book_value"];
    $variance = $_POST["variance"];
    $remark = $_POST["remark"];
    $shortage_claim = $_POST["shortage_claim"];
    $variance_of_sales = $_POST["variance_of_sales"];
    $average_daily_sales = $_POST["average_daily_sales"];

    $total_days = $_POST["total_days"];
    $last_date = $_POST["last_date"];
    $remark = str_replace("'", '', $remark);


    $date = date('Y-m-d H:i:s');

    function new_totalizer($is_totalizer_data, $db, $dealer_id, $date, $user_id,$last_date)
    {
        $output = '';
        $dataArray = json_decode($is_totalizer_data, true);

        if (is_array($dataArray)) {
            // Iterate through the array using a foreach loop
            foreach ($dataArray as $item) {
                $id = $item['id'];
                $name = $item['name'];
                $opening = $item['opening'];
                $closing = $item['closing'];

                $readings = "INSERT INTO `dealers_nozzel_readings`
                (`nozle_id`,
                `dispenser_id`,
                `dealer_id`,
                `product_id`,
                `old_reading`,
                `new_reading`,
                `is_change_totalizer`,
                `created_at`,
                `created_by`)
                VALUES
                ('$id',
                '',
                '$dealer_id',
                '',
                '$opening',
                '$closing',
                '1',
                '$date',
                '$user_id');";

                if (mysqli_query($db, $readings)) {
                    $update_dips = "UPDATE `dealers_nozzel`
                    SET `last_reading` = '$closing',
                        `totalizer` = (totalizer+1),
                        `last_date` = '$date'
                    WHERE `id` = '$id';";

                    if (mysqli_query($db, $update_dips)) {
                        $output = 1;
                    } else {
                        $output = 'Error: ' . mysqli_error($db) . '<br>' . $update_dips;
                    }
                } else {
                    $output = 'Error: ' . mysqli_error($db) . '<br>' . $readings;
                }
            }
        } else {
            $output = 0;
        }
        return $output;
    }

    // echo 'HAmza';
    if ($_POST["row_id"] != '') {


    } else {

        $query = "INSERT INTO `dealer_stock_recon_new`
        (`task_id`,
        `form_id`,
        `dpt_id`,
        `dealer_id`,
        `product_id`,
        `total_days`,
        `last_recon_date`,
        `tanks`,
        `sum_of_opening`,
        `sum_of_closing`,
        `nozzel`,
        `is_totalizer_data`,
        `total_sales`,
        `total_recipt`,
        `book_value`,
        `variance`,
        `remark`,
        `shortage_claim`,
        `variance_of_sales`,
        `average_daily_sales`,
        `created_at`,
        `created_by`)
        VALUES
        ('$task_id',
        '$form_id',
        '$dpt_id',
        '$dealer_id',
        '$product_id',
        '$total_days',
        '$last_date',
        '$tanks',
        '$sum_of_opening',
        '$sum_of_closing',
        '$nozzel',
        '$is_totalizer_data',
        '$total_sales',
        '$total_recipt',
        '$book_value',
        '$variance',
        '$remark',
        '$shortage_claim',
        '$variance_of_sales',
        '$average_daily_sales',
        '$date',
        '$user_id');";


        if (mysqli_query($db, $query)) {


            $output = 1;
            $dataArray = json_decode($nozzel, true);
            $dataArraytanks = json_decode($tanks, true);
            if (is_array($dataArray)) {
                // Iterate through the array using a foreach loop
                foreach ($dataArray as $item) {

                    $id = $item['id'];
                    $opening = $item['opening'];
                    $closing = $item['closing'];

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
                    ('$id',
                    '',
                    '$dealer_id',
                    '',
                    '$opening',
                    '$closing',
                    '$date',
                    '$user_id');";
                    if (mysqli_query($db, $readings)) {


                        // $output = 1;
                        $update_dips = "UPDATE `dealers_nozzel`
                        SET
                        `last_reading` = '$closing',
                        `last_date` = '$date'
                        WHERE `id` = '$id';";

                        if (mysqli_query($db, $update_dips)) {


                            $output = 1;

                        } else {
                            $output = 'Error' . mysqli_error($db) . '<br>' . $update_dips;

                        }

                    } else {
                        $output = 'Error' . mysqli_error($db) . '<br>' . $readings;

                    }
                }

                new_totalizer($is_totalizer_data, $db, $dealer_id, $date, $user_id,$last_date);


                foreach ($dataArraytanks as $tanki) {

                    $tank_id = $tanki['id'];
                    $opening = $tanki['opening'];
                    $closing = $tanki['closing'];
                    $opening_dip = $tanki['opening_dip'];
                    $closing_dip = $tanki['closing_dip'];

                    $readings_tanks = "INSERT INTO `dealer_dip_log`
                    (`dealer_id`,
                    `tank_id`,
                    `previous_dip`,
                    `current_dip`,
                    `old_reading`,
                    `current_reading`,
                    `datetime`,
                    `description`,
                     `created_at`,
                    `created_by`)
                    VALUES
                    ('$dealer_id',
                    '$tank_id',
                    '$opening_dip',
                    '$closing_dip',
                    '$opening',
                    '$closing',
                    '$date',
                    '---',
                    '$date',
                    '$user_id');";
                    if (mysqli_query($db, $readings_tanks)) {

                        $update_dips = "UPDATE `dealers_lorries`
                        SET
                        `current_dip` = '$closing_dip',
                        `current_reading` = '$closing',
                        `update_time` = '$date'
                        WHERE `id` = '$tank_id';";
                        if (mysqli_query($db, $update_dips)) {


                            $output = 1;

                        } else {
                            $output = 'Error' . mysqli_error($db) . '<br>' . $update_dips;

                        }

                    } else {
                        $output = 'Error' . mysqli_error($db) . '<br>' . $readings_tanks;

                    }
                }
            }

        } else {
            $output = 'Error' . mysqli_error($db) . '<br>' . $query;

        }
    }



    echo $output;


   
}

?>