<?php
//fetch.php  
include("../config.php");


$access_key = '03201232927';

$pass = $_GET["key"];
$id = $_GET["id"];
if ($pass != '') {
    if ($pass == $access_key) {
        $sql_query1 = "SELECT od.*,dl.name as dealer_name,us.name as username FROM omcs_dealers AS od 
        join dealers as dl on dl.id=od.old_dealer_id
        join users as us on us.id=od.created_by
        where od.id=$id;";

        $result1 = $db->query($sql_query1) or die("Error :" . mysqli_error());

        $thread = array();
        while ($user = $result1->fetch_assoc()) {
            $thread[] = $user;
        }
        echo json_encode($thread);

    } else {
        echo 'Wrong Key...';
    }

} else {
    echo 'Key is Required';
}


?>