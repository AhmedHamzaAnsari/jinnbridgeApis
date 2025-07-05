<?php
//fetch.php  
include("../../config.php");


$access_key = '03201232927';

$pass = $_GET["key"];
$from = $_GET["from"];
$to = $_GET["to"];
// $id=$_GET["id"];
if ($pass != '') {
    if ($pass == $access_key) {
        // $sql_query1 = "SELECT *,us.name as user_name,dd.name as dealer_name,
        // CASE
        //         WHEN it.status = 0 THEN 'Pending'
        //         WHEN it.status = 1 THEN 'Complete'
        //         WHEN it.status = 2 THEN 'Cancel'
        //         END AS current_status
        //  FROM inspector_task as it
        //         join dealers as dd on dd.id=it.dealer_id
        //         join users as us on us.id=it.user_id;";

        $sql_query1 = "SELECT 
      
        us.name as user_name,us.privilege,
        sum(it.status = 0 AND DATE(it.time) = CURDATE() || it.status = 0 AND DATE(it.time) > CURDATE()) as sum_pending,
        sum(it.status = 0 AND DATE(it.time) < CURDATE()) as sum_Late,
        sum(it.status = 0 AND DATE(it.time) > CURDATE()) as sum_Upcoming,
        sum(it.status = 1) as sum_Complete,
        COUNT(it.id) AS total_visits
    FROM 
        inspector_task AS it
    JOIN 
        dealers AS dd ON dd.id = it.dealer_id
    JOIN 
        users AS us ON us.id = it.user_id
    JOIN 
        users AS usz ON usz.id = dd.zm
    JOIN 
        users AS ust ON ust.id = dd.tm
    JOIN 
        users AS usa ON usa.id = dd.asm where it.time>='$from' and it.time<='$to' group by us.name";

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