<?php
//fetch.php  
include ("../../config.php");

$access_key = '03201232927';
$pass = $_GET["key"];

if ($pass != '') {
    if ($pass == $access_key) {
        $id = $_GET["id"];
        $pre = $_GET["pre"];
        $month = $_GET["month"];  // Added 'month' as input parameter

        $sql_query1 = '';
        $sql = "SELECT * FROM users WHERE id=$id";

        $result = mysqli_query($db, $sql);
        $row = mysqli_fetch_array($result);
        $rol = $row['privilege'];

        if ($rol != 'ASM Disabled') {
            if ($pre == 'ZM') {
                $sql_query1 = "SELECT GROUP_CONCAT(id ORDER BY id ASC) AS id FROM dealers WHERE zm=$id";
            } elseif ($pre == 'TM') {
                $sql_query1 = "SELECT GROUP_CONCAT(id ORDER BY id ASC) AS id FROM dealers WHERE tm=$id";
            } else {
                $sql_query1 = "SELECT GROUP_CONCAT(id ORDER BY id ASC) AS id FROM dealers WHERE asm=$id";
            }

            $result = mysqli_query($db, $sql_query1);
            $row = mysqli_fetch_array($result);
            $count = mysqli_num_rows($result);

            if ($count > 0) {
                $dealer_id = $row['id'];

                $sql_query2 = "SELECT it.*,us.name as user_name,dl.name as dealer_name,dl.`co-ordinates` as co_ordinates,ir.old_time,ir.new_time,ir.description as reschedule_reason,
                CASE
                                WHEN it.status = 0 THEN 'Pending'
                                WHEN it.status = 1 THEN 'Complete'
                                WHEN it.status = 2 THEN 'Cancel'
                            END AS current_status FROM inspector_task as it 
                join users as us on us.id=it.user_id
                join dealers as dl on dl.id=it.dealer_id
                LEFT JOIN inspector_task_reschedule as ir ON ir.task_id = it.id
                where  dl.id IN ($dealer_id) and it.user_id='$id'  AND MONTH(it.time) = MONTH('" . $month . "-01') AND YEAR(it.time) = YEAR('" . $month . "-01') group by it.id
                ORDER BY it.id DESC";
                
                $result1 = $db->query($sql_query2) or die("Error :" . mysqli_error($db));

                $thread = array();
                while ($user = $result1->fetch_assoc()) {
                    $thread[] = $user;
                }
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
