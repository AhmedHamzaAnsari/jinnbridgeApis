<?php
include("../config.php");
session_start();
if (isset($_POST)) {


   $dealers_id=$_POST['dealers_id'];
    // echo 'HAmza';



    $query = "UPDATE `dealers` SET `no_lorries`='1' WHERE id='$dealers_id';";


    if (mysqli_query($db, $query)) {

        $output= 1;
    } else {
        $output = 'Error' . mysqli_error($db) . '<br>' . $query;

    }




    echo $output;
}
?>
