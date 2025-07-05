<?php
// fetch.php  
include("../config.php");

$access_key = '03201232927';
$pass = $_GET["key"];

if ($pass != '') {
    if ($pass == $access_key) {
        $id = $_GET["id"];
        $pre = $_GET["pre"];
        $month = $_GET["month"];  // Added 'month' as input parameter

        $sql = "SELECT * FROM users WHERE id=$id";
        $result = mysqli_query($db, $sql);
        $row = mysqli_fetch_array($result);
        $rol = $row['privilege'];

        if ($rol != 'ASM Disabled') {
            // Prepare query to retrieve dealers based on role
            if ($pre == 'ZM') {
                $sql_query1 = "SELECT * FROM dealers WHERE zm=$id";
            } elseif ($pre == 'TM') {
                $sql_query1 = "SELECT * FROM dealers WHERE tm=$id";
            } else {
                $sql_query1 = "SELECT * FROM dealers WHERE asm=$id";
            }

            $result = mysqli_query($db, $sql_query1);
            $data = []; // Initialize main array for JSON output

            // Process each dealer's data
            while ($user = mysqli_fetch_assoc($result)) {
                $dealer_id = $user['id'];

                $dealers_recon = "SELECT SUM(si.quantity) AS month_sales ,
                pp.name AS product_name, 
                dl.name AS dealer_name
                FROM order_info as si 
                join order_sales_invoice as ss on ss.order_no=si.order_no
                join all_products as pp on pp.sap_no=si.item
                join dealers as dl on dl.sap_no=si.customer_id
                WHERE ss.status!=3 and DATE_FORMAT(si.created_at, '%Y-%m') = '$month'
                AND dl.id = $dealer_id 
                and dl.asm = $id 
                GROUP BY pp.name";

                $result_recon = $db->query($dealers_recon) or die("Error: " . mysqli_error($db));

                // Initialize product sales for each dealer
                $dealer_data = [
                    "dealer_name" => $user['name'],
                    "HASRON" => 0,
                    "HSD" => 0,
                    "PMG" => 0
                ];

                // Aggregate sales data by product
                while ($user2 = $result_recon->fetch_assoc()) {
                    $product_name = strtoupper($user2['product_name']);
                    $month_sales = (int) $user2['month_sales'];

                    if (in_array($product_name, ['HASRON', 'HSD', 'PMG'])) {
                        $dealer_data[$product_name] = $month_sales;
                    }
                }

                // Add each dealer's data to the main array
                $data[] = $dealer_data;
            }

            // Output JSON-formatted data
            echo json_encode($data, JSON_PRETTY_PRINT);
        }

    } else {
        echo 'Wrong Key...';
    }

} else {
    echo 'Key is Required';
}
?>