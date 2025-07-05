<?php
include("../config.php");
session_start();
if (isset($_POST)) {


   $orderinfo_id=$_POST['orderinfo_id'];
    
    // echo 'HAmza';



    $query = "UPDATE `order_info` SET `is_shortage`='1' WHERE id='$orderinfo_id';";


    if (mysqli_query($db, $query)) {

        $output= 1;
    } else {
        $output = 'Error' . mysqli_error($db) . '<br>' . $query;

    }




    echo $output;
}
?>
