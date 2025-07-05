<?php
//fetch.php  
include("../../config.php");


$access_key = '03201232927';

$pass = $_GET["key"];
if ($pass != '') {
    if ($pass == $access_key) {
       
        $v_lat = floatval($_GET['i_lat']);
        $v_lng = floatval($_GET['i_lng']);

        $c_lat = floatval($_GET['d_lat']);
        $c_lng = floatval($_GET['d_lng']);
        $km = 0.12000;

        $ky = 40000 / 360;
        $kx = cos(pi() * $c_lat / 180.0) * $ky;
        $dx = abs($c_lng - $v_lng) * $kx;
        $dy = abs($c_lat - $v_lat) * $ky;
      



        if (sqrt(($dx * $dx) + ($dy * $dy)) <= $km == true) {
            

            echo 'IN';
        } else {
            
            echo 'Not IN ';
           
        }

    } else {
        echo 'Wrong Key...';
    }

} else {
    echo 'Key is Required';
}

?>