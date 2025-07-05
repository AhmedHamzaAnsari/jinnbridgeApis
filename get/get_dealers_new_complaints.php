<?php
//fetch.php  
include("../config.php");


$access_key = '03201232927';

$pass = $_GET["key"];
if ($pass != '') {
    $dealer_id = $_GET["dealer_id"];
    if ($pass == $access_key) {
        $sql_query1 = "SELECT dc.*,op.object_name,cd.name as damage_name FROM dealers_complaint as dc
        join complaint_object_part as op on op.id=dc.object_part_id
        join complaint_damage as cd on cd.id=dc.damage_overview_id where dc.created_by='$dealer_id';";

        $result1 = $db->query($sql_query1) or die("Error :" . mysqli_error($db));

        $thread = array();
        while ($user = $result1->fetch_assoc()) {
            $thread[] = $user;
        }
        echo json_encode($thread);

    } else {
        echo 'Wrong Key...';
    }

} 
else 
{
    echo 'Key is Required';
}


?>