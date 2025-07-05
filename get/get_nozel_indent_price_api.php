<?php
//fetch.php  
include("../config.php");


$access_key = '03201232927';

$pass = $_GET["key"];
if ($pass != '') {
    if ($pass == $access_key) {
        // $sql_query1 = "SELECT * FROM geofenceing where geotype='depot';";

        // $result1 = $db->query($sql_query1) or die("Error :" . mysqli_error());

        // $thread = array();
        // while ($user = $result1->fetch_assoc()) {
        //     $thread[] = $user;
        // }


        $dealersData = [
            [
                'dealer_id' => 1,
                'HOB_indent_price' => 5000,
                'HOB_nozel_price' => 5000,
                'from_date' => '2023-11-01',
                'to_date' => '2023-11-30'
            ],
            [
                'dealer_id' => 2,
                'HOB_indent_price' => 6000,
                'HOB_nozel_price' => 5000,
                'from_date' => '2023-11-15',
                'to_date' => '2023-12-15'
            ],
            [
                'dealer_id' => 3,
                'HOB_indent_price' => 4500,
                'HOB_nozel_price' => 5000,
                'from_date' => '2023-11-10',
                'to_date' => '2023-12-10'
            ],
            [
                'dealer_id' => 4,
                'HOB_indent_price' => 5500,
                'HOB_nozel_price' => 5000,
                'from_date' => '2023-11-05',
                'to_date' => '2023-12-05',
            ],
            [
                'dealer_id' => 5,
                'HOB_indent_price' => 4800,
                'HOB_nozel_price' => 5000,
                'from_date' => '2023-11-20',
                'to_date' => '2023-12-20'
            ],
            [
                'dealer_id' => 6,
                'HOB_indent_price' => 5200,
                'HOB_nozel_price' => 5000,
                'from_date' => '2023-11-25',
                'to_date' => '2023-12-25'
            ],
            [
                'dealer_id' => 7,
                'HOB_indent_price' => 4900,
                'HOB_nozel_price' => 5000,
                'from_date' => '2023-11-08',
                'to_date' => '2023-12-08',
            ],
            [
                'dealer_id' => 8,
                'HOB_indent_price' => 5100,
                'HOB_nozel_price' => 5000,
                'from_date' => '2023-11-12',
                'to_date' => '2023-12-12'
            ]
        ];

        // You can add more dealer data arrays to the main array if needed.



        echo json_encode($dealersData);

    } else {
        echo 'Wrong Key...';
    }

} else {
    echo 'Key is Required';
}


?>