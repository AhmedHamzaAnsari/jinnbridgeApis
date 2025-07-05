<?php
//fetch.php  
include("../config.php");

$access_key = '03201232927';
$pass = $_GET["key"];

if (!empty($pass)) {
    if ($pass == $access_key) {
        $dealer_id = intval($_GET["dealer_id"]);
        $tm_id = intval($_GET["tm_id"]);

        $from = $db->real_escape_string($_GET["from"]);
        $to = $db->real_escape_string($_GET["to"]);
        $products = $db->real_escape_string($_GET["products"]);
        $product_val = "";
        if($products!=""){
            $product_val = "AND pp.id='$products'";
        }
        else{
            $product_val = "";
        }
        // Initialize an array to store the data
        $formatted_data = [];
        $recon_dater = "SELECT GROUP_CONCAT(DISTINCT(task_id)) AS task_ids 
        FROM dealer_stock_recon_new 
        WHERE date(created_at) >= '$from' 
        AND date(created_at) <= '$to' 
        AND dealer_id = '$dealer_id' and created_by='$tm_id'";

        $result_recon_dater = mysqli_query($db, $recon_dater);

        if ($result_recon_dater) {
            $row_recon_dater = mysqli_fetch_assoc($result_recon_dater);
            $task_ids = $row_recon_dater['task_ids'];

            if (!empty($task_ids)) {
                // Query to fetch records
                $sql = "SELECT it.*, dl.name as dealer_name, dl.region, us.name as tm_name,dl.sap_no as dealer_sap
                FROM inspector_task as it
                JOIN dealers as dl on dl.id = it.dealer_id
                JOIN users as us on us.id = it.user_id
                WHERE dl.id = $dealer_id 
                AND it.stock_recon=1
                AND it.id IN($task_ids);";

                $result = $db->query($sql);

                if ($result) {
                    while ($row = $result->fetch_assoc()) {
                        $id = $row["id"];
                        $name = $row["dealer_name"];
                        $dealer_sap = $row["dealer_sap"];
                        $terr = '';
                        $region = $row["region"];
                        $tm_name = $row["tm_name"];

                        $get_orders = "SELECT rs.*, pp.name as product_name
                       FROM dealer_stock_recon_new as rs
                       join dealers_products as dp on dp.id=rs.product_id
                       JOIN all_products as pp ON pp.name = dp.name
                       WHERE rs.task_id = $id $product_val
                       GROUP BY rs.product_id, rs.task_id";

                        $result_orders = $db->query($get_orders);

                        if ($result_orders) {
                            while ($row_2 = $result_orders->fetch_assoc()) {
                                // Prepare the record data
                                $record_data = [
                                    'site_code' => $dealer_sap,
                                    'site' => $name,
                                    'tm' => $tm_name,
                                    'territory' => $terr,
                                    'region' => $region,
                                    'product_name' => $row_2['product_name'],
                                    'opening_date' => date('Y-m-d', strtotime($row_2['last_recon_date'])),
                                    'closing_date' => date('Y-m-d', strtotime($row_2['created_at'])),
                                    'no_os_days' => $row_2['total_days'],
                                    'daily_sales' => $row_2['average_daily_sales'],
                                    'opening_stock' => $row_2['sum_of_opening'],
                                    'receipts' => $row_2['total_recipt'],
                                    'sales' => $row_2['total_sales'],
                                    'book_stock' => $row_2['book_value'],
                                    'variance' => $row_2['variance'],
                                    'variance_percentage' => round($row_2['average_daily_sales'], 2),
                                    'remark' => $row_2['remark']

                                ];

                                // Append the record data to the formatted_data array
                                $formatted_data[] = $record_data;
                            }
                        } else {
                            echo "Error fetching stock recon data: " . $db->error;
                        }
                    }

                    // Convert the array to a JSON string
                    $jsonData = json_encode($formatted_data);

                    // Set the response header to indicate JSON content
                    header('Content-Type: application/json');

                    // Output the JSON string
                    echo $jsonData;

                } else {
                    echo "Error fetching inspector task data: " . $db->error;
                }
            } else {
                echo "No tasks found for the specified dealer and date range.";
            }
        } else {
            echo "Error executing the task ID query.";
        }
    } else {
        echo 'Wrong Key...';
    }
} else {
    echo 'Key is Required';
}
?>