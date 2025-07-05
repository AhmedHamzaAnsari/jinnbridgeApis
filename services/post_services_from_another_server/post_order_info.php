<?php
include("../../hacol_conif_post.php");


// Check if the request method is POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Extract data from the form data
    $customer_id = $_POST['customer_id'];
    $customer_name = $_POST['customer_name'];
    $order_no = $_POST['order_no'];
    $order_type = $_POST['order_type'];
    $invoice = $_POST['invoice'];
    $invoicetype = $_POST['invoicetype'];
    $item = $_POST['item'];
    $quantity = $_POST['quantity'];
    $unitmeasure = $_POST['unitmeasure'];
    $order_date = $_POST['order_date'];
    $load_status = $_POST['load_status'];
    $vehicle = $_POST['vehicle'];
    $carrier_code = $_POST['carrier_code'];
    $carrier_desc = $_POST['carrier_desc'];

    // $sql = "SELECT * FROM dealers where sap_no='$customer_id';";

    // // echo $sql;

    // $result = mysqli_query($conn, $sql);
    // $row = mysqli_fetch_array($result);
    // $count = mysqli_num_rows($result);
    // $dealer_co = '';
    // if ($count > 0) {

    //     $dealer_co = $row['co-ordinates'];

    // }

    // $sql2 = "SELECT * FROM geofenceing where code='$carrier_code' and geotype='depot';";

    // // echo $sql;

    // $result2 = mysqli_query($conn, $sql2);
    // $row2 = mysqli_fetch_array($result2);
    // $count2 = mysqli_num_rows($result2);
    // $geo_co = '';
    // if ($count2 > 0) {

    //     $geo_co = $row2['Coordinates'];

    // }

    // if ($geo_co != '' && $dealer_co != "") {
    //     $mychars = explode(', ', $geo_co);
    //     $geo_lat = floatval($mychars[0]);
    //     $geo_lng = floatval($mychars[1]);

    //     $mychars1 = explode(', ', $dealer_co);
    //     $dealers_lat = floatval($mychars1[0]);
    //     $dealers_lng = floatval($mychars1[1]);

    //     $distance = calculateDistance($geo_lat, $geo_lat, $dealers_lat, $dealers_lng);

    //     echo 'Distance '.$distance;

    // }
    // Prepare and bind the SQL statement
    $stmt = $conn->prepare("INSERT INTO order_info (customer_id, customer_name, order_no, order_type, invoice, invoicetype, item, quantity, unitmeasure, order_date, load_status, vehicle, carrier_code, carrier_desc) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssssssssssss", $customer_id, $customer_name, $order_no, $order_type, $invoice, $invoicetype, $item, $quantity, $unitmeasure, $order_date, $load_status, $vehicle, $carrier_code, $carrier_desc);

    // Execute the statement
    if ($stmt->execute() === TRUE) {
        // echo json_encode(array("message" => "Record created successfully"));
    } else {
        // echo json_encode(array("error" => "Error: " . $conn->error));
    }

    // Close the statement
    $stmt->close();
}

// Close the connection
$conn->close();
// echo 'Service Last Run => ' . date('Y-m-d H:i:s');

function calculateDistance($originLat, $originLng, $destLat, $destLng)
{
    $apiKey = 'AIzaSyD9ztWZaPapSg_s2x_VIKx2DwO5zq0gcDU';
    $url = "https://maps.googleapis.com/maps/api/distancematrix/json?units=metric&origins={$originLat},{$originLng}&destinations={$destLat},{$destLng}&key={$apiKey}";

    $response = file_get_contents($url);
    $data = json_decode($response, true);

    if ($data['status'] === 'OK' && isset($data['rows'][0]['elements'][0]['distance']['value'])) {
        $distanceInMeters = $data['rows'][0]['elements'][0]['distance']['value'];
        $distanceInKm = $distanceInMeters / 1000; // Convert meters to kilometers
        return $distanceInKm;
    } else {
        return null;
    }
}
?>