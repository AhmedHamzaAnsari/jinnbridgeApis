<?php
// File: get_questions.php
include("../config.php");
session_start();
header("Content-Type: application/json");

$sql = "
    SELECT q.question, c.name AS category_name, q.status
    FROM survey_category_questions q
    JOIN survey_category c ON q.category_id = c.id
    WHERE q.status = '1'
    ORDER BY q.id asc
";

$res = $db->query($sql);
$data = [];
$sr = 1;
while ($row = $res->fetch_assoc()) {
    $row['sr_no'] = $sr++;
    $row['status'] = 'Approved';
    $data[] = $row;
}
echo json_encode($data);
