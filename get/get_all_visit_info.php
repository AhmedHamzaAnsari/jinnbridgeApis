<?php
//fetch.php  
include("../config.php");


$access_key = '03201232927';

$pass = $_GET["key"];
if ($pass != '') {
    $id = $_GET["id"];
    $from = $_GET["from"];
    $to = $_GET["to"];
    $pre = $_GET["pre"];

    if ($pass == $access_key) {

        if ($pre == 'ZM') {
            $sql_query1 = "SELECT 
            dd.id,
            dd.sap_no,
            dd.name as dealer_name,
            GROUP_CONCAT(DISTINCT DATE(it.time) ORDER BY it.time SEPARATOR ', ') AS dates,  -- Collect unique dates as comma-separated values
            COUNT(DISTINCT it.id) AS visit_count,  -- Count unique tasks
            us.name, 
            us.privilege 
                FROM inspector_task AS it
                JOIN users AS us ON us.id = it.user_id  
                JOIN dealers AS dd ON dd.id = it.dealer_id 
                WHERE dd.zm='$id' and DATE(it.time) >= '$from' 
                AND DATE(it.time) <= '$to' 
                AND (it.stock_recon = 1 
                    OR it.inspection = 1 
                    )
                    AND it.type='Inpection'
            GROUP BY dd.id;";

        } elseif ($pre == 'TM') {
            $sql_query1 = "SELECT 
            dd.id,
            dd.sap_no,
            dd.name as dealer_name,
            GROUP_CONCAT(DISTINCT DATE(it.time) ORDER BY it.time SEPARATOR ', ') AS dates,  -- Collect unique dates as comma-separated values
            COUNT(DISTINCT it.id) AS visit_count,  -- Count unique tasks
            us.name, 
            us.privilege 
                FROM inspector_task AS it
                JOIN users AS us ON us.id = it.user_id  
                JOIN dealers AS dd ON dd.id = it.dealer_id 
                WHERE dd.tm='$id' and DATE(it.time) >= '$from' 
                AND DATE(it.time) <= '$to' 
                AND (it.stock_recon = 1 
                    OR it.inspection = 1 
                    )
                    AND it.type='Inpection'
            GROUP BY dd.id;";

        } elseif ($pre == 'ASM') {
            $sql_query1 = "SELECT 
            dd.id,
            dd.sap_no,
            dd.name as dealer_name,
            GROUP_CONCAT(DISTINCT DATE(it.time) ORDER BY it.time SEPARATOR ', ') AS dates,  -- Collect unique dates as comma-separated values
            COUNT(DISTINCT it.id) AS visit_count,  -- Count unique tasks
            us.name, 
            us.privilege 
                FROM inspector_task AS it
                JOIN users AS us ON us.id = it.user_id  
                JOIN dealers AS dd ON dd.id = it.dealer_id 
                WHERE dd.asm='$id' and DATE(it.time) >= '$from' 
                AND DATE(it.time) <= '$to' 
                AND (it.stock_recon = 1 
                    OR it.inspection = 1 
                    )
                    AND it.type='Inpection'
            GROUP BY dd.id;";
        } else {
            $sql_query1 = "SELECT 
        dd.id,
        dd.sap_no,
        dd.name as dealer_name,
        GROUP_CONCAT(DISTINCT DATE(it.time) ORDER BY it.time SEPARATOR ', ') AS dates,  -- Collect unique dates as comma-separated values
        COUNT(DISTINCT it.id) AS visit_count,  -- Count unique tasks
        us.name, 
        us.privilege 
            FROM inspector_task AS it
            JOIN users AS us ON us.id = it.user_id  
            JOIN dealers AS dd ON dd.id = it.dealer_id 
            WHERE DATE(it.time) >= '$from' 
            AND DATE(it.time) <= '$to' 
            AND (it.stock_recon = 1 
                OR it.inspection = 1 
                )
                AND it.type='Inpection'
        GROUP BY dd.id;";
        }

        $result1 = $db->query($sql_query1) or die("Error :" . mysqli_error($db));

        $thread = array();
        while ($user = $result1->fetch_assoc()) {
            $thread[] = $user;
        }
        // echo json_encode($thread);

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

function utf8ize($data)
{
    if (is_array($data)) {
        return array_map('utf8ize', $data);
    } elseif (is_string($data)) {
        return mb_convert_encoding($data, 'UTF-8', 'UTF-8');
    }
    return $data;
}
?>