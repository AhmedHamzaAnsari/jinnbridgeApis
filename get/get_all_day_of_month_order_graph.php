<?php
//fetch.php  
include("../config.php");


$access_key = '03201232927';

$pass = $_GET["key"];
if ($pass != '') {
    $dealer_id = $_GET["dealer_id"];
    if ($pass == $access_key) {


        $allMonths = [
            'January', 'February', 'March', 'April', 'May', 'June',
            'July', 'August', 'September', 'October', 'November', 'December'
        ];
        
        // Initialize an array to store the data
        $data = [];
        $month_series = 1;
        foreach ($allMonths as $month) {
            // SQL query to fetch product counts for each dealer for the current month
            $sql = "SELECT
            od.cus_id,
            od.product_type,
            dp.name,
            count(od.product_type) AS total_product_count
        FROM order_detail as od
        left join dealers_products as dp on dp.id=od.product_type
        WHERE MONTH(od.date) = $month_series and  YEAR(od.date) = YEAR(CURRENT_DATE) and od.cus_id=$dealer_id
        GROUP BY od.cus_id, od.product_type";
        
            $result = $db->query($sql);
        
            // Initialize an array to store product counts for each dealer
            $dealerProductCounts = [];
            $myArray = [];
        
            while ($row = $result->fetch_assoc()) {
                $dealerId = $row["cus_id"];
                $productType = $row["name"];
                $productCount = (int)$row["total_product_count"];
        
                // if (!isset($dealerProductCounts[$dealerId])) {
                //     $dealerProductCounts[$dealerId] = [];
                //     $myArray[] = 0;
                // }
                // echo $productCount .'<br>';
                $dealerProductCounts[$dealerId][$productType] = $productCount;
                $myArray[] = $productCount;
            }
        
            // Format the data for the current month
            $formattedData = [
                "name" => $month,
                "data" => $dealerProductCounts,
            ];
        
            $data[] = $formattedData;
            $month_series ++;
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