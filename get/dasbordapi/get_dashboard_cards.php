<?php
include("../config.php");
session_start();
header("Content-Type: application/json");

$from_date = $_GET['from_date'] ?? '';
$to_date = $_GET['to_date'] ?? '';
$dealers = $_GET['dealers'] ?? [];
$response = $_GET['response'] ?? '';
$categories = $_GET['categories'] ?? [];
$questions = $_GET['questions'] ?? [];

$whereSurvey = "WHERE 1=1";

// Apply date filter
if ($from_date && $to_date) {
    $whereSurvey .= " AND DATE(sr.created_at) BETWEEN '$from_date' AND '$to_date'";
} elseif ($from_date) {
    $whereSurvey .= " AND DATE(sr.created_at) >= '$from_date'";
} elseif ($to_date) {
    $whereSurvey .= " AND DATE(sr.created_at) <= '$to_date'";
}

// Dealer filter
if (!empty($dealers) && is_array($dealers)) {
    $dealerList = implode(",", array_map('intval', $dealers));
    $whereSurvey .= " AND sr.dealer_id IN ($dealerList)";
}

// Response filter
if ($response) {
    $responseValue = ucfirst(strtolower($response));
    $whereSurvey .= " AND sr.response = '$responseValue'";
}

// Category filter
if (!empty($categories) && is_array($categories)) {
    $categoryList = implode(",", array_map('intval', $categories));
    $whereSurvey .= " AND sr.category_id IN ($categoryList)";
}

// Question filter
if (!empty($questions) && is_array($questions)) {
    $questionList = implode(",", array_map('intval', $questions));
    $whereSurvey .= " AND sr.question_id IN ($questionList)";
}

// Total Inspections (distinct main_id count)
$sql = "SELECT COUNT(DISTINCT sr.main_id) as inspections FROM survey_response sr $whereSurvey";
$res = $db->query($sql);
$inspections = $res ? $res->fetch_assoc()['inspections'] : 0;

// YES Responses
$sql = "SELECT COUNT(*) as yesCount FROM survey_response sr $whereSurvey AND sr.response = 'Yes'";
$res = $db->query($sql);
$yesCount = $res ? $res->fetch_assoc()['yesCount'] : 0;

// NO Responses
$sql = "SELECT COUNT(*) as noCount FROM survey_response sr $whereSurvey AND sr.response = 'No'";
$res = $db->query($sql);
$noCount = $res ? $res->fetch_assoc()['noCount'] : 0;

echo json_encode([
    'inspections' => (int)$inspections,
    'yes'         => (int)$yesCount,
    'no'          => (int)$noCount
]);
?>