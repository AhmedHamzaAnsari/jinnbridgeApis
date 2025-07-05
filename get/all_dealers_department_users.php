<?php
//fetch.php  
include("../config.php");


$access_key = '03201232927';

$pass = $_GET["key"];
$is_role = $_GET["is_role"];
$user_id = $_GET["user_id"];

if ($pass != '') {
    if ($pass == $access_key) {
        if ($is_role != 0) {

            $sql_query1 = "SELECT * FROM dealers ;";

            $result1 = $db->query($sql_query1) or die("Error :" . mysqli_error($db));

            $thread = array();
            while ($user = $result1->fetch_assoc()) {
                $thread[] = $user;
            }
            echo json_encode($thread);
        } else {
            $thread = array();
            if($user_id!=''){
                $sql_query2 = "SELECT * FROM dealers where asm='$user_id';";

                $result2 = $db->query($sql_query2) or die("Error :" . mysqli_error($db));

                $thread = array();
                while ($user = $result2->fetch_assoc()) {
                    $thread[] = $user;
                }
                echo json_encode($thread);
                
            }
            else{
                echo json_encode($thread);

            }

            
        }

    } else {
        echo 'Wrong Key...';
    }

} else {
    echo 'Key is Required';
}


?>