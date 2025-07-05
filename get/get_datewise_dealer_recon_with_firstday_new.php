<?php
// fetch.php
include("../config.php");

$access_key = '03201232927';

// Validate the access key
$pass = $_GET["key"];
if (!empty($pass)) {
    $dealer_id = $_GET["dealer_id"];
    $from = $_GET["from"];
    $to = $_GET["to"];

    if ($pass === $access_key) {
        // Fetch all products for the dealer
        $sql_query1 = "SELECT dp.id AS product_id, dp.name AS product_name, dl.name AS dealer_name
                       FROM dealers_products AS dp
                       JOIN dealers AS dl ON dl.id = dp.dealer_id
                       WHERE dp.dealer_id = '$dealer_id';";
        $result1 = $db->query($sql_query1) or die("Error: " . $db->error);

        $combined_data = []; // Initialize an array to hold the combined results

        while ($product = $result1->fetch_assoc()) {
            $product_id = $product['product_id'];
            $product_name = $product['product_name'];
            $dealer_name = $product['dealer_name'];

            $date_header = [];
            $recon = [];
            $first_index = 0;

            // Fetch tasks (dates) associated with the product
            $sql_query2 = "SELECT id, DATE(time) as planed_date , 0 as is_totalizer_change
            FROM inspector_task 
            WHERE dealer_id='$dealer_id'
            AND DATE(time) >= '$from'
            AND DATE(time) <= '$to'
            AND stock_recon = 1
            AND type = 'Inpection'
            ORDER BY time ASC;";
            $result2 = $db->query($sql_query2) or die("Error: " . $db->error);

            while ($task = $result2->fetch_assoc()) {
                $task_id = $task['id'];
                $date_header[] = $task;

                // Fetch reconciliation data for the specific task and product
                $sql_query3 = "SELECT rr.*, ap.name AS product_name, dc.name AS dealer_name 
                               FROM dealer_stock_recon_new AS rr
                               JOIN dealers_products AS dp ON dp.id = rr.product_id
                               JOIN all_products AS ap ON ap.name = dp.name
                               JOIN dealers AS dc ON dc.id = rr.dealer_id
                               WHERE rr.task_id = $task_id
                               AND rr.dealer_id = $dealer_id
                               AND rr.product_id = '$product_id';";
                $result3 = $db->query($sql_query3) or die("Error: " . $db->error);

                while ($recon_data = $result3->fetch_assoc()) {
                    // Handle first index logic
                    if ($first_index === 0) {
                        $duplicate_recon_data = $recon_data;

                        $nozzel_data = $duplicate_recon_data['nozzel'];
                        $duplicate_recon_data['is_totalizer_data'] = $nozzel_data;

                        // Modify `is_totalizer_data`
                        $dataArray = json_decode($duplicate_recon_data['is_totalizer_data'], true);
                        if (is_array($dataArray)) {
                            foreach ($dataArray as &$entry) {
                                $entry['opening'] = "---";
                                $entry['closing'] = "---";
                            }
                            $duplicate_recon_data['is_totalizer_data'] = json_encode($dataArray, JSON_PRETTY_PRINT);
                        }

                        // Add a new date entry with the last recon date
                        $newDate = [
                            "id" => "",
                            "planed_date" => $recon_data['last_recon_date']
                        ];
                        array_unshift($date_header, $newDate);

                        $recon[] = $duplicate_recon_data;
                        $first_index++;
                    }

                    // Process `is_totalizer_data` and `nozzel`
                    $org_is_totalizer_data = $recon_data['is_totalizer_data'];
                    $nozzel_data = json_decode($recon_data['nozzel'], true);
                    $totalize_data = json_decode($org_is_totalizer_data, true);

                    $is_totalizer_data = $recon_data['is_totalizer_data'];

                    // Decode the JSON data to check its structure
                    $dataArray = json_decode($is_totalizer_data, true);

                    // Ensure `json_decode` was successful and check if the array has elements
                    if (is_array($dataArray) && count($dataArray) > 0) {
                        // Update Data A with values from Data B
                        // echo $recon_data['created_at'];

                        $dataA = $nozzel_data; // Nozzle data as Data A
                        $dataB = $totalize_data; // Totalizer data as Data B

                        $dataBMap = [];
                        foreach ($dataB as $nozzle) {
                            $dataBMap[$nozzle['id']] = $nozzle['opening'];
                        }

                        foreach ($dataA as &$nozzle) {
                            $id = $nozzle['id'];
                            if (isset($dataBMap[$id])) {
                                // Update the closing value with the opening value from Data B
                                $nozzle['closing'] = $dataBMap[$id];
                            } else {
                                // Set opening and closing to "---" for nozzles not present in Data B
                                $nozzle['opening'] = "---";
                                $nozzle['closing'] = "---";
                            }
                        }


                        $updatedDataA = json_encode($dataA, JSON_PRETTY_PRINT);
                        $recon[count($recon) - 1]['is_totalizer_data'] = $updatedDataA;
                        $recon[] = $recon_data;
                    } else {
                        // Handle cases where `is_totalizer_data` is invalid or empty
                        $duplicate_recon_data = $recon_data;
                        $duplicate_recon_data['is_totalizer_data'] = $duplicate_recon_data['nozzel'];

                        $dataArray = json_decode($duplicate_recon_data['is_totalizer_data'], true);
                        if (is_array($dataArray)) {
                            foreach ($dataArray as &$entry) {
                                $entry['opening'] = "---";
                                $entry['closing'] = "---";
                            }
                            $duplicate_recon_data['is_totalizer_data'] = json_encode($dataArray, JSON_PRETTY_PRINT);
                        }

                        $recon[] = $duplicate_recon_data;
                    }
                }
            }

            // Combine product with tasks and reconciliation data
            $combined_data[] = [
                'product_id' => $product_id,
                'product_name' => $product_name,
                'dealer_name' => $dealer_name,
                'dates' => $date_header,
                'recons' => $recon
            ];
        }

        // Return the combined data as JSON
        // echo json_encode($combined_data);

        $combined_data = utf8ize($combined_data);
        $json = json_encode($combined_data, JSON_PRETTY_PRINT);

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