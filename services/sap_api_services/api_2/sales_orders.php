<?php
//fetch.php  
ini_set('max_execution_time', '0');
$url1 = $_SERVER['REQUEST_URI'];
header("Refresh: 60; URL=$url1");
include("../../../config.php");
set_time_limit(5000); // 
// file_put_contents('reload_log.txt', 'Page reloaded at ' . date('Y-m-d H:i:s') . PHP_EOL, FILE_APPEND);


$access_key = '03201232927';

$pass = $_GET["key"];
$date = date('Y-m-d H:i:s');
if ($pass != '') {
    if ($pass == $access_key) {

        $sql_query1 = "SELECT * FROM order_main where delivered_status=0 order by id desc";

        $result1 = $db->query($sql_query1) or die("Error :" . mysqli_error($db));

        $thread = array();
        while ($user = $result1->fetch_assoc()) {

            $id = $user['id'];
            $sap = $user['dealer_sap'];
            $SaleOrder = $user['SaleOrder'];

            // echo $sap .'<br>';

            // echo 'http://172.20.230.71:8080/flowbridgeApis/services/get_dealer_ledger.php?sap='.$sap.'' . '<br>';

            $url = 'http://172.20.230.71:8080/flowbridgeApis/services/sap_api_services/api_2/api_2_data_check.php?sap=' . $sap . ''; // Replace '...' with the provided data string
            $data = file_get_contents($url);

            // Extracting the relevant information from the data string
            $startPos = strpos($data, '[');
            $endPos = strrpos($data, ']');

            $jsonData = substr($data, $startPos, $endPos - $startPos + 1);

            // Decoding the JSON string into a PHP array
            $arrayData = json_decode($jsonData, true);
            // print_r($arrayData);
            // Encoding the array as JSON for better formatting
            // $jsonFormatted = json_encode($arrayData, JSON_PRETTY_PRINT);

            // Output the formatted JSON
            // echo $jsonFormatted;
            $netValue = null;
            if ($arrayData) {

                $search_value = $SaleOrder;

                // Flag to indicate whether the value is found
                $value_found = false;

                // Iterate through the array to search for the value
                foreach ($arrayData as $item) {
                    if ($item['ORDER_NO'] == $search_value) {
                        $value_found = true;
                        // Print the details of the found item
                        echo "Order found:\n";
                        // print_r($item);
                        $ORDER_NO = $item['ORDER_NO'];
                        // $netValue = $item["NET_VALUE"];
                        echo $RETAIL_SITE_NAME = $item['RETAIL_SITE_NAME'];
                        // echo '<br>';
                        $ORDER_DATE = $item['ORDER_DATE'];
                        $ORDER_TIME = $item['ORDER_TIME'];
                        $DELIVERY_TYPE = $item['DELIVERY_TYPE'];
                        // echo '<br>';

                        $ORDER_STATUS = $item['ORDER_STATUS'];

                        $selected = "SELECT * FROM order_detail_log where sales_order='$ORDER_NO' order by id desc limit 1;";
                        $result_selected = mysqli_query($db, $selected);
                        $selected_row = mysqli_fetch_array($result_selected);

                        $count = mysqli_num_rows($result_selected);

                        $up = date('Y-m-d H:i:s');
                        if ($count > 0) {
                            $status_value = $selected_row['status_value'];
                            if ($ORDER_STATUS != $status_value) {

                                $log = "INSERT INTO `order_detail_log`
                                (`order_id`,
                                `sales_order`,
                                `status`,
                                `status_value`,
                                `created_at`,
                                `created_by`)
                                VALUES
                                ('$id',
                                '$SaleOrder',
                                '<{status: }>',
                                '$ORDER_STATUS',
                                '$date',
                                '1'); ";
                                if (mysqli_query($db, $log)) {
                                    $output = 1;

                                    $update = "UPDATE `order_main`
                                    SET
                                    `status_value` = '$ORDER_STATUS',
                                    `approved_time` = '$up'
                                    WHERE `id` = '$id';";

                                    if (mysqli_query($db, $update)) {
                                        $output = 1;



                                    } else {
                                        $output = 'Error' . mysqli_error($db) . '<br>';

                                    }

                                } else {
                                    $output = 'Error' . mysqli_error($db) . '<br>';

                                }
                            }
                        } else {
                            $log = "INSERT INTO `order_detail_log`
                                (`order_id`,
                                `sales_order`,
                                `status`,
                                `status_value`,
                                `created_at`,
                                `created_by`)
                                VALUES
                                ('$id',
                                '$SaleOrder',
                                '<{status: }>',
                                '$ORDER_STATUS',
                                '$date',
                                '1'); ";
                            if (mysqli_query($db, $log)) {
                                $output = 1;

                                $update = "UPDATE `order_main`
                                    SET
                                    `status_value` = '$ORDER_STATUS',
                                    `approved_time` = '$up'
                                    WHERE `id` = '$id';";

                                if (mysqli_query($db, $update)) {
                                    $output = 1;



                                } else {
                                    $output = 'Error' . mysqli_error($db) . '<br>';

                                }

                            } else {
                                $output = 'Error' . mysqli_error($db) . '<br>';

                            }
                        }






                        break; // No need to continue searching if found
                    }
                }

                // If the value is not found, print a message
                if (!$value_found) {
                    echo "Order not found.\n";
                }

                //     $searchValue = $SaleOrder;
                //     foreach ($arrayData as $item) {
                // // print_r($item);

                //         if ($item["ORDER_NO"] == $searchValue) {
                // $ORDER_NO = $item['ORDER_NO'];
                // // $netValue = $item["NET_VALUE"];
                // echo $RETAIL_SITE_NAME = $item['RETAIL_SITE_NAME'];
                // // echo '<br>';
                // $ORDER_DATE = $item['ORDER_DATE'];
                // $ORDER_TIME = $item['ORDER_TIME'];
                // $DELIVERY_TYPE = $item['DELIVERY_TYPE'];
                // // echo '<br>';

                // $ORDER_STATUS = $item['ORDER_STATUS'];
                // $log = "INSERT INTO `order_detail_log`
                // (`order_id`,
                // `sales_order`,
                // `status`,
                // `status_value`,
                // `created_at`,
                // `created_by`)
                // VALUES
                // ('$id',
                // '$SaleOrder',
                // '<{status: }>',
                // '$ORDER_STATUS',
                // '$date',
                // '1'); ";
                // if (mysqli_query($db, $log)) {
                //     $output = 1;

                // } else {
                //     $output = 'Error' . mysqli_error($db) . '<br>';

                // }

                //         }
                //         break; // Exit the loop once found
                //     }



            }

            // echo $output;

        }


    } else {
        echo 'Wrong Key...';
    }

} else {
    echo 'Key is Required';
}

echo date('Y-m-d H:i:s');
?>