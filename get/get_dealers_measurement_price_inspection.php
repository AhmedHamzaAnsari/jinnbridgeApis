<?php
//fetch.php  
include("../config.php");


$access_key = '03201232927';

$pass = $_GET["key"];
if ($pass != '') {
    if ($pass == $access_key) {
        $inspection_id = $_GET["inspection_id"];
        $dealer_id = $_GET["dealer_id"];
        $task_id = $_GET["task_id"];

        // Initialize an array to store the data
        $data = [];
        $month_series = 1;


        $sql = "SELECT * FROM dealer_measurement_pricing_action where task_id=$task_id";

        $result = $db->query($sql);


        $dealerProductCounts = [];
        $myArray = [];

        while ($row = $result->fetch_assoc()) {
            $id = $row["id"];

            $dealerProductCounts = [];
            $myArray = [];

            $get_orders = "SELECT mp.*,dc.name as dispensor_name FROM dealer_measurement_pricing as mp 
            join dealers_dispenser as dc on dc.id=mp.dispenser_id where mp.main_id='$id'";
            // echo $get_orders .'<br>';
            $result_orders = $db->query($get_orders);

            while ($row_2 = $result_orders->fetch_assoc()) {


                // Push the values into the array
                // $myArray[$productType] = $count;
                $myArray[] = $row_2;
            }

            $dealerProductCounts = [

                "main_data" => $row,
                "sub_data" => $myArray,
            ];
            $data[] = $dealerProductCounts;
        }

        // Format the data for the current month

        $month_series++;


        // Convert the array to a JSON string
        $jsonData = json_encode($data);

        // Output the JSON string
        // echo $jsonData;


        // Output the JSON string
        echo $jsonData;


    } else {
        echo 'Wrong Key...';
    }

} else {
    echo 'Key is Required';
}


?>