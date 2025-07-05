<?php
include("../config.php");
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_id = $_POST['user_id'];
    $date = date('Y-m-d H:i:s');
    $userData = count($_POST["product_qty"]);

    $total_amount = 0;
    $mains_id = '';

    $db->autocommit(FALSE); // Disable autocommit mode to use transactions

    try {
        for ($i = 0; $i < $userData; $i++) {
            $product_name = $_POST['product_name'][$i];
            $product_qty = $_POST['product_qty'][$i];
            $product_id = $_POST['product_id'][$i];
            $product_old_qty = $_POST['product_old_qty'][$i];
            $main_id = $_POST['main_id'][$i];
            $sub_id = $_POST['sub_id'][$i];
            $rate = $_POST['rate'][$i];
            $amount = $rate * $product_qty;
            $total_amount += $amount;
            $mains_id = $main_id;

            $query_count = "UPDATE `order_detail`
                            SET `quantity` = ?, `amount` = ?
                            WHERE `id` = ? AND `main_id` = ?";
            $stmt = $db->prepare($query_count);
            $stmt->bind_param("idis", $product_qty, $amount, $sub_id, $main_id);
            if (!$stmt->execute()) {
                throw new Exception("Error updating order_detail: " . $stmt->error);
            }

            $insert_log = "INSERT INTO `order_qty_update_log`
                           (`order_main_id`, `order_sub_id`, `product_id`, `product_name`,
                            `old_qty`, `new_qty`, `created_at`, `created_by`)
                           VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $db->prepare($insert_log);
            $stmt->bind_param("iiissisi", $main_id, $sub_id, $product_id, $product_name, 
                              $product_old_qty, $product_qty, $date, $user_id);
            if (!$stmt->execute()) {
                throw new Exception("Error inserting into order_qty_update_log: " . $stmt->error);
            }
        }

        $main_update = "UPDATE `order_main`
                        SET `total_amount` = ?
                        WHERE `id` = ?";
        $stmt = $db->prepare($main_update);
        $stmt->bind_param("di", $total_amount, $mains_id);
        if (!$stmt->execute()) {
            throw new Exception("Error updating order_main: " . $stmt->error);
        }

        $db->commit(); // Commit the transaction
        $output = 1;
    } catch (Exception $e) {
        $db->rollback(); // Rollback the transaction on error
        $output = $e->getMessage();
    }

    $stmt->close();
    $db->autocommit(TRUE); // Re-enable autocommit mode

    echo $output;
}
?>
