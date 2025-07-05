<?php
//fetch.php  
include("../config.php");


$access_key = '03201232927';

$pass = $_GET["key"];
if ($pass != '') {
    if ($pass == $access_key) {

        $curl = curl_init();

        curl_setopt_array(
            $curl,
            array(
                CURLOPT_URL => 'http://110.38.69.114:5003/jinnbridgeApis/get/get_nozel_indent_price_api.php?key=03201232927',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'GET',
            )
        );

        $response = curl_exec($curl);

        curl_close($curl);
        $dealerData = json_decode($response, true);
        $datetime = date('Y-m-d H:i:s');
        // echo $response;

        foreach ($dealerData as $dealer) {
            $id = $dealer['dealer_id'];
            $indent_price = $dealer['indent_price'];
            $nozel_price = $dealer['nozel_price'];
            $from_date = $dealer['from_date'];
            $to_date = $dealer['to_date'];


            echo $indent_price .'<br>';

            $update = "UPDATE `dealers` SET 
            `indent_price`='$indent_price',
            `Nozel_price`='$nozel_price'
             WHERE id=$id";

            if (mysqli_query($db, $update)) {


                $log = "INSERT INTO `dealer_nozel_price_log`
                (`dealer_id`,
                `indent_price`,
                `nozel_price`,
                `from`,
                `to`,
                `created_at`,
                `created_by`)
                VALUES
                ('$id',
                '$indent_price',
                '$nozel_price',
                '$from_date',
                '$to_date',
                '$datetime',
                '1');";
                if (mysqli_query($db, $log)) {
                    $output = 1;

                } else {
                    $output = 'Error' . mysqli_error($db) . '<br>' . $log;

                }

            } else {
                $output = 'Error' . mysqli_error($db) . '<br>' . $query;

            }


        }


    } else {
        echo 'Wrong Key...';
    }

} else {
    echo 'Key is Required';
}


?>