<?php
// fetch.php  
include("../../config.php");

$access_key = '03201232927';
$pass = $_GET["key"] ?? '';
$from = $_GET["from"] ?? '';
$to = $_GET["to"] ?? '';
$pre = $_GET["pre"] ?? '';
$id = $_GET["user_id"] ?? '';

if ($pass !== '') {
    if ($pass === $access_key) {
        $sql_query1 = "";

        if ($pre === 'ZM') {
            $sql_query1 = "SELECT 
                us.name AS user_name,
                us.privilege,
                CASE 
                    WHEN us.privilege = 'TM' THEN (SELECT COUNT(id) FROM dealers WHERE tm = us.id)
                    WHEN us.privilege = 'ASM' THEN (SELECT COUNT(id) FROM dealers WHERE asm = us.id)
                    ELSE 0
                END AS total_dealers,
                SUM(it.status = 0 AND DATE(it.time) = CURDATE()) AS sum_pending,
                SUM(it.status = 0 
                    AND it.sales_status = 0 
                    AND it.measurement_status = 0 
                    AND it.wet_stock_status = 0
                    AND it.dispensing_status = 0 
                    AND it.stock_variations_status = 0 
                    AND it.inspection = 0 
                    AND DATE(it.time) < CURDATE()) AS sum_Late,
                SUM(it.status = 0 AND DATE(it.time) > CURDATE()) AS sum_Upcoming,
                SUM(it.status = 1) AS sum_Complete,
                SUM(it.status = 0 
                    AND (it.sales_status = 1 
                         OR it.measurement_status = 1 
                         OR it.wet_stock_status = 1 
                         OR it.dispensing_status = 1 
                         OR it.stock_variations_status = 1 
                         OR it.inspection = 1)) AS only_visited,
                COUNT(*) AS total_visits
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
                users AS usa ON usa.id = dd.asm
            WHERE it.time >= '$from' AND it.time <= '$to' AND dd.zm = $id 
            GROUP BY us.name";

        } elseif ($pre === 'TM') {
            $sql_query1 = "SELECT 
                us.name AS user_name,
                us.privilege,
                CASE 
                    WHEN us.privilege = 'TM' THEN (SELECT COUNT(id) FROM dealers WHERE tm = us.id)
                    WHEN us.privilege = 'ASM' THEN (SELECT COUNT(id) FROM dealers WHERE asm = us.id)
                    ELSE 0
                END AS total_dealers,
                SUM(it.status = 0 AND DATE(it.time) = CURDATE()) AS sum_pending,
                SUM(it.status = 0 
                    AND it.sales_status = 0 
                    AND it.measurement_status = 0 
                    AND it.wet_stock_status = 0
                    AND it.dispensing_status = 0 
                    AND it.stock_variations_status = 0 
                    AND it.inspection = 0 
                    AND DATE(it.time) < CURDATE()) AS sum_Late,
                SUM(it.status = 0 AND DATE(it.time) > CURDATE()) AS sum_Upcoming,
                SUM(it.status = 1) AS sum_Complete,
                SUM(it.status = 0 
                    AND (it.sales_status = 1 
                         OR it.measurement_status = 1 
                         OR it.wet_stock_status = 1 
                         OR it.dispensing_status = 1 
                         OR it.stock_variations_status = 1 
                         OR it.inspection = 1)) AS only_visited,
                COUNT(*) AS total_visits
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
                users AS usa ON usa.id = dd.asm
            WHERE it.time >= '$from' AND it.time <= '$to' AND dd.tm = $id 
            GROUP BY us.name";

        } elseif ($pre === 'ASM') {
            $sql_query1 = "SELECT 
                us.name AS user_name,
                us.privilege,
                CASE 
                    WHEN us.privilege = 'TM' THEN (SELECT COUNT(id) FROM dealers WHERE tm = us.id)
                    WHEN us.privilege = 'ASM' THEN (SELECT COUNT(id) FROM dealers WHERE asm = us.id)
                    ELSE 0
                END AS total_dealers,
                SUM(it.status = 0 AND DATE(it.time) = CURDATE()) AS sum_pending,
                SUM(it.status = 0 
                    AND it.sales_status = 0 
                    AND it.measurement_status = 0 
                    AND it.wet_stock_status = 0
                    AND it.dispensing_status = 0 
                    AND it.stock_variations_status = 0 
                    AND it.inspection = 0 
                    AND DATE(it.time) < CURDATE()) AS sum_Late,
                SUM(it.status = 0 AND DATE(it.time) > CURDATE()) AS sum_Upcoming,
                SUM(it.status = 1) AS sum_Complete,
                SUM(it.status = 0 
                    AND (it.sales_status = 1 
                         OR it.measurement_status = 1 
                         OR it.wet_stock_status = 1 
                         OR it.dispensing_status = 1 
                         OR it.stock_variations_status = 1 
                         OR it.inspection = 1)) AS only_visited,
                COUNT(*) AS total_visits
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
                users AS usa ON usa.id = dd.asm
            WHERE it.time >= '$from' AND it.time <= '$to' AND dd.asm = $id 
            GROUP BY us.name";
        } else {
            $sql_query1 = "SELECT 
            us.name AS user_name,
            us.privilege,
           (SELECT count(*) FROM jinnbridge.eng_users_dealers where user_id=it.user_id) as total_dealers,
            SUM(it.status = 0 AND DATE(it.time) = CURDATE()) AS sum_pending,
            SUM(it.status = 0 
                AND it.inspection = 0 
                AND DATE(it.time) < CURDATE()) AS sum_Late,
            SUM(it.status = 0 AND DATE(it.time) > CURDATE()) AS sum_Upcoming,
            SUM(it.status = 1) AS sum_Complete,
            SUM(it.status = 0 
                AND (it.inspection = 1)) AS only_visited,
            COUNT(*) AS total_visits
            FROM eng_inspector_task AS it
            JOIN dealers AS dd ON dd.id = it.dealer_id
            -- join eng_users_dealers as eu on eu.dealer_id=dd.id
            join users as us on us.id=it.user_id
            WHERE it.time >= '$from' AND it.time <= '$to' 
            GROUP BY us.id, us.name, us.privilege";
        }

        $result1 = $db->query($sql_query1);

        if ($result1) {
            $thread = array();
            while ($user = $result1->fetch_assoc()) {
                $thread[] = $user;
            }
            echo json_encode($thread);
        } else {
            echo "Error: " . $db->error;
        }
    } else {
        echo 'Wrong Key...';
    }
} else {
    echo 'Key is Required';
}

?>