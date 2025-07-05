<?php
//fetch.php  
include("../../config.php");


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


        $sql = "SELECT distinct(category_id) FROM jinnbridge.survey_response_eng_new order by category_id asc;";

        $result = $db->query($sql);


        $dealerProductCounts = [];
        $myArray = [];

        while ($row = $result->fetch_assoc()) {
            $category_id = $row["category_id"];

            $dealerProductCounts = [];
            $myArray = [];

            $get_orders = "SELECT sr.*,
            (SELECT file as cancel_file FROM survey_response_files_eng where question_id=sr.question_id and category_id=sr.category_id and inspection_id=sr.inspection_id order by id desc limit 1) as cancel_file
            FROM survey_response_eng_new as sr 
            where sr.category_id='$category_id' and sr.inspection_id='$inspection_id' and sr.dealer_id='$dealer_id';";
            // echo $get_orders .'<br>';
            $result_orders = $db->query($get_orders);

            while ($row_2 = $result_orders->fetch_assoc()) {


                // Push the values into the array
                // $myArray[$productType] = $count;
                $myArray[] = $row_2;
            }

            $dealerProductCounts = [

                "id" => '',
                "name" => $category_id,
                "Questions" => $myArray,
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