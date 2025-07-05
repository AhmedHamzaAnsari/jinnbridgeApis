<?php
//fetch.php  
include("../../config.php");


$access_key = '03201232927';

$pass = $_GET["key"];
if ($pass != '') {
    if ($pass == $access_key) {

        $id = $_GET["id"];

        // Initialize an array to store the data
        $data = [];
        $month_series = 1;


        $sql = "SELECT om.*,dl.name as dealer_name,dl.email,dl.location,dl.contact FROM lubes_order_main as om 
        join dealers as dl on dl.id=om.dealer_id where om.id='$id' order by om.id desc;";

        $result = $db->query($sql);


        $dealerProductCounts = [];
        $myArray = [];

        while ($row = $result->fetch_assoc()) {
            $id = $row["id"];
            $dealer_name = $row["dealer_name"];
            $total_amount = $row["total_amount"];
            $created_at = $row["created_at"];
            $email = $row["email"];
            $location = $row["location"];
            $contact = $row["contact"];

            $dealerProductCounts = [];
            $myArray = [];

            $get_orders = "SELECT os.*,pc.name as category,ps.name as size_name,ps.ctn_size,ps.ctn_qty,(os.price*os.qty) as amount,pp.name as product_name,pp.code as product_code FROM lubes_order_sub as os
            join lubes_product_category as pc on pc.id=os.cat_id
            join lubes_product_sizes as ps on ps.id=os.size_id
            join lubes_product as pp on pp.id=os.product_id where os.main_id='$id';";
            // echo $get_orders .'<br>';
            $result_orders = $db->query($get_orders);

            while ($row_2 = $result_orders->fetch_assoc()) {


                // Push the values into the array
                // $myArray[$productType] = $count;
                $myArray[] = $row_2;
            }

            $dealerProductCounts = [

                "order_id" => $id,
                "dealer_name" => $dealer_name,
                "total_amount" => $total_amount,
                "created_at" => $created_at,
                "email" => $email,
                "location" => $location,
                "contact" => $contact,
                "product" => $myArray,
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