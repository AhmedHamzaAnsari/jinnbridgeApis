<?php
include("../config.php");
session_start();
header("Content-Type: application/json");

// Fetch filters
$from_date = $_GET['from_date'] ?? '';
$to_date = $_GET['to_date'] ?? '';
$dealers = $_GET['dealers'] ?? [];
$response = $_GET['response'] ?? '';
$categories = $_GET['categories'] ?? [];
$questions = $_GET['questions'] ?? [];

$where = "WHERE 1=1";

// Date filter
if ($from_date && $to_date) {
    $where .= " AND DATE(sr.created_at) BETWEEN '$from_date' AND '$to_date'";
} elseif ($from_date) {
    $where .= " AND DATE(sr.created_at) >= '$from_date'";
} elseif ($to_date) {
    $where .= " AND DATE(sr.created_at) <= '$to_date'";
}

// Dealer filter
if (!empty($dealers) && is_array($dealers)) {
    $dealerList = implode(",", array_map('intval', $dealers));
    $where .= " AND sr.dealer_id IN ($dealerList)";
}

// Response filter
if ($response) {
    $responseValue = ucfirst(strtolower($response));
    $where .= " AND sr.response = '$responseValue'";
}

// Category filter
if (!empty($categories) && is_array($categories)) {
    $categoryList = implode(",", array_map('intval', $categories));
    $where .= " AND sr.category_id IN ($categoryList)";
}

// Question filter
if (!empty($questions) && is_array($questions)) {
    $questionList = implode(",", array_map('intval', $questions));
    $where .= " AND sr.question_id IN ($questionList)";
}

// =================== Category Breakdown ===================
$categorySQL = "
    SELECT 
        sc.name as category_name,
        sr.response,
        COUNT(*) as count
    FROM survey_response_eng sr
    LEFT JOIN survey_category_eng sc ON sc.id = sr.category_id
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

// =================== Timeline ===================
$timelineSQL = "
    SELECT 
        DATE(sr.created_at) as date,
        sc.name as category,
        COUNT(*) as total
    FROM survey_response_eng sr
    LEFT JOIN survey_category_eng sc ON sc.id = sr.category_id
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

// =================== Detailed Breakdown ===================
$detailSQL = "
    SELECT 
        DATE(sr.created_at) as date,
        d.name as dealer_name,
        sc.name as category_name,
        scq.question as question_text,
        sr.response,
        COALESCE(NULLIF(sr.comment, ''), 'N/A') as description
    FROM survey_response_eng sr
    LEFT JOIN dealers d ON d.id = sr.dealer_id
    LEFT JOIN survey_category_eng sc ON sc.id = sr.category_id
    LEFT JOIN survey_category_questions_eng scq ON scq.id = sr.question_id
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

// =================== Attachments Section ===================
$whereFiles = "WHERE 1=1";

if ($from_date && $to_date) {
    $whereFiles .= " AND DATE(f.created_at) BETWEEN '$from_date' AND '$to_date'";
} elseif ($from_date) {
    $whereFiles .= " AND DATE(f.created_at) >= '$from_date'";
} elseif ($to_date) {
    $whereFiles .= " AND DATE(f.created_at) <= '$to_date'";
}

if (!empty($dealers) && is_array($dealers)) {
    $dealerListFiles = implode(",", array_map('intval', $dealers));
    $whereFiles .= " AND f.dealer_id IN ($dealerListFiles)";
}
if (!empty($categories) && is_array($categories)) {
    $categoryListFiles = implode(",", array_map('intval', $categories));
    $whereFiles .= " AND f.category_id IN ($categoryListFiles)";
}
if (!empty($questions) && is_array($questions)) {
    $questionListFiles = implode(",", array_map('intval', $questions));
    $whereFiles .= " AND f.question_id IN ($questionListFiles)";
}

$attachmentSQL = "
    SELECT 
        f.id,
        f.dealer_id,
        d.name AS dealer_name,
        f.category_id AS category,
        f.question_id AS question,
        f.file,
        DATE(f.created_at) AS upload_date
    FROM survey_response_files_eng f
    LEFT JOIN dealers d ON d.id = f.dealer_id
    $whereFiles
    ORDER BY f.created_at DESC
";

$res = $db->query($attachmentSQL);
$attachmentList = [];
$baseUrl = "http://192.168.3.5:5003/jinnBridge_files/uploads";
$sr = 1;

if ($res && $res->num_rows > 0) {
    while ($row = $res->fetch_assoc()) {
        $attachmentList[] = [
            'sr_no'        => $sr++,
            'dealer_name'  => $row['dealer_name'] ?: "Dealer #{$row['dealer_id']}",
            'category'     => $row['category'] ?? '',
            'question'     => $row['question'] ?? '',
            'file_url'     => "$baseUrl/{$row['file']}",
            'date'         => $row['upload_date'] ?? ''
        ];
    }
}

// =================== Optional: Dispenser Setup Section ===================
// If you're rendering dispenser data on this dashboard, you can also filter using $dealerList
// Use similar logic like:
// if (!empty($dealers)) $whereDispenser .= " AND d.dealer_id IN ($dealerList)";


// =================== Final Output ===================
echo json_encode([
    'categories' => [
        'labels' => $categoryLabels,
        'data' => $categoryData
    ],
    'timeline' => $timelineData,
    'details' => $details,
    'attachments' => $attachmentList
]);
