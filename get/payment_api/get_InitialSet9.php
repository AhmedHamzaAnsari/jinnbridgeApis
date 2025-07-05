<?php


$access_key = '03201232927';

$pass = $_GET["key"];
if ($pass != '') {
    if ($pass == $access_key) {
        $SalesOrder = $_GET["SalesOrder"];

        $url = 'http://110.38.69.114:5003/jinnbridgeApis/get/payment_api/InitialSet9.php?SalesOrder=' . $SalesOrder . ''; // Replace '...' with the provided data string
        $data = file_get_contents($url);

        // Extracting the relevant information from the data string
        $startPos = strpos($data, '[');
        $endPos = strrpos($data, ']');

       echo $jsonData = substr($data, $startPos, $endPos - $startPos + 1);

        // Decoding the JSON string into a PHP array
        // echo $arrayData = json_decode($jsonData, true);



    } else {
        echo 'Wrong Key...';
    }

} else {
    echo 'Key is Required';
}

?>