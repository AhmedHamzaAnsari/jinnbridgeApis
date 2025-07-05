<?php
include("../../hacol_conif_post.php");

// Check if the request method is POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Extract data from the form data
    $customer_id = $_POST['customer_id'];
    $customer_name = $_POST['customer_name'];
    $catcode07 = $_POST['catcode07'];
    $cat7desc = $_POST['cat7desc'];
    $order_no = $_POST['order_no'];
    $order_type = $_POST['order_type'];
    $invoice_no = $_POST['invoice_no'];
    $invoice_desc = $_POST['invoice_desc'];
    $item = $_POST['item'];
    $quantity = $_POST['quantity'];
    $unit_measure = $_POST['unit_measure'];
    $next_status = $_POST['next_status'];
    $last_status = $_POST['last_status'];
    $hold_code = $_POST['hold_code'];
    $order_date = $_POST['order_date'];
    $datetime = $_POST['datetime']; // This field was missing
    $load_status = $_POST['load_status'];
    $vehicle = $_POST['vehicle'];
    $carrier_code = $_POST['carrier_code'];
    $carrier_desc = $_POST['carrier_desc'];
    $buyer_own = $_POST['buyer_own'];
    $sp_code = $_POST['sp_code'];
    $sp_desc = $_POST['sp_desc'];

    $status = 0;
    $created_at = date('Y-m-d H:i:s');

    $sql = "SELECT pd.id,pd.indent_price,pd.Nozel_price,pd.freight_value,dl.rettype_desc  FROM dealers_products as pd
    join all_products as pp on pp.name=pd.name
    join dealers as dl on dl.id=pd.dealer_id
    where pp.sap_no='$item' and dl.sap_no=$customer_id";

    // echo $sql;

    $result = mysqli_query($conn, $sql);
    $row = mysqli_fetch_array($result);
    $count = mysqli_num_rows($result);
    $rate = '';
    if ($count > 0) {
        $id = $row['id'];
        $indent_price = $row['indent_price'];
        $Nozel_price = $row['Nozel_price'];
        $freight_value = $row['freight_value'];
        $rettype_desc = $row['rettype_desc'];

        if ($buyer_own == 'PP ') {
            $rate = $indent_price;
        } else {
            if ($rettype_desc == 'COCO site                     ') {
                $rate = $Nozel_price;

            } else {

                $rate = $freight_value;
            }

        }
    }



    // Prepare and bind the SQL statement
    // $stmt = $conn->prepare("INSERT INTO order_sales_invoice 
    // (customer_id, 
    // customer_name, 
    // catcode07, 
    // cat7desc, 
    // order_no, 
    // order_type, 
    // invoice_no, 
    // invoice_desc, 
    // item, 
    // rate, 
    // quantity, 
    // unit_measure, 
    // next_status, 
    // last_status, 
    // hold_code, 
    // order_date, 
    // datetime, 
    // load_status, 
    // vehicle, 
    // carrier_code, 
    // carrier_desc, 
    // status, 
    // buyer_own,
    // sp_code,
    // sp_desc,
    // created_at) 
    // VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?,?,?,?)");


    // // Check if the prepare statement was successful
    // if ($stmt) {
    //     // Bind parameters to the statement
    //     $stmt->bind_param(
    //         "ssssssssssssssssssssssssss",
    //         $customer_id,
    //         $customer_name,
    //         $catcode07,
    //         $cat7desc,
    //         $order_no,
    //         $order_type,
    //         $invoice_no,
    //         $invoice_desc,
    //         $item,
    //         $rate,
    //         $quantity,
    //         $unit_measure,
    //         $next_status,
    //         $last_status,
    //         $hold_code,
    //         $order_date,
    //         $datetime,
    //         $load_status,
    //         $vehicle,
    //         $sp_code,
    //         $sp_desc,
    //         $status,
    //         $buyer_own,
    //         $sp_code,
    //         $sp_desc,
    //         $created_at
    //     );


    //     // Execute the statement
    //     if ($stmt->execute() === TRUE) {
    //         // echo json_encode(array("message" => "Record created successfully"));
    //     } else {
    //         // echo json_encode(array("error" => "Error: " . $conn->error));
    //     }

    //     // Close the statement
    //     $stmt->close();
    // } else {
    //     echo json_encode(array("error" => "Prepare statement error: " . $conn->error));
    // }


    // Prepare the INSERT statement with ON DUPLICATE KEY UPDATE
    $stmt = $conn->prepare("INSERT INTO order_sales_invoice (
        customer_id, 
        customer_name, 
        catcode07, 
        cat7desc, 
        order_no, 
        order_type, 
        invoice_no, 
        invoice_desc, 
        item, 
        rate, 
        quantity, 
        unit_measure, 
        next_status, 
        last_status, 
        hold_code, 
        order_date, 
        datetime, 
        load_status, 
        vehicle, 
        carrier_code, 
        carrier_desc, 
        status, 
        buyer_own,
        sp_code,
        sp_desc,
        created_at
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ON DUPLICATE KEY UPDATE
        hold_code = VALUES(hold_code),
        last_status = VALUES(last_status),
        next_status = VALUES(next_status),
        invoice_no = VALUES(invoice_no),
        update_at = CURRENT_TIMESTAMP");

    // Check if the prepare statement was successful
    if ($stmt) {
        // Bind parameters to the statement
        $stmt->bind_param(
            "ssssssssssssssssssssssssss",
            $customer_id,
            $customer_name,
            $catcode07,
            $cat7desc,
            $order_no,
            $order_type,
            $invoice_no,
            $invoice_desc,
            $item,
            $rate,
            $quantity,
            $unit_measure,
            $next_status,
            $last_status,
            $hold_code,
            $order_date,
            $datetime,
            $load_status,
            $vehicle,
            $carrier_code,
            $carrier_desc,
            $status,
            $buyer_own,
            $sp_code,
            $sp_desc,
            $created_at
        );

        // Execute the statement
        if ($stmt->execute() === TRUE) {
            echo json_encode(array("message" => "Record created or updated successfully"));
        } else {
            echo json_encode(array("error" => "Error: " . $conn->error));
        }

        // Close the statement
        $stmt->close();
    } else {
        echo json_encode(array("error" => "Prepare statement error: " . $conn->error));
    }

    // Close the connection

}

// Close the connection
$conn->close();
?>