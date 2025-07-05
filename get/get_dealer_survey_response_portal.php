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


        $sql = "SELECT * FROM survey_category order by id asc;";

        $result = $db->query($sql);


        $dealerProductCounts = [];
        $myArray = [];

        while ($row = $result->fetch_assoc()) {
            $id = $row["id"];
            $name = $row["name"];

            $dealerProductCounts = [];
            $myArray = [];

            $get_orders = "SELECT sr.*,sq.question,rf.file as cancel_file FROM survey_response as sr 
            join survey_category_questions as sq on sq.id=sr.question_id
            LEFT JOIN survey_response_files rf ON (rf.question_id = sr.question_id and rf.inspection_id=sr.inspection_id)
            where sr.category_id=$id and sr.inspection_id='$inspection_id' and sr.dealer_id='$dealer_id';";
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