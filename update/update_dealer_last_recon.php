<?php
include("../config.php");
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Validate and retrieve all input parameters
    $user_id = isset($_POST['user_id']) ? $_POST['user_id'] : null;
    $task_id = isset($_POST['task_id']) ? $_POST['task_id'] : null;
    $dealer_id = isset($_POST['dealer_id']) ? $_POST['dealer_id'] : null;
    $product_id = isset($_POST['product_id']) ? $_POST['product_id'] : null;
    $recon_id = isset($_POST['recon_id']) ? $_POST['recon_id'] : null;
    $reason = isset($_POST['reason']) ? $_POST['reason'] : null;

    $tanks = isset($_POST['tanks_data']) ? $_POST['tanks_data'] : null;
    $sum_of_opening = isset($_POST['sum_of_opening']) ? $_POST['sum_of_opening'] : null;
    $sum_of_closing = isset($_POST['sum_of_closing']) ? $_POST['sum_of_closing'] : null;

    $nozzel = isset($_POST['nozels_data']) ? $_POST['nozels_data'] : null;

    $total_sales = isset($_POST['total_sales']) ? $_POST['total_sales'] : null;

    $total_receipts = isset($_POST['total_receipts']) ? $_POST['total_receipts'] : null;
    $in_transit = isset($_POST['in_transit']) ? $_POST['in_transit'] : null;
    $final_receipts = isset($_POST['final_receipts']) ? $_POST['final_receipts'] : null;



    $physical_stock = isset($_POST['physical_stock']) ? $_POST['physical_stock'] : null;
    $book_stock = isset($_POST['book_stock']) ? $_POST['book_stock'] : null;
    $variance = isset($_POST['variance']) ? $_POST['variance'] : null;
    $remarks = isset($_POST['remarks']) ? $_POST['remarks'] : null;
    $shortage_claim = isset($_POST['shortage_claim']) ? $_POST['shortage_claim'] : null;
    $variance_of_sales = isset($_POST['variance_of_sales']) ? $_POST['variance_of_sales'] : null;
    $average_daily_sales = isset($_POST['average_daily_sales']) ? $_POST['average_daily_sales'] : null;



    $date = date('Y-m-d H:i:s');

    // Function to handle totalizer data
    function new_totalizer($is_totalizer_data, $db, $dealer_id, $date, $user_id, $last_date, $task_id, $recon_id)
    {
        $dataArray = json_decode($is_totalizer_data, true);

        if (is_array($dataArray)) {
            foreach ($dataArray as $item) {
                $id = $item['id'];
                $opening = $item['opening'];
                $closing = $item['closing'];

                $readings = "INSERT INTO `dealers_nozzel_readings`
                (`nozle_id`, `recon_id`, `task_id`, `dealer_id`, `old_reading`, `new_reading`, `is_change_totalizer`, `created_at`, `created_by`)
                VALUES ('$id', '$recon_id', '$task_id', '$dealer_id', '$opening', '$closing', '1', '$date', '$user_id');";

                if (mysqli_query($db, $readings)) {
                    $update_dips = "UPDATE `dealers_nozzel`
                    SET `last_reading` = '$closing', `totalizer` = (totalizer + 1)
                    WHERE `id` = '$id';";

                    if (!mysqli_query($db, $update_dips)) {
                        return 'Error: ' . mysqli_error($db);
                    }
                } else {
                    return 'Error: ' . mysqli_error($db);
                }
            }
        } else {
            return 'Input is not a valid array';
        }

        return 1;
    }


    // Insert into dealer_stock_recon_new
    $query = "UPDATE `dealer_stock_recon_new`
        SET
        `tanks` = '$tanks',
        `sum_of_closing` = '$sum_of_closing',
        `nozzel` = '$nozzel',
        `total_sales` = '$total_sales',
        `total_recipt` = '$total_receipts',
        `book_value` = '$book_stock',
        `variance` = '$variance',
        `remark` = '$remarks',
        `shortage_claim` = '$shortage_claim',
        `variance_of_sales` = '$variance_of_sales',
        `average_daily_sales` = '$average_daily_sales',
        `is_edit` = `is_edit` + 1 
        WHERE `id` = '$recon_id' and task_id='$task_id';";

    if (mysqli_query($db, $query)) {

        // Insert into dealers_nozzel_readings
        $dataArray = json_decode($nozzel, true);
        if (is_array($dataArray)) {
            foreach ($dataArray as $item) {
                $id = $item['id'];
                $opening = $item['opening'];
                $closing = $item['closing'];

                $readings = "INSERT INTO `dealers_nozzel_readings`
                    (`nozle_id`, `recon_id`, `task_id`, `dealer_id`, `old_reading`, `new_reading`, `created_at`, `created_by`)
                    VALUES ('$id', '$recon_id', '$task_id', '$dealer_id', '$opening', '$closing', '$date', '$user_id');";

                if (mysqli_query($db, $readings)) {
                    $update_dips = "UPDATE `dealers_nozzel`
                        SET `last_reading` = '$closing'
                        WHERE `id` = '$id';";

                    if (!mysqli_query($db, $update_dips)) {
                        $output = 'Error: ' . mysqli_error($db);
                    }
                } else {
                    $output = 'Error: ' . mysqli_error($db);
                }
            }

            // Handle totalizer data
            // new_totalizer($is_totalizer_data, $db, $dealer_id, $date, $user_id, $last_date, $task_id, $recon_id);
        }

        // Insert tanks data
        $dataArrayTanks = json_decode($tanks, true);
        if (is_array($dataArrayTanks)) {
            foreach ($dataArrayTanks as $tanki) {
                $tank_id = $tanki['id'];
                $opening = $tanki['opening'];
                $closing = $tanki['closing'];
                $opening_dip = $tanki['opening_dip'];
                $closing_dip = $tanki['closing_dip'];

                $readings_tanks = "INSERT INTO `dealer_dip_log`
                    (`dealer_id`, `tank_id`, `previous_dip`, `current_dip`, `old_reading`, `current_reading`, `datetime`, `description`, `created_at`, `created_by`)
                    VALUES ('$dealer_id', '$tank_id', '$opening_dip', '$closing_dip', '$opening', '$closing', '$date', '---', '$date', '$user_id');";

                if (mysqli_query($db, $readings_tanks)) {
                    $update_dips = "UPDATE `dealers_lorries`
                        SET `current_dip` = '$closing_dip', `current_reading` = '$closing'
                        WHERE `id` = '$tank_id';";

                    if (!mysqli_query($db, $update_dips)) {
                        $output = 'Error: ' . mysqli_error($db);
                    }
                } else {
                    $output = 'Error: ' . mysqli_error($db);
                }
            }
        }

        $logs = "INSERT INTO `dealers_stock_recon_new_log`
            (`task_id`,
            `recon_id`,
            `reason`,
            `product_id`,
            `created_at`,
            `created_by`)
            VALUES
            ('$task_id',
            '$recon_id',
            '$reason',
            '$product_id',
            '$date',
            '$user_id');";

        mysqli_query($db, $logs);

        $output = 1; // Success
    } else {
        $output = 'Error: ' . mysqli_error($db);
    }


    echo $output;
}

?>