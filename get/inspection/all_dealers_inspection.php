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

        $sql_query1 = "SELECT it.id as task_id,
        it.*,
        dd.*,
        us.name as user_name,
        dd.name as dealer_name,
        usz.name as zm_name,
        ust.name as tm_name,
        usa.name as asm_name,
        it.created_at as task_create_time,tr.created_at as visit_close_time,
        CASE
            WHEN it.status = 0 AND DATE(it.time) = CURDATE() THEN 'Pending'
            WHEN it.status = 0 AND DATE(it.time) < CURDATE() THEN 'Late'
            WHEN it.status = 0 AND DATE(it.time) > CURDATE() THEN 'Pending'
            WHEN it.status = 1 THEN 'Completed'
            WHEN it.status = 2 THEN 'Cancel'
        END AS current_status,
        tr.dealer_sign,
        CASE
        WHEN tr.created_at != '' THEN
    CASE
        -- If recon_approval = 0 and recon_approved_status = 1, show 'Hold'
        WHEN tr.recon_approval = 0 AND tr.approved_status = 1 THEN
            'Hold'

        -- If recon_approval = 1, check if the approval time is within 72 hours
        WHEN tr.recon_approval = 1 THEN
            CASE
                WHEN TIMESTAMPDIFF(HOUR, tr.created_at, tr.approved_at) <= 72 THEN 
                    'Approved On Time' 
                ELSE 
                    'Late Approved'
            END

        -- If recon_approval is neither 0 nor 1 (unexpected case)
        ELSE 'Still Not Approved' 
    END
ELSE 'Visit Not Complete'

    END AS approval_status,
        tr.approved_status,
        tr.approved_at,
        tr.created_at as visit_close_time,
        CASE 
        WHEN (SELECT COUNT(*) FROM inspector_task_reschedule WHERE task_id = it.id) > 0 
        THEN 'Rescheduled' 
        ELSE 'Not Scheduled' 
    END AS schedule_status
    FROM inspector_task AS it
    JOIN dealers AS dd ON dd.id = it.dealer_id
    JOIN users AS us ON us.id = it.user_id
    JOIN users AS usz ON usz.id = dd.zm
    JOIN users AS ust ON ust.id = dd.tm
    JOIN users AS usa ON usa.id = dd.asm 
	left join inspector_task_response as tr on tr.task_id=it.id where it.time>='$from' and it.time<='$to' group by it.id
            order by it.id desc";

        $result1 = $db->query($sql_query1) or die("Error :" . mysqli_error());

        $thread = array();
        while ($user = $result1->fetch_assoc()) {
            $thread[] = $user;
        }
        $thread = utf8ize($thread);
$json = json_encode($thread, JSON_PRETTY_PRINT);

if ($json === false) {
    echo json_encode(["error" => "JSON encoding failed", "details" => json_last_error_msg()]);
} else {
    echo $json;
}

    } else {
        echo 'Wrong Key...';
    }

} else {
    echo 'Key is Required';
}

function utf8ize($data) {
    if (is_array($data)) {
        return array_map('utf8ize', $data);
    } elseif (is_string($data)) {
        return mb_convert_encoding($data, 'UTF-8', 'UTF-8');
    }
    return $data;
}
?>