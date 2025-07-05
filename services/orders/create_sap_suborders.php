<?php
ini_set('max_execution_time', '0');
$url1 = $_SERVER['REQUEST_URI'];
header("Refresh: 20; URL=$url1");
// error_reporting(0);

include("../../config.php");
set_time_limit(5000); // 
// file_put_contents('reload_log.txt', 'Page reloaded at ' . date('Y-m-d H:i:s') . PHP_EOL, FILE_APPEND);


$access_key = '03201232927';

$pass = $_GET["key"];
$date = date('Y-m-d H:i:s');
if ($pass != '') {
    if ($pass == $access_key) {

        $vehicle_name;
        $sql = "SELECT * FROM puma_sap_data where status=0 and vehicle!=''  order by id desc";
        $result = mysqli_query($db, $sql);
        $count = mysqli_num_rows($result);
        // echo $count;
        if ($count > 0) {
            while ($row = mysqli_fetch_array($result)) {
                // $userid = $row['id'];
                $id = $row['id'];
                $product_detail = $row['product_detail'];
                $created_at = $row["created_at"];
                $data = json_decode($product_detail, true);

                // Loop through the array and print each element
                foreach ($data as $item) {
                    $customer = 'customer';
                    $sapNo = 'sapNo';
                    $line_item = 'line_item';
                    $material = 'material';
                    $qty = 'qty';
                    foreach ($item as $key => $value) {
                        if ($key == $customer) {

                            $customer = $value;
                        } elseif ($key == $sapNo) {
                            $sapNo = $value;

                        } elseif ($key == $line_item) {
                            $line_item = $value;

                        } elseif ($key == $material) {
                            $material = $value;

                        } elseif ($key == $qty) {
                            $qty = $value;

                        }
                    }
                    //    echo $customer.'<br>';
                    //    echo $sapNo.'<br>';
                    //    echo $line_item.'<br>';
                    //    echo $material.'<br>';
                    //    echo $qty.'<br>';
                    $net_price = get_net_price($customer, $created_at, $sapNo);
                    $d_sap = ltrim($customer, '0');
                    $query = "INSERT INTO `puma_sap_data_trips`
                     (`main_id`,
                        `dealer_sap`,
                        `salesapNo`,
                     `line_item`,
                     `material`,
                     `qty`,
                     `price`,
                     `created_at`,
                     `created_by`)
                     VALUES
                     ('$id',
                     '$customer',
                     '$sapNo',
                     '$line_item',
                     '$material',
                     '$qty',
                     '$net_price',
                     '$date',
                     '1');";


                    if (mysqli_query($db, $query)) {

                        $output = 1;
                        $update = "UPDATE `puma_sap_data`
                        SET
                        `status` = '1'
                        WHERE `id` = '$id';";

                        if (mysqli_query($db, $update)) {
                            echo 'Updated ';

                        } else {
                            $output = 'Error' . mysqli_error($db) . '<br>' . $update;

                        }

                    } else {
                        $output = 'Error' . mysqli_error($db) . '<br>' . $query;

                    }
                }









            }




        } else {
            echo '<h1>No Records Found to send Msg</h1>';
        }
    }
}

function calculateDistance($originLat, $originLng, $destLat, $destLng)
{
	$apiKey = 'AIzaSyD9ztWZaPapSg_s2x_VIKx2DwO5zq0gcDU';
	$url = "https://maps.googleapis.com/maps/api/distancematrix/json?units=metric&origins={$originLat},{$originLng}&destinations={$destLat},{$destLng}&key={$apiKey}";

	// Make the API request
	$response = file_get_contents($url);
	$data = json_decode($response, true);

	// Check if the request was successful
	if ($data['status'] === 'OK' && isset($data['rows'][0]['elements'][0]['distance']['value'])) {
		$distanceInMeters = $data['rows'][0]['elements'][0]['distance']['value'];
		$distanceInKm = $distanceInMeters / 1000; // Convert meters to kilometers
		return $distanceInKm;
	} else {
		return null; // Error in API request or invalid response
	}
}
function get_net_price($id, $created_at, $sapNo)
{
    $dateString = $created_at;

    // Convert to DateTime object
    $date = new DateTime($dateString);

    // Format the date as YYYYMMDD
    $formattedDate = $date->format("Ymd");

    // Subtract one day
    $date->modify("-10 day");

    // Get the new date after subtracting one day
    $newDate = $date->format("Ymd");

    $url = 'http://110.38.69.114:5003/jinnbridgeApis/services/sap_api_services/api_4/api_4_data_check.php?sap=' . $id . '&from=' . $newDate . '&to=' . $formattedDate . ''; // Replace '...' with the provided data string
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
        // print_r($arrayData);
        // foreach ($arrayData as $item) {
        //     print_r($item);
        //     // $MAT_DESCRIPTION = $item['MAT_DESCRIPTION'];
        //     // // echo '<br>';
        //     // $NET_VALUE = $item['NET_VALUE'];
        //     // $FREIGHT_VALUE = $item['FREIGHT_VALUE'];
        //     // $MATERIAL_ID = $item['MATERIAL_ID'];
        //     // $nozel_price = 0;
        //     // $from_date = '2023-02-16 00:00:00';
        //     // $to_date = '2023-02-28 23:59:59';



        // }

        $searchValue = $sapNo;


        foreach ($arrayData as $innerArray) {
            if ($innerArray["SALES_ORD"] === $searchValue) {
                $netValue = $innerArray["NET_VALUE"];
                break; // Exit the loop once found
            }
        }

        // Output the result
        if ($netValue !== null) {
            // echo "NET_VALUE for SALES_ORD $searchValue: $netValue";
        } else {
            // echo "SALES_ORD $searchValue not found";
        }



    }
    return $netValue;
}

mysqli_close($db);
echo date('Y-m-d H:i:s');
?>