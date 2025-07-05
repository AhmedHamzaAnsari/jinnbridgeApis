<?php
//fetch.php  
include("../../config.php");


$access_key = '03201232927';

$pass = $_GET["key"];
$rm_id=$_GET["rm_id"];
if ($pass != '') {
    if ($pass == $access_key) {
        $sql_query1 = "SELECT it.*,us.name as user_name,dl.name as dealer_name,dl.`co-ordinates` as co_ordinates,
        CASE
                        WHEN it.status = 0 THEN 'Pending'
                        WHEN it.status = 1 THEN 'Complete'
                        WHEN it.status = 2 THEN 'Cancel'
                    END AS current_status FROM inspector_task as it 
        join users as us on us.id=it.user_id
        join dealers as dl on dl.id=it.dealer_id
        join users_asm_tm as uzt on uzt.asm_id=it.created_by and uzt.tm_id = $rm_id order by it.id desc";

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