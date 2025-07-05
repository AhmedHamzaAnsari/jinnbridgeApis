<?php
include("../config.php");
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = isset($_GET['user_id']) ? $_GET['user_id'] : null;
    $dealers = $_POST['dealers'];
    $product_name = $_POST['products_name'];
    $from_date = $_POST['from_date'];
    $to_date = $_POST['to_date'];
    $indent_price = $_POST['indent_price'];
    $nozel_price = $_POST['nozel_price'];
    $freight_value = isset($_POST['freight_value_pro']) ? $_POST['freight_value_pro'] : null;
    $description = $_POST['products_description'];
    $date = date('Y-m-d H:i:s');

    $output = 0;

    if (!$user_id) {
        echo json_encode(['status' => 0, 'error' => 'User ID missing']);
        exit;
    }
    

    foreach ($dealers as $dealer_id) {
        // Check if record exists
        $checkQuery = "SELECT * FROM dealers_products WHERE dealer_id = '$dealer_id' AND name = '$product_name'";
        $result = mysqli_query($db, $checkQuery);
        $exists = mysqli_num_rows($result) > 0;

        if ($exists) {
            $row = mysqli_fetch_assoc($result);
            $product_id = $row['id'];

            // Update existing
            $updateQuery = "UPDATE dealers_products SET 
                `from` = '$from_date',
                `to` = '$to_date',
                `indent_price` = '$indent_price',
                `nozel_price` = '$nozel_price',
                `freight_value` = " . ($freight_value !== null ? "'$freight_value'" : "NULL") . ",
                `description` = '$description',
                `update_time` = '$date'
                WHERE id = $product_id";

            $logQuery = "INSERT INTO dealer_nozel_price_log (
                dealer_id, product_id, indent_price, nozel_price, freight_value, `from`, `to`, description, created_at, created_by
            ) VALUES (
                '$dealer_id', '$product_id', '$indent_price', '$nozel_price',
                " . ($freight_value !== null ? "'$freight_value'" : "NULL") . ",
                '$from_date', '$to_date', '$description', '$date', '$user_id'
            )";

            if (mysqli_query($db, $updateQuery) && mysqli_query($db, $logQuery)) {
                $output = 1;
            }

        } else {
            // Insert new record
            $insertQuery = "INSERT INTO dealers_products (
                dealer_id, name, `from`, `to`, indent_price, nozel_price, freight_value, description, created_at, created_by, update_time
            ) VALUES (
                '$dealer_id', '$product_name', '$from_date', '$to_date',
                '$indent_price', '$nozel_price',
                " . ($freight_value !== null ? "'$freight_value'" : "NULL") . ",
                '$description', '$date', '$user_id', '$date'
            )";

            if (mysqli_query($db, $insertQuery)) {
                $product_id = mysqli_insert_id($db);

                $logQuery = "INSERT INTO dealer_nozel_price_log (
                    dealer_id, product_id, indent_price, nozel_price, freight_value, `from`, `to`, description, created_at, created_by
                ) VALUES (
                    '$dealer_id', '$product_id', '$indent_price', '$nozel_price',
                    " . ($freight_value !== null ? "'$freight_value'" : "NULL") . ",
                    '$from_date', '$to_date', '$description', '$date', '$user_id'
                )";

                if (mysqli_query($db, $logQuery)) {
                    $output = 1;
                }
            }
        }
    }

    echo $output;
}
?>
