<?php
//fetch.php  
include("../config.php");


$access_key = '03201232927';

$pass = $_GET["key"];
if ($pass != '') {
    $dealer_id = $_GET["dealer_id"];
    if ($pass == $access_key) {
        $sql_query1 = "SELECT lo.*,lg.grade,
        CASE
                       WHEN lo.sap_no = '' THEN 'Pending'
                       WHEN lo.status != ''  THEN 'In-Progress'
                   END AS current_status
       
        FROM lube_order as lo 
       join lude_grade as lg on lg.id=lo.grade_id where lo.created_by='$dealer_id';";

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