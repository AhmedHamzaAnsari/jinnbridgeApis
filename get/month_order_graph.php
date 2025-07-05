<?php
//fetch.php  
include("../config.php");


$access_key = '03201232927';

$pass = $_GET["key"];
if ($pass != '') {
    $dealer_id = $_GET["dealer_id"];
    if ($pass == $access_key) {


        $allMonths = [
            'Jan',
            'Feb',
            'Mar',
            'Apr',
            'May',
            'Jun',
            'Jul',
            'Aug',
            'Sep',
            'Oct',
            'Nov',
            'Dec'
        ];

        // Initialize an array to store the data
        $data = [];
        $month_series = 1;
        foreach ($allMonths as $month) {
            // SQL query to fetch product counts for each dealer for the current month
            $sql = "SELECT * FROM dealers_products where dealer_id=$dealer_id";

            $result = $db->query($sql);

            // Initialize an array to store product counts for each dealer
            $dealerProductCounts = [];
            $myArray = [];

            while ($row = $result->fetch_assoc()) {
                $id = $row["id"];
                $name = $row["name"];



                $get_orders = "SELECT count(*) as order_count FROM order_detail where cus_id=$dealer_id and product_type=$id and MONTH(date) = $month_series and  YEAR(date) = YEAR(CURRENT_DATE)";
                // echo $get_orders .'<br>';
                 $result_orders = $db->query($get_orders);

                while ($row_2 = $result_orders->fetch_assoc()) {
                    $order_count = $row_2["order_count"];

                    $productType = $name; // Replace this with your actual data source
                    $count = $row_2['order_count']; // Replace this with your actual data source

                    // Push the values into the array
                    // $myArray[$productType] = $count;
                    $myArray[] = array($productType =>intval($count));
                }
            }

            // Format the data for the current month
            $formattedData = [
                "name" => $month,
                "data" => $myArray,
            ];

            $data[] = $formattedData;
            $month_series++;
        }

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