<?php
include("../config.php");
header('Content-Type: application/json');

$response = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['id']) && !empty($_POST['id'])) {
        $id = $_POST['id'];

        $deleteQuery = "DELETE FROM dealers_products WHERE id = '$id'";
        $logDeleteQuery = "DELETE FROM dealer_nozel_price_log WHERE product_id = '$id'";

        if (mysqli_query($db, $deleteQuery)) {
            mysqli_query($db, $logDeleteQuery); // delete related logs
            $response['status'] = 1;
            $response['message'] = 'Product deleted successfully.';
        } else {
            $response['status'] = 0;
            $response['message'] = 'Failed to delete the product.';
        }
    } else {
        $response['status'] = 0;
        $response['message'] = 'Missing or invalid product ID.';
    }
} else {
    $response['status'] = 0;
    $response['message'] = 'Invalid request method.';
}

echo json_encode($response);
?>
