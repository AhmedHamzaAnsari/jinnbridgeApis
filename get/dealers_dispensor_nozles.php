<?php
//fetch.php  
include("../config.php");


$access_key = '03201232927';

$pass = $_GET["key"];
if ($pass != '') {
    if ($pass == $access_key) {

        $dealer_id = $_GET["dealer_id"];

        // Initialize an array to store the data
        $data = [];
        $month_series = 1;


        $sql = "SELECT * FROM dealers_dispenser where dealer_id=$dealer_id;";

        $result = $db->query($sql);


        $dealerProductCounts = [];
        $myArray = [];

        while ($row = $result->fetch_assoc()) {
            $id = $row["id"];
            $name = $row["name"];

            $dealerProductCounts = [];
            $myArray = [];

            $get_orders = "SELECT dz.*,ap.name as product_name,ds.name as dispenser_name,dl.lorry_no as tank_name,
            (SELECT new_reading FROM dealers_nozzel_readings where nozle_id=dz.id order by id desc limit 1) as new_reading 
            FROM dealers_nozzel dz 
            join dealers_products as dp on dp.id=dz.products
            join dealers_dispenser as ds on ds.id=dz.dispenser_id
            join dealers_lorries as dl on dl.id=dz.tank_id 
            join all_products as ap on ap.name=dp.name where dz.dispenser_id='$id' and dz.dealer_id='$dealer_id';";
            // echo $get_orders .'<br>';
            $result_orders = $db->query($get_orders);

            while ($row_2 = $result_orders->fetch_assoc()) {


                // Push the values into the array
                // $myArray[$productType] = $count;
                $myArray[] = $row_2;
            }

            $dealerProductCounts = [

                "id" => $id,
                "name" => $name,
                "nozels" => $myArray,
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