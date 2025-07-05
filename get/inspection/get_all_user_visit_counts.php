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
           

                $sql_query2 = "SELECT 
                SUM(CASE WHEN it.status = 0 THEN 1 ELSE 0 END) AS total_pending,
                SUM(CASE WHEN it.status = 1 THEN 1 ELSE 0 END) AS total_complete,
                SUM(CASE WHEN ir.new_time IS NOT NULL THEN 1 ELSE 0 END) AS total_reschedule
            FROM 
            inspector_task as it
            JOIN 
            users as us ON us.id = it.user_id
            JOIN 
            dealers as dl ON dl.id = it.dealer_id
            LEFT JOIN 
            inspector_task_reschedule as ir ON ir.task_id = it.id
            WHERE  
                it.user_id = '$id' 
                AND MONTH(it.time) = MONTH('" . $month . "-01') 
                AND YEAR(it.time) = YEAR('" . $month . "-01')
            GROUP BY 
                it.user_id
            ORDER BY 
                it.id DESC";
                
                $result1 = $db->query($sql_query2) or die("Error :" . mysqli_error($db));

                $thread = array();
                while ($user = $result1->fetch_assoc()) {
                    $thread[] = $user;
                }
                echo json_encode($thread);
            
        }
    } else {
        echo 'Wrong Key...';
    }
} else {
    echo 'Key is Required';
}
?>