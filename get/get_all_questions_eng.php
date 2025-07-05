<?php
include("../config.php");
session_start();
header("Content-Type: application/json");

$sql = "SELECT id, question FROM survey_category_questions_eng WHERE status = '1' ORDER BY question ASC";
$result = $db->query($sql);

$questions = [];
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $questions[] = [
            'id' => $row['id'],
            'question' => $row['question']
        ];
    }
}

echo json_encode($questions);
?>