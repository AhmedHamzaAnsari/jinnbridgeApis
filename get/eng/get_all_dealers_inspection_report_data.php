<?php
//fetch.php  
include("../../config.php");


$access_key = '03201232927';

$pass = $_GET["key"];
$from = $_GET["from"];
$to = $_GET["to"];
$pre = $_GET["pre"];
$id = $_GET["id"];
if ($pass != '') {
    // $id = $_GET["id"];
    if ($pass == $access_key) {


        if ($pre == 'ZM') {

            $sql_query1 = "SELECT it.*,us.name, dd.name as dealer_name, CASE
            WHEN it.status = 0 THEN 'Pending'
            WHEN it.status = 1 THEN 'Complete'
            WHEN it.status = 2 THEN 'Cancel'
            
            END AS current_status,
            tr.created_at as visit_close_time,
            (SELECT id FROM eng_inspector_task where dealer_id=it.dealer_id and id!=it.id and id<it.id and inspection=1 order by id desc limit 1) as last_visit_id,
            tr.dealer_sign
            FROM eng_inspector_task as it 
            join eng_users_dealers as ud on ud.dealer_id=it.dealer_id
            join users us on us.id=ud.user_id  
            left join inspector_task_response_eng as tr on tr.task_id=it.id
            JOIN  dealers AS dd ON dd.id = it.dealer_id where it.time>='$from' and it.time<='$to' and dd.zm='$id' group by it.id
            order by it.id desc;";

        } elseif ($pre == 'TM') {
            $sql_query1 = "SELECT it.*,us.name, dd.name as dealer_name, CASE
            WHEN it.status = 0 THEN 'Pending'
            WHEN it.status = 1 THEN 'Complete'
            WHEN it.status = 2 THEN 'Cancel'
            
            END AS current_status,
            tr.created_at as visit_close_time,
            (SELECT id FROM eng_inspector_task where dealer_id=it.dealer_id and id!=it.id and id<it.id and inspection=1 order by id desc limit 1) as last_visit_id,
            tr.dealer_sign
            FROM eng_inspector_task as it 
            join eng_users_dealers as ud on ud.dealer_id=it.dealer_id
            join users us on us.id=ud.user_id  
            left join inspector_task_response_eng as tr on tr.task_id=it.id
            JOIN  dealers AS dd ON dd.id = it.dealer_id where it.time>='$from' and it.time<='$to' and dd.tm='$id' group by it.id
            order by it.id desc;";

        } elseif ($pre == 'ASM') {
            $sql_query1 = "SELECT it.*,us.name, dd.name as dealer_name, CASE
            WHEN it.status = 0 THEN 'Pending'
            WHEN it.status = 1 THEN 'Complete'
            WHEN it.status = 2 THEN 'Cancel'
            
            END AS current_status,
            tr.created_at as visit_close_time,
            (SELECT id FROM eng_inspector_task where dealer_id=it.dealer_id and id!=it.id and id<it.id and inspection=1 order by id desc limit 1) as last_visit_id,
            tr.dealer_sign
            FROM eng_inspector_task as it 
            join eng_users_dealers as ud on ud.dealer_id=it.dealer_id
            join users us on us.id=ud.user_id  
            left join inspector_task_response_eng as tr on tr.task_id=it.id
            JOIN  dealers AS dd ON dd.id = it.dealer_id where it.time>='$from' and it.time<='$to' and dd.asm='$id' group by it.id
            order by it.id desc;";
        }elseif ($pre == 'Eng') {
            $sql_query1 = "SELECT it.*,us.name, dd.name as dealer_name, CASE
            WHEN it.status = 0 THEN 'Pending'
            WHEN it.status = 1 THEN 'Complete'
            WHEN it.status = 2 THEN 'Cancel'
            
            END AS current_status,
            tr.created_at as visit_close_time,
            (SELECT id FROM eng_inspector_task where dealer_id=it.dealer_id and id!=it.id and id<it.id and inspection=1 order by id desc limit 1) as last_visit_id,
            tr.dealer_sign
            FROM eng_inspector_task as it 
            join eng_users_dealers as ud on ud.dealer_id=it.dealer_id
            join users us on us.id=ud.user_id  
            left join inspector_task_response_eng as tr on tr.task_id=it.id
            JOIN  dealers AS dd ON dd.id = it.dealer_id where it.time>='$from' and it.time<='$to' and it.user_id='$id' group by it.id
            order by it.id desc;";

        } else {



            $sql_query1 = "SELECT it.*,us.name, dd.name as dealer_name, CASE
            WHEN it.status = 0 THEN 'Pending'
            WHEN it.status = 1 THEN 'Complete'
            WHEN it.status = 2 THEN 'Cancel'
            
            END AS current_status,
            tr.created_at as visit_close_time,
            (SELECT id FROM eng_inspector_task where dealer_id=it.dealer_id and id!=it.id and id<it.id and inspection=1 order by id desc limit 1) as last_visit_id,
            tr.dealer_sign
            FROM eng_inspector_task as it 
            join eng_users_dealers as ud on ud.dealer_id=it.dealer_id
            join users us on us.id=ud.user_id  
            left join inspector_task_response_eng as tr on tr.task_id=it.id
            JOIN  dealers AS dd ON dd.id = it.dealer_id where it.time>='$from' and it.time<='$to' group by it.id
            order by it.id desc;";  

            
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