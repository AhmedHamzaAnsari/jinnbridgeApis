<?php
//fetch.php  
include("../config.php");
set_time_limit(500); // 


$access_key = '03201232927';

$pass = $_GET["key"];
$date = date('Y-m-d H:i:s');
if ($pass != '') {
    if ($pass == $access_key) {
        $sql_query1 = "SELECT * FROM dealers where privilege='Dealer' and sap_no ='25860505' order by id desc limit 1;";

        $result1 = $db->query($sql_query1) or die("Error :" . mysqli_error());

        $thread = array();
        while ($user = $result1->fetch_assoc()) {

            $dealers_id = $user['id'];
           echo $sap = $user['sap_no'];
           echo '<br>';

            $acount = $user['acount'];

            echo 'http://110.38.69.114:5003/jinnbridgeApis/services/get_dealer_ledger.php?sap='.$sap.'' . '<br>';

            $url = 'http://110.38.69.114:5003/jinnbridgeApis/services/get_dealer_ledger.php?sap=' . $sap . ''; // Replace '...' with the provided data string
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
            


            // echo $output;

        }


    } else {
        echo 'Wrong Key...';
    }

} else {
    echo 'Key is Required';
}


?>