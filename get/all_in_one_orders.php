<?php
//fetch.php  
include("../config.php");


$access_key = '03201232927';

$pass = $_GET["key"];
$pre = $_GET["pre"];
$id = $_GET["user_id"];
if ($pass != '') {
    if ($pass == $access_key) {



        // Initialize an array to store the data
        $data = [];
        $month_series = 1;

        if ($pre == 'ZM') {

            $sql_query1 = "SELECT om.*,geo.consignee_name,dl.name ,
            CASE
                    WHEN om.status = 0 THEN 'Pending'
                    WHEN om.status = 1 THEN 'Pushed'
                    WHEN om.status = 3 THEN 'Complete'
                    WHEN om.status = 2 THEN 'Cancel'
                    WHEN om.status = 4 THEN 'Special Approval'
                    WHEN om.status = 5 THEN 'ASM Approved'
                END AS current_status
            FROM order_main as om 
           left join geofenceing as geo on geo.id=om.depot
            join dealers as dl on dl.id=om.created_by where om.status IN(0,5) and dl.zm='$id' order by om.id desc;";
        } elseif ($pre == 'TM') {

            $sql_query1 = "SELECT om.*,geo.consignee_name,dl.name ,
            CASE
                    WHEN om.status = 0 THEN 'Pending'
                    WHEN om.status = 1 THEN 'Pushed'
                    WHEN om.status = 3 THEN 'Complete'
                    WHEN om.status = 2 THEN 'Cancel'
                    WHEN om.status = 4 THEN 'Special Approval'
                    WHEN om.status = 5 THEN 'ASM Approved'
                END AS current_status
            FROM order_main as om 
           left join geofenceing as geo on geo.id=om.depot
            join dealers as dl on dl.id=om.created_by where om.status IN(0,5) and dl.tm='$id' order by om.id desc;";
        } elseif ($pre == 'ASM') {
            $sql_query1 = "SELECT om.*,geo.consignee_name,dl.name ,
            CASE
                    WHEN om.status = 0 THEN 'Pending'
                    WHEN om.status = 1 THEN 'Pushed'
                    WHEN om.status = 3 THEN 'Complete'
                    WHEN om.status = 2 THEN 'Cancel'
                    WHEN om.status = 4 THEN 'Special Approval'
                    WHEN om.status = 5 THEN 'ASM Approved'
                END AS current_status
            FROM order_main as om 
           left join geofenceing as geo on geo.id=om.depot
            join dealers as dl on dl.id=om.created_by where om.status IN(0,5) and dl.asm='$id' order by om.id desc;";

        } else {

            $sql_query1 = "SELECT om.*,geo.consignee_name,dl.name ,
            CASE
                    WHEN om.status = 0 THEN 'Pending'
                    WHEN om.status = 1 THEN 'Pushed'
                    WHEN om.status = 3 THEN 'Complete'
                    WHEN om.status = 2 THEN 'Cancel'
                    WHEN om.status = 4 THEN 'Special Approval'
                    WHEN om.status = 5 THEN 'ASM Approved'
                END AS current_status
            FROM order_main as om 
           left join geofenceing as geo on geo.id=om.depot
            join dealers as dl on dl.id=om.created_by where om.status IN(0,5) order by om.id desc;";
        }

        // SQL query to fetch product counts for each dealer for the current month

        $result = $db->query($sql_query1);

        // Initialize an array to store product counts for each dealer
        $dealerProductCounts = [];
        $myArray = [];

        while ($row = $result->fetch_assoc()) {
            $id = $row["id"];
            $created_at = $row["created_at"];
            $name = $row["name"];
            $type = $row["type"];
            $consignee_name = $row["consignee_name"];
            $total_amount = $row["total_amount"];
            $legder_balance = $row["legder_balance"];
            $current_status = $row["current_status"];

            $dealerProductCounts = [];
            $myArray = [];

            $get_orders = "SELECT od.*,geo.name as d_name,geo_d.consignee_name,dp.name as product_name FROM order_detail as od 
                join dealers as geo on geo.id = od.cus_id 
               left join geofenceing as geo_d on geo_d.id=od.depot
                join dealers_products as dp on dp.id=od.product_type
                where od.main_id = $id  order by od.id desc";
            // echo $get_orders .'<br>';
            $result_orders = $db->query($get_orders);

            while ($row_2 = $result_orders->fetch_assoc()) {


                // Push the values into the array
                // $myArray[$productType] = $count;
                $myArray[] = $row_2;
            }

            $dealerProductCounts = [
                "id" => $id,
                "created_at" => $created_at,
                "name" => $name,
                "type" => $type,
                "consignee_name" => $consignee_name,
                "total_amount" => $total_amount,
                "legder_balance" => $legder_balance,
                "current_status" => $current_status,
                "results" => $myArray,
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