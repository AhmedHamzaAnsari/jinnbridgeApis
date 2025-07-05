<?php
//fetch.php  
include("../../config.php");


$access_key = '03201232927';

$pass = $_GET["key"];
if ($pass != '') {
    if ($pass == $access_key) {
        $id = $_GET["id"];
        $pre = $_GET["pre"];

        if($pre == 'ZM'){

            $sql_query1 = "SELECT GROUP_CONCAT(id ORDER BY id ASC) AS id FROM dealers where zm=$id";
        }
        elseif($pre == 'TM'){
            
            $sql_query1 = "SELECT GROUP_CONCAT(id ORDER BY id ASC) AS id FROM dealers where tm=$id";
        }
        else{
            $sql_query1 = "SELECT GROUP_CONCAT(id ORDER BY id ASC) AS id FROM dealers where asm=$id";

        }


        $result = mysqli_query($db, $sql_query1);
        $row = mysqli_fetch_array($result);

        $count = mysqli_num_rows($result);

        if($count>0){
            $dealer_id = $row['id'];

            $sql_query2 = 'SELECT om.*,dl.name,dl.location,od.quantity,od.rate FROM order_main as om 
            join dealers as dl on dl.id=om.created_by
            join order_detail as od on od.main_id=om.id where om.created_by IN('.$dealer_id.')';
            $result1 = $db->query($sql_query2) or die("Error :" . mysqli_error());
    
            $thread = array();
            while ($user = $result1->fetch_assoc()) {
                $thread[] = $user;
            }
            echo json_encode($thread);
        }


    } else {
        echo 'Wrong Key...';
    }

} else {
    echo 'Key is Required';
}


?>