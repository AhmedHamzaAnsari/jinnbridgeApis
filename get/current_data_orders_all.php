<?php
header("Content-Type: application/json");

// Connect to your MySQL database
include("../config.php");

$access_key = '03201232927';
$pass = $_GET["key"] ?? '';
$from = $_GET["from"] ?? '';
$rettype = $_GET["rettype"];

if (!empty($pass) && $pass === $access_key) {

    // Initialize an array to store the formatted data
    $formatted_data = [];

    // Initialize variables to accumulate totals
    $total_PMG = 0;
    $total_HSD = 0;
    $total_HASRON = 0;
    // $rettype_condition = ($rettype !== 'RT') ? "AND dl.rettype = '$rettype'" : '';

    // Query to fetch dealer records and their associated depots
    $sql = "SELECT dl.id, dl.name, dl.sap_no, dl.region, dl.province,
                   (SELECT GROUP_CONCAT(geo.consignee_name SEPARATOR ', ') 
                    FROM dealers_depots AS dd
                    JOIN geofenceing AS geo ON geo.id = dd.depot_id
                    WHERE dd.dealers_id = dl.id) as dealers_depots
            FROM dealers as dl 
            WHERE dl.indent_price = 1 AND dl.rettype = '$rettype'
            ORDER BY dl.id DESC";

    $result = $db->query($sql);

    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $record_data = [
                'dealer_id' => $row["id"],
                'dealer_name' => $row["name"],
                'dealer_sap' => $row["sap_no"],
                'dealer_region' => $row["province"],
                'dealers_depots' => $row["dealers_depots"],
                'PMG' => 0,
                'HSD' => 0,
                'HASRON' => 0
            ];

            $id = $row["id"];

            // Query to get stock recon data for the current dealer
            $get_orders = "SELECT sum(od.quantity) as quantity,pp.name as product_name FROM order_main as om
            join order_detail as od on od.main_id=om.id
            join dealers_products as pp on pp.id=od.product_type where od.cus_id = ? and date(od.date) = ? and om.status IN(1,5) group by od.product_type;";

            if ($stmt = $db->prepare($get_orders)) {
                // Bind the dealer ID and the date to the query
                $stmt->bind_param('is', $id, $from);
                $stmt->execute();
                $result_orders = $stmt->get_result();

                while ($row_2 = $result_orders->fetch_assoc()) {
                    $quantity = $row_2['quantity'];
                    $product_name = $row_2['product_name'];

                    // Update the record data and accumulate totals based on the product type
                    if ($product_name === 'HSD') {
                        $record_data['HSD'] = $quantity;
                        $total_HSD += $quantity;
                    } elseif ($product_name === 'PMG') {
                        $record_data['PMG'] = $quantity;
                        $total_PMG += $quantity;
                    } elseif ($product_name === 'HASRON') {
                        $record_data['HASRON'] = $quantity;
                        $total_HASRON += $quantity;
                    }
                }

                $stmt->close();
            }

            // Add the record to the formatted data array
            $formatted_data[] = $record_data;
        }

        // Append the total sums to the formatted data
        $formatted_data[] = [
            'dealer_id' => '---',
            'dealer_name' => '---',
            'dealer_sap' => 'Total',
            'dealer_region' => '---',
            'dealers_depots' => '---',
            'PMG' => $total_PMG,
            'HSD' => $total_HSD,
            'HASRON' => $total_HASRON
        ];

        // Output the JSON string
        echo json_encode($formatted_data, JSON_PRETTY_PRINT);
    } else {
        echo json_encode(["error" => "Failed to fetch dealer data: " . $db->error]);
    }

} else {
    echo json_encode(["error" => "Invalid or missing key"]);
}

?>
