<?php
//fetch.php  
include("../config.php");

$access_key = '03201232927';
$pass = $_GET["key"];

if (!empty($pass)) {
    if ($pass == $access_key) {
        // $dealer_id = intval($_GET["dealer_id"]);
        // $tm_id = intval($_GET["tm_id"]);
        $region = $_GET["region"];
        $tm = $_GET["tm"] ?? '';

        $from = $db->real_escape_string($_GET["from"]);
        $to = $db->real_escape_string($_GET["to"]);
        $products = $db->real_escape_string($_GET["products"]);
        $product_val = "";
        if ($products != "") {
            $product_val = "AND pp.id='$products'";
        } else {
            $product_val = "";
        }
        
        // Initialize an array to store the data
        // and tr.recon_approval='1' and tr.approved_status='1';

        $formatted_data = [];
        $get_orders = "SELECT rs.*, pp.name as product_name,us.name as tm_name,dl.sap_no as dealer_sap,dl.name as dealer_name
        FROM dealer_stock_recon_new as rs
        join dealers_products as dp on dp.id=rs.product_id
        JOIN all_products as pp ON pp.name = dp.name
        join inspector_task as it on it.id=rs.task_id
        JOIN dealers as dl ON dl.id = it.dealer_id
        left join inspector_task_response as tr on tr.task_id=it.id
        JOIN users as us on us.id = it.user_id
        WHERE us.region='$region' and date(rs.created_at)>='$from' and date(rs.created_at)<='$to'  and us.id IN($tm)
        GROUP BY rs.product_id, rs.task_id";

        $result_orders = $db->query($get_orders);

        if ($result_orders) {
            while ($row_2 = $result_orders->fetch_assoc()) {
                // Prepare the record data
                $tank_beharior = false;
                $external_dumping = false;
                $external_upliftment = false;
                $variance = floatval($row_2['variance']);
                $book_value = floatval($row_2['book_value']);
                $physical_stock = floatval($row_2['sum_of_closing']);
                $task_id = $row_2['task_id'];
                $total_days = $row_2['total_days'];
                $created_at = $row_2['created_at'];
                $tm_name = $row_2['tm_name'];
                $dealer_sap = $row_2['dealer_sap'];
                $dealer_name = $row_2['dealer_name'];
                


                if ($variance < 1000 && $variance > -1000) {
                    // echo "Variance is less than 1000 and greater than -1000.";
                    $tank_beharior = true;
                } else {

                    $tank_beharior = false;


                    if ($book_value > $physical_stock) {
                        // echo "Variance is less than 1000 and greater than -1000.";
                        $external_dumping = true;
                    } else {

                        $external_dumping = false;
                    }

                    if ($physical_stock > $book_value) {
                        // echo "Variance is less than 1000 and greater than -1000.";
                        $external_upliftment = true;
                    } else {

                        $external_upliftment = false;
                    }
                }



                $record_data = [
                    'task_id' => $task_id,
                    'total_days' => $total_days,
                    'created_at' => $created_at,
                    'site' => $dealer_name,
                    'dealer_sap' => $dealer_sap,
                    'tm' => $tm_name,
                    'territory' => '',
                    'region' => $region,
                    'product_name' => $row_2['product_name'],
                    'opening_date' => date('Y-m-d', strtotime($row_2['last_recon_date'])),
                    'closing_date' => date('Y-m-d', strtotime($row_2['created_at'])),
                    'no_os_days' => $row_2['total_days'],
                    'daily_sales' => $row_2['average_daily_sales'],
                    'opening_stock' => $row_2['sum_of_opening'],
                    'physical_stock' => $row_2['sum_of_closing'],
                    'receipts' => $row_2['total_recipt'],
                    'sales' => $row_2['total_sales'],
                    'book_stock' => $row_2['book_value'],
                    'variance' => ($row_2['variance'] === 'NaN') ? 0 : $row_2['variance'],
                    'variance_percentage' => round($row_2['variance_of_sales'], 2),
                    'remark' => $row_2['remark'],
                    'tank_beharior' => $tank_beharior,
                    'external_dumping' => $external_dumping,
                    'external_upliftment' => $external_upliftment



                ];

                // Append the record data to the formatted_data array
                $formatted_data[] = $record_data;
            }
        } else {
            echo "Error fetching stock recon data: " . $db->error;
        }
        header('Content-Type: application/json');

        $formatted_data = utf8ize($formatted_data);
        $json = json_encode($formatted_data, JSON_PRETTY_PRINT);

        if ($json === false) {
            echo json_encode(["error" => "JSON encoding failed", "details" => json_last_error_msg()]);
        } else {
            echo $json;
        }
    } else {
        echo 'Wrong Key...';
    }
} else {
    echo 'Key is Required';
}

function utf8ize($data)
{
    if (is_array($data)) {
        return array_map('utf8ize', $data);
    } elseif (is_string($data)) {
        return mb_convert_encoding($data, 'UTF-8', 'UTF-8');
    }
    return $data;
}
?>