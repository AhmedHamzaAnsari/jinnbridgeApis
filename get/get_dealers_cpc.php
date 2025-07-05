<?php
// fetch.php  
include("../config.php");

$access_key = '03201232927';
$pass = $_GET["key"] ?? '';
$emp_id = $_GET["emp_id"] ?? 'All';
$month = $_GET["month"] ?? ''; // Format: YYYY-MM

if ($pass === '') {
    echo 'Key is Required';
    exit;
}

if ($pass !== $access_key) {
    echo 'Wrong Key...';
    exit;
}

$conditions = [];
if ($emp_id !== 'All') {
    $conditions[] = "us.id = '" . mysqli_real_escape_string($db, $emp_id) . "'";
}

if ($month !== '') {
    // Extract start and end date from the given month
    $start_date = $month . '-01';
    $end_date = date('Y-m-t', strtotime($start_date)); // gets last date of the month
    $conditions[] = "DATE(at.time) BETWEEN '$start_date' AND '$end_date'";
}

$where_clause = count($conditions) > 0 ? 'WHERE ' . implode(' AND ', $conditions) : '';

$sql_query1 = "SELECT at.time as visit_date,  
                      dl.name as site_name,
                      dl.sap_no as site_code,
                      us.id as employee_id,
                      CASE
                          WHEN at.status = 0 THEN 'Pending'
                          WHEN at.status = 1 THEN 'Complete'
                          WHEN at.status = 2 THEN 'Cancel'
                      END AS visit_status
               FROM inspector_task as at
               JOIN users as us ON us.id = at.user_id
               JOIN dealers as dl ON dl.id = at.dealer_id
               $where_clause";

$result1 = $db->query($sql_query1) or die("Error: " . mysqli_error($db));

$thread = [];
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
