<?php
//fetch.php  
include("../config.php");


$access_key = '03201232927';

$pass = $_GET["key"];
$id=$_GET['id'];
if ($pass != '') {
    if ($pass == $access_key) {
        $id = $_GET['id'];

        $sql = "DELETE FROM `containers_sizes` WHERE id='$id'";

        // echo $sql;

        if(mysqli_query($db, $sql)){
            echo 1;
        }
        else{
            echo 'Error' . mysqli_error($db) . '<br>' . $query;
        }
      

    } else {
        echo 'Wrong Key...';
    }

} else {
    echo 'Key is Required';
}


?>
