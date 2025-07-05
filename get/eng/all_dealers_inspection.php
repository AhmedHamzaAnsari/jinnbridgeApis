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
        it.*,
        dd.*,
        us.name as user_name,
        dd.name as dealer_name,
        '' as zm_name,'' tm_name,us.name as asm_name,us.id as eng_user_id,
        it.created_at as task_create_time,tr.created_at as visit_close_time,
        it.id as task_id,
        CASE
            WHEN it.status = 0 AND DATE(it.time) = CURDATE() THEN 'Pending'
            WHEN it.status = 0 AND DATE(it.time) < CURDATE() THEN 'Overdue'
            WHEN it.status = 0 AND DATE(it.time) > CURDATE() THEN 'Upcoming'
            WHEN it.status = 1 THEN 'Complete'
            WHEN it.status = 2 THEN 'Cancel'
        END AS current_status,
         tr.approved_status,
        tr.approved_at,
        tr.created_at as visit_close_time,
		tr.dealer_sign,
        CASE
        WHEN tr.created_at != '' THEN
            CASE
                WHEN tr.approved_status = 1 THEN
                    CASE
                        WHEN TIMESTAMPDIFF(HOUR, tr.created_at, tr.approved_at) <= 72 THEN 'Approved On Time'
                        ELSE 'Late Approved'
                    END
                ELSE 'Still Not Approved'
            END
        ELSE 'Visit Not Complete'
    END AS approval_status
    FROM eng_inspector_task AS it
    JOIN dealers AS dd ON dd.id = it.dealer_id
    join eng_users_dealers as eu on eu.dealer_id=dd.id
	join users as us on us.id=eu.user_id
	left join inspector_task_response_eng as tr on tr.task_id=it.id where it.time>='$from' and it.time<='$to' group by it.id order by it.id desc";

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