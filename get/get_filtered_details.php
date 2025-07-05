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

$where = "WHERE 1=1";

// Apply filters
if ($from_date && $to_date) {
    $where .= " AND DATE(sr.created_at) BETWEEN '$from_date' AND '$to_date'";
} elseif ($from_date) {
    $where .= " AND DATE(sr.created_at) >= '$from_date'";
} elseif ($to_date) {
    $where .= " AND DATE(sr.created_at) <= '$to_date'";
}

if (!empty($dealers) && is_array($dealers)) {
    $dealerList = implode(",", array_map('intval', $dealers));
    $where .= " AND sr.dealer_id IN ($dealerList)";
}

if ($response) {
    $responseValue = ucfirst(strtolower($response));
    $where .= " AND sr.response = '$responseValue'";
}

if (!empty($categories) && is_array($categories)) {
    $categoryList = implode(",", array_map('intval', $categories));
    $where .= " AND sr.category_id IN ($categoryList)";
}

if (!empty($questions) && is_array($questions)) {
    $questionList = implode(",", array_map('intval', $questions));
    $where .= " AND sr.question_id IN ($questionList)";
}

// Category breakdown with Yes/No responses - FIXED
$categorySQL = "
    SELECT 
        sc.name as category_name,
        sr.response,
        COUNT(*) as count
    FROM survey_response sr
    LEFT JOIN survey_category sc ON sc.id = sr.category_id
    $where
    GROUP BY sr.category_id, sc.name, sr.response
    ORDER BY sc.name, sr.response
";

$res = $db->query($categorySQL);
$categoryLabels = [];
$categoryData = [];

if ($res) {
    while ($row = $res->fetch_assoc()) {
        $label = ($row['category_name'] ?? 'Unknown') . ' - ' . $row['response'];
        $categoryLabels[] = $label;
        $categoryData[] = (int)$row['count'];
    }
}

// Timeline data per category - FIXED
$timelineSQL = "
    SELECT 
        DATE(sr.created_at) as date,
        sc.name as category,
        COUNT(*) as total
    FROM survey_response sr
    LEFT JOIN survey_category sc ON sc.id = sr.category_id
    $where
    GROUP BY DATE(sr.created_at), sc.name
    ORDER BY DATE(sr.created_at)
";

$timelineRes = $db->query($timelineSQL);
$timeline = [];

if ($timelineRes) {
    while ($row = $timelineRes->fetch_assoc()) {
        $date = $row['date'];
        $category = $row['category'] ?? 'Uncategorized';
        $count = (int)$row['total'];

        if (!isset($timeline[$category])) {
            $timeline[$category] = [];
        }
        $timeline[$category][$date] = $count;
    }
}

// Build timeline data for Chart.js
$allDates = [];
foreach ($timeline as $dates) {
    $allDates = array_merge($allDates, array_keys($dates));
}
$allDates = array_unique($allDates);
sort($allDates);

$timelineData = [
    'labels' => $allDates,
    'datasets' => []
];

$colors = ['#007bff', '#28a745', '#ffc107', '#dc3545', '#17a2b8', '#6f42c1', '#fd7e14', '#20c997'];
$colorIndex = 0;

foreach ($timeline as $category => $dateCounts) {
    $dataPoints = [];
    foreach ($allDates as $date) {
        $dataPoints[] = $dateCounts[$date] ?? 0;
    }

    $timelineData['datasets'][] = [
        'label' => $category,
        'data' => $dataPoints,
        'borderColor' => $colors[$colorIndex % count($colors)],
        'backgroundColor' => 'transparent',
        'tension' => 0.4,
        'fill' => false
    ];

    $colorIndex++;
}

// Detailed breakdown with question text
$detailSQL = "
    SELECT 
        DATE(sr.created_at) as date,
        d.name as dealer_name,
        sc.name as category_name,
        scq.question as question_text,
        sr.response,
        COALESCE(NULLIF(sr.description, ''), 'N/A') as description
    FROM survey_response sr
    LEFT JOIN dealers d ON d.id = sr.dealer_id
    LEFT JOIN survey_category sc ON sc.id = sr.category_id
    LEFT JOIN survey_category_questions scq ON scq.id = sr.question_id
    $where
    ORDER BY sr.created_at DESC
    LIMIT 200
";

$res = $db->query($detailSQL);
$details = [];

if ($res) {
    while ($row = $res->fetch_assoc()) {
        $details[] = [
            'date' => $row['date'],
            'dealer_name' => $row['dealer_name'] ?? 'Unknown',
            'category_name' => $row['category_name'] ?? 'Unknown',
            'question_text' => $row['question_text'] ?? 'N/A',
            'response' => $row['response'],
            'description' => $row['description']
        ];
    }
}

echo json_encode([
    'categories' => [
        'labels' => $categoryLabels,
        'data' => $categoryData
    ],
    'timeline' => $timelineData,
    'details' => $details
]);
?>