<?php
include("../../config.php");
session_start();

$output = '';

if (isset($_POST)) {
    $user_id = $_POST['user_id'];
    $datetime = date('Y-m-d H:i:s');

    $order_main_id = $_POST["order_main_id"];
    $product_id = $_POST['product_id'];
    $product_qty = $_POST['product_qty'];
    $rec_dip = $_POST['rec_dip'];
    $rec_reading = $_POST['rec_reading'];
    $rec_tanks = $_POST['rec_tanks'];
    $sub_order_id = $_POST['sub_order_id'];
    $depot_id = $_POST['depot_id'];
    $temperatures = $_POST['temperatures'];

    $query_main = "UPDATE `depots_order_main`
        SET
        `status` = '1'
        WHERE `id` = '$order_main_id';";

    if (mysqli_query($db, $query_main)) {
        $tdate = date('Y-m-d H:i:s'); // Moved inside successful query execution
        
        for ($i = 0; $i < count($product_id); $i++) {
            // Retrieve data for each product
            $diff = $rec_reading[$i] - $product_qty[$i];
            
            // File upload handling
            $file = rand(1000, 100000) . "-" . $_FILES['rec_file']['name'][$i];
            $file_loc = $_FILES['rec_file']['tmp_name'][$i];
            $folder = "../../../jinnBridge_files/uploads/";
            move_uploaded_file($file_loc, $folder . $file);

            if ($product_qty[$i] > 0) {
                // Insert receiving details into depots_order_receiving table
                $sql1 = "INSERT INTO `depots_order_receiving`
                    (`sub_order_id`, `depot_id`, `product_id`, `order_qty`, `receiving_dip`, `receiving_reading`, `receiving_temperature`, `receiving_depot_tanks`, `difference`, `created_at`, `created_by`, `file`)
                    VALUES
                    ('$sub_order_id[$i]', '$depot_id[$i]', '$product_id[$i]', '$product_qty[$i]', '$rec_dip[$i]', '$rec_reading[$i]', '$temperatures[$i]', '$rec_tanks[$i]', '$diff', '$tdate', '$user_id', '$file');";

                if (mysqli_query($db, $sql1)) {
                    // Update depots_tanks table
                    $query = "UPDATE `depots_tanks` SET 
                        `actual_stock` = actual_stock + $rec_reading[$i],
                        `update_time` = '$tdate'
                        WHERE id = $rec_tanks[$i]";

                    if (mysqli_query($db, $query)) {
                        // Insert into depots_tanks_actual_stock_log
                        $log = "INSERT INTO `depots_tanks_actual_stock_log`
                            (`tank_id`, `type`, `qty`, `created_at`, `created_by`)
                            VALUES
                            ('$rec_tanks[$i]', 'Purchase', '$rec_reading[$i]', '$tdate', '$user_id');";

                        if (mysqli_query($db, $log)) {
                            $output = 1;
                        } else {
                            $output = 'Error: ' . mysqli_error($db) . '<br>' . $log;
                        }
                    } else {
                        $output = 'Error: ' . mysqli_error($db) . '<br>' . $query;
                    }
                } else {
                    $output = 'Error: ' . mysqli_error($db) . '<br>' . $sql1;
                }
            }
        }

        // Insert into depot_order_log
        $order_log = "INSERT INTO `depot_order_log`
            (`order_id`, `status`, `created_at`, `created_by`)
            VALUES
            ('$order_main_id', '1', '$tdate', '$user_id');";
        mysqli_query($db, $order_log);
    } else {
        $output = 'Error: ' . mysqli_error($db) . '<br>' . $query_main;
    }

    echo $output;
}
?>
