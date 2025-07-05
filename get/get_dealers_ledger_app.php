<?php
//fetch.php  
include ("../config.php");


$access_key = '03201232927';

$pass = $_GET["key"];
if ($pass != '') {
    if ($pass == $access_key) {
        // $sql_query1 = "SELECT * FROM omcs order by name asc";

        // $result1 = $db->query($sql_query1) or die("Error :" . mysqli_error($db));

        // $thread = array();
        // while ($user = $result1->fetch_assoc()) {
        //     $thread[] = $user;
        // }
        // echo json_encode($thread);

        $dealers_sap = $_GET['sap'];
        $from = $_GET['from'];
        $to = $_GET['to'];
        $user_id = $_GET['user_id'];
        $date = date('Y-m-d H:i:s');
        $insert = "INSERT INTO `request_for_dealers_ledger`
        (`customer_sap`,
        `from`,
        `to`,
        `created_at`,
        `created_by`)
        VALUES
        ('$dealers_sap',
        '$from',
        '$to',
        '$date',
        '$user_id');";


        if (mysqli_query($db, $insert)) {

            echo 'Requested';

        } else {
            echo 'Error' . mysqli_error($db) . '<br>' . $insert;

        }



    } else {
        echo 'Wrong Key...';
    }

} else {
    echo 'Key is Required';
}

?>