<?php

include("../../hacol_conif_post.php");


// Check if POST request is received
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Extract data from POST request
    $erp_id = $_POST['erp_id'];
    $site_name = $_POST['site_name'];
    $location = $_POST['location'];
    $area_code = $_POST['area_code'];
    $contact = $_POST['contact'];
    $address_line1 = $_POST['address_line1'];
    $address_line2 = $_POST['address_line2'];
    $address_line3 = $_POST['address_line3'];
    $city = $_POST['city'];
    $postal = $_POST['postal'];
    $province = $_POST['province'];
    $depotcode = $_POST['depotcode'];
    $depotdesc = $_POST['depotdesc'];
    $regional_manager = $_POST['regional_manager'];
    $territory_manager = $_POST['territory_manager'];
    $REGIONALMANAGERDESC = $_POST['REGIONALMANAGERDESC'];
    $TERRITORYMANAGERDESC = $_POST['TERRITORYMANAGERDESC'];
    $RETTYPE = $_POST['RETTYPE'];
    $RETTYPEDESC = $_POST['RETTYPEDESC'];

    // Prepare SQL statement
    $sql = "INSERT INTO customer_details (erp_id, site_name, location, area_code, contact, address_line1, address_line2, address_line3, city, postal, province, depotcode, depotdesc, regional_manager, territory_manager, regional_manager_desc, territory_manager_decs, rettype, retype_desc) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

if ($stmt = $conn->prepare($sql)) {
    // Bind parameters to the statement
    $stmt->bind_param("sssssssssssssssssss", $erp_id, $site_name, $location, $area_code, $contact, $address_line1, $address_line2, $address_line3, $city, $postal, $province, $depotcode, $depotdesc, $regional_manager, $territory_manager, $REGIONALMANAGERDESC, $TERRITORYMANAGERDESC, $RETTYPE, $RETTYPEDESC);

    // Attempt to execute the prepared statement
    if ($stmt->execute()) {
        // Success
        $response["success"] = true;
        $response["message"] = "Data inserted successfully";
        echo json_encode($response);
    } else {
        // Error
        $response["success"] = false;
        $response["message"] = "Error: " . $sql . "<br>" . $conn->error;
        echo json_encode($response);
    }

    // Close statement
    $stmt->close();
} else {
    // Error
    $response["success"] = false;
    $response["message"] = "Error: " . $sql . "<br>" . $conn->error;
    echo json_encode($response);
}


} else {
    // Invalid request method
    $response["success"] = false;
    $response["message"] = "Invalid request method";
    echo json_encode($response);
}

// Close connection
$conn->close();

?>
