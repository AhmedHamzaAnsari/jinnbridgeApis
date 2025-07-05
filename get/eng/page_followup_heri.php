<?php
//fetch.php  
include("../../config.php");
error_reporting(0);
$access_key = '03201232927';

$pass = $_GET["key"];
if ($pass != '') {
    $from = $_GET["from"];
    $to = $_GET["to"];
    $pre = $_GET["pre"];
    if ($pass == $access_key) {
        $datetimes = date('Y-m-d H:i:s');
        $thread = array();
        $survey_form_sql = "SELECT * FROM follow_ups_eng
                 WHERE status='0' 
                 AND created_at >= '$from' 
                 AND created_at <= '$to'";

        $survey_form_result = $db->query($survey_form_sql) or die("Error: " . mysqli_error($db));

        while ($user_f = $survey_form_result->fetch_assoc()) {
            $idds = $user_f['id'];
            $cat_table = $user_f['cat_table'];
            $ques_table = $user_f['ques_table'];

            $survey_sql = "SELECT dl.id as dealer_id,
                 fu.*, 
                  it.type AS task_type, 
                 it.time AS tas_time, it.description AS task_des, us.name AS inspector_name, 
                 dl.name AS dealers_name, sc.name AS cat_name, 
                 sq.question AS ques_name, sq.duration AS hours_duration, 
                 dl.region, dl.province, dl.city,
                 CASE 
                     WHEN fu.status = 0 THEN 'Pending'
                     ELSE 'Complete'
                 END AS status_val,
                 (SELECT count(*) as chat_counts FROM followup_notification_eng where followup_id=$idds)as chat_counts
                 FROM follow_ups_eng AS fu
                 JOIN eng_inspector_task AS it ON it.id = fu.task_id
                 JOIN users AS us ON us.id = it.user_id
                 JOIN dealers AS dl ON dl.id = it.dealer_id
                 JOIN $cat_table AS sc ON sc.id = fu.category_id
                 JOIN $ques_table AS sq ON sq.id = fu.question_id
                 WHERE fu.id = $idds 
                 AND fu.created_at >= '$from' 
                 AND fu.created_at <= '$to'";

            $survey_result = $db->query($survey_sql) or die("Error: " . mysqli_error($db));

            while ($user = $survey_result->fetch_assoc()) {
                $hours_duration = $user['hours_duration'];
                $created_at = $user['created_at'];
                $dealer_id = $user['dealer_id'];

                $diff = diferr($created_at, $datetimes);

                $sql_dpt_heri = "SELECT 
                dl.name,
                zm.name AS zm_name, 
                CASE 
                WHEN zm.privilege = 'ZM' THEN 'GM' 
                ELSE zm.privilege 
                END AS zm_pre,
                rm.name AS rm_name, 
                CASE 
                WHEN rm.privilege = 'TM' THEN 'RM' 
                ELSE rm.privilege 
                END AS rm_pre,
                tm.name AS tm_name, 
                CASE 
                WHEN tm.privilege = 'ASM' THEN 'TM' 
                ELSE tm.privilege 
                END AS tm_pre
                FROM dealers AS dl 
                JOIN users AS zm ON zm.id = dl.zm 
                JOIN users AS rm ON rm.id = dl.tm 
                JOIN users AS tm ON tm.id = dl.asm 
                WHERE dl.id = '$dealer_id';";

                $sql_dpt_heri_result = $db->query($sql_dpt_heri) or die("Error: " . mysqli_error($db));

                $resultLength = mysqli_num_rows($sql_dpt_heri_result);
                $dynamic_val_end = $hours_duration * 3;
                $dynamic_val = $hours_duration;
                $resultArray = array();

                while ($user_sql_dpt = $sql_dpt_heri_result->fetch_assoc()) {
                    $resultArray[] = $user_sql_dpt;

                    $zm_name = $user_sql_dpt['zm_name'];
                    $zm_pre = $user_sql_dpt['zm_pre'];

                    $rm_name = $user_sql_dpt['rm_name'];
                    $rm_pre = $user_sql_dpt['rm_pre'];

                    $tm_name = $user_sql_dpt['tm_name'];
                    $tm_pre = $user_sql_dpt['tm_pre'];
                    if ($diff <= $dynamic_val) {
                        $user['waiting'] = 'Waiting For ' . $tm_pre . ' ' . $tm_name;
                    } elseif ($diff > $dynamic_val && $diff <= $dynamic_val * 2) {
                        $user['waiting'] = 'Waiting For ' . $rm_pre . ' ' . $rm_name;
                    } elseif ($diff > $dynamic_val * 2) {
                        $user['waiting'] = 'Waiting For ' . $zm_pre . ' ' . $zm_name;
                    }
                    


                }
                $thread[] = $user;
            }
        }

        echo json_encode($thread);

    } else {
        echo 'Wrong Key...';
    }

} else {
    echo 'Key is Required';
}

function diferr($d1, $d2)
{
    $datetime1 = new DateTime($d1);
    $datetime2 = new DateTime($d2);

    $interval = $datetime1->diff($datetime2);
    $hours = $interval->h + ($interval->days * 24);

    return $hours;
}
?>