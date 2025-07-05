<?php
//fetch.php  
include ("../../config.php");


$access_key = '03201232927';

$pass = $_GET["key"];
if ($pass != '') {
    if ($pass == $access_key) {
        $pre = $_GET["pre"];
        $id = $_GET["user_id"];
        $from = $_GET["from"];
        $to = $_GET["to"];
        if ($pre != 'Admin') {

            $sql_query1 = "SELECT at.*,us.name as user_name,dl.name as dealer_name,CASE
            WHEN at.status = 0 THEN 'Pending'
            WHEN at.status = 1 THEN 'Complete'
            WHEN at.status = 2 THEN 'Cancel'
            END AS current_status,dl.`co-ordinates` as co_ordinates FROM eng_inspector_task as at 
            join dealers as dl on dl.id=at.dealer_id 
            join eng_users_dealers as ud on ud.dealer_id=dl.id
            join users as us on us.id=ud.user_id
            where at.time>='$from' and at.time<='$to' and us.id=$id group by at.id
            order by at.id desc";
        }else {

          

            $sql_query1 = "SELECT at.*,us.name as user_name,dl.name as dealer_name,CASE
            WHEN at.status = 0 THEN 'Pending'
            WHEN at.status = 1 THEN 'Complete'
            WHEN at.status = 2 THEN 'Cancel'
            END AS current_status,dl.`co-ordinates` as co_ordinates FROM eng_inspector_task as at 
            join dealers as dl on dl.id=at.dealer_id 
            join eng_users_dealers as ud on ud.dealer_id=dl.id
            join users as us on us.id=ud.user_id
            where at.time>='$from' and at.time<='$to' group by at.id
            order by at.id desc;";
        }

        $result1 = $db->query($sql_query1) or die("Error :" . mysqli_error($db));

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