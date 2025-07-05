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

// Apply date filter
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

// Count Yes
$yesSQL = "SELECT COUNT(*) as count FROM survey_response sr $where AND sr.response = 'Yes'";
$res = $db->query($yesSQL);
$yes = $res ? $res->fetch_assoc()['count'] : 0;

// Count No
$noSQL = "SELECT COUNT(*) as count FROM survey_response sr $where AND sr.response = 'No'";
$res = $db->query($noSQL);
$no = $res ? $res->fetch_assoc()['count'] : 0;

// Issue Chart - Group by description for 'No' responses
$issueSQL = "
    SELECT 
        COALESCE(NULLIF(sr.description, ''), 'No Description') as description, 
        COUNT(*) as count
    FROM survey_response sr
    $where AND sr.response = 'No'
    GROUP BY COALESCE(NULLIF(sr.description, ''), 'No Description')
    ORDER BY count DESC
    LIMIT 10
";

$res = $db->query($issueSQL);
$labels = [];
$data = [];
$details = [];

if ($res && $res->num_rows > 0) {
    while ($row = $res->fetch_assoc()) {
        $desc = $row['description'];
        $labels[] = $desc;
        $data[] = (int)$row['count'];

        // Get details for modal
        $detailSQL = "
            SELECT 
                DATE(sr.created_at) as date,
                COALESCE(NULLIF(sr.description, ''), 'No Description') as description,
                sr.response, 
                d.name as site_name
            FROM survey_response sr
            LEFT JOIN dealers d ON d.id = sr.dealer_id
            $where AND sr.response = 'No' 
            AND COALESCE(NULLIF(sr.description, ''), 'No Description') = '" . $db->real_escape_string($desc) . "'
            ORDER BY sr.created_at DESC
            LIMIT 50
        ";
        
        $subres = $db->query($detailSQL);
        $details[$desc] = [];
        
        if ($subres) {
            while ($subrow = $subres->fetch_assoc()) {
                $details[$desc][] = [
                    'date' => $subrow['date'],
                    'site_name' => $subrow['site_name'] ?? 'Unknown',
                    'response' => $subrow['response'],
                    'description' => $subrow['description']
                ];
            }
        }
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

// Build data for JS chart
$allDates = [];
foreach ($timeline as $dates) {
    $allDates = array_merge($allDates, array_keys($dates));
}
$allDates = array_unique($allDates);
sort($allDates); // Ensure chronological order

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

echo json_encode([
    'yes' => (int)$yes,
    'no' => (int)$no,
    'issues' => [
        'labels' => $labels,
        'data' => $data,
        'details' => $details
    ],
    'timeline' => $timelineData
]);
?>