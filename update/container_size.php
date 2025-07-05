<?php
include("../config.php");
session_start();
if (isset($_POST)) {


   $row_id=$_POST['row_id'];
   $name=$_POST['name'];
    
    // echo 'HAmza';



    $query = "UPDATE `containers_sizes` SET `sizes`='$name' WHERE id='$row_id';";


    if (mysqli_query($db, $query)) {

        $output= 1;
    } else {
        $output = 'Error' . mysqli_error($db) . '<br>' . $query;

    }




    echo $output;
}
?>
