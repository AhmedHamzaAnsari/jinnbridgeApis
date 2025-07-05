<?php
include ("../config.php");
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_id = $_POST['user_id'];
    $date = date('Y-m-d H:i:s');

    // Fetch data from POST request
    $product_name = $_POST['product_name'];
    $product_qty = $_POST['product_qty'];
    $product_id = $_POST['product_id'];
    $product_old_qty = $_POST['product_old_qty'];
    $rate = $_POST['rate'];
    $main_id = $_POST['main_id']; // Assuming main_id is provided for the single order update
    $order_id = $_POST['sub_id']; // Single order ID

    $amount = $rate * $product_qty;
    $total_amount = $amount;

    $total_order_amount = $_POST['total_order_amount'];

    // Begin transaction
    $db->autocommit(FALSE);

    try {
        // Update order_detail
        $query_count = "UPDATE `order_detail`
                        SET `quantity` = ?, `amount` = ?
                        WHERE `id` = ? AND `main_id` = ?";
        $stmt = $db->prepare($query_count);
        $stmt->bind_param("idis", $product_qty, $amount, $order_id, $main_id);
        if (!$stmt->execute()) {
            throw new Exception("Error updating order_detail: " . $stmt->error);
        }

        // Insert into order_qty_update_log
        $insert_log = "INSERT INTO `order_qty_update_log`
                       (`order_main_id`, `order_sub_id`, `product_id`, `product_name`,
                        `old_qty`, `new_qty`, `created_at`, `created_by`)
                       VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $db->prepare($insert_log);
        $stmt->bind_param(
            "iiissisi",
            $main_id,
            $order_id,
            $product_id,
            $product_name,
            $product_old_qty,
            $product_qty,
            $date,
            $user_id
        );
        if (!$stmt->execute()) {
            throw new Exception("Error inserting into order_qty_update_log: " . $stmt->error);
        }

        // Update order_main
        $main_update = "UPDATE `order_main`
                        SET `total_amount` = ?
                        WHERE `id` = ?";
        $stmt = $db->prepare($main_update);
        $stmt->bind_param("di", $total_order_amount, $main_id);
        if (!$stmt->execute()) {
            throw new Exception("Error updating order_main: " . $stmt->error);
        }

        // Commit the transaction
        $db->commit();
        $output = 1;
    } catch (Exception $e) {
        // Rollback the transaction on error
        $db->rollback();
        $output = $e->getMessage();
    }

    $stmt->close();
    $db->autocommit(TRUE);

    echo $output;
}
?>