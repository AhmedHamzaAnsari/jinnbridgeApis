<?php
//fetch.php  
include("../../../config.php");
set_time_limit(5000); // 
// file_put_contents('reload_log.txt', 'Page reloaded at ' . date('Y-m-d H:i:s') . PHP_EOL, FILE_APPEND);


$access_key = '03201232927';

$pass = $_GET["key"];
$date = date('Y-m-d H:i:s');
if ($pass != '') {
    if ($pass == $access_key) {

        $sql_query1 = "SELECT * FROM dealers where privilege='Dealer' and sap_no !='' order by id desc;";

        $result1 = $db->query($sql_query1) or die("Error :" . mysqli_error());

        $thread = array();
        while ($user = $result1->fetch_assoc()) {

            $dealers_id = $user['id'];
            $sap = $user['sap_no'];

            // echo '<br>';

            $acount = $user['acount'];

            // echo 'http://172.20.230.71:8080/flowbridgeApis/services/get_dealer_ledger.php?sap='.$sap.'' . '<br>';

            $url = 'http://172.20.230.71:8080/flowbridgeApis/services/sap_api_services/api_1/api_1_data_check.php?sap=' . $sap . ''; // Replace '...' with the provided data string
            $data = file_get_contents($url);

            // Extracting the relevant information from the data string
            $startPos = strpos($data, '[');
            $endPos = strrpos($data, ']');

            $jsonData = substr($data, $startPos, $endPos - $startPos + 1);

            // Decoding the JSON string into a PHP array
            $arrayData = json_decode($jsonData, true);
            print_r($arrayData);
            // Encoding the array as JSON for better formatting
            // $jsonFormatted = json_encode($arrayData, JSON_PRETTY_PRINT);

            // Output the formatted JSON
            // echo $jsonFormatted;
            if ($arrayData) {
                foreach ($arrayData as $item) {
                    // $DOC_NO = $item['DOC_NO'];
                    // echo '<br>';
                    echo $RETAIL_SITE_NAME = $item['RETAIL_SITE_NAME'];
                    // $DEBIT_CREDIT = $item['DEBIT_CREDIT'];
                    // $LEDGER_BALANCE = $item['LEDGER_BALANCE'];
                    // $DATE = $item['DATE'];
                    // // echo '<br>';

                    // $ASSIGNMENT_NO = $item['ASSIGNMENT_NO'];
                    // $DOCUMENT_TYPE = $item['DOCUMENT_TYPE'];




                    $log = "UPDATE `dealers`
                    SET
                    `is_found_in_sap_api` = '1'
                    WHERE `id` = '$dealers_id';;
                            ";
                    if (mysqli_query($db, $log)) {
                        $output = 1;

                    } else {
                        $output = 'Error' . mysqli_error($db) . '<br>';

                    }

                }


                // $updated = "SELECT * FROM dealer_ledger_log where dealer_id='$dealers_id' and sap_no='$sap' order by datetime desc limit 1;";

                // $resultupdated = $db->query($updated) or die("Error :" . mysqli_error($db));

                // $thread = array();
                // while ($user3 = $resultupdated->fetch_assoc()) {
                //     // $thread[] = $user;
                //     $ledgers = $user3['new_ledger'];

                //      $query = "UPDATE `dealers` SET 
                //         `acount`='$ledgers' WHERE id=$dealers_id and sap_no='$sap'";


                //     if (mysqli_query($db, $query)) {
                //         // echo 'Dealer Ledger Updated <br>';
                //     } else {
                //         $output = 'Error' . mysqli_error($db) . '<br>' . $query;

                //     }
                // }


            } else {
                $log = "UPDATE `dealers`
                    SET
                    `is_found_in_sap_api` = '0'
                    WHERE `id` = '$dealers_id';;
                            ";
                if (mysqli_query($db, $log)) {
                    $output = 1;

                } else {
                    $output = 'Error' . mysqli_error($db) . '<br>';

                }
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