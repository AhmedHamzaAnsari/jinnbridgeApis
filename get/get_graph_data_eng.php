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

// Date Filter
if ($from_date && $to_date) {
    $where .= " AND DATE(sr.created_at) BETWEEN '$from_date' AND '$to_date'";
} elseif ($from_date) {
    $where .= " AND DATE(sr.created_at) >= '$from_date'";
} elseif ($to_date) {
    $where .= " AND DATE(sr.created_at) <= '$to_date'";
}

// Dealer Filter
if (!empty($dealers)) {
    $dealerList = implode(",", array_map('intval', $dealers));
    $where .= " AND sr.dealer_id IN ($dealerList)";
}

// Response Filter
if ($response) {
    $responseValue = ucfirst(strtolower($response));
    $where .= " AND sr.response = '$responseValue'";
}

// Category Filter
if (!empty($categories)) {
    $categoryList = implode(",", array_map('intval', $categories));
    $where .= " AND sr.category_id IN ($categoryList)";
}

// Question Filter
if (!empty($questions)) {
    $questionList = implode(",", array_map('intval', $questions));
    $where .= " AND sr.question_id IN ($questionList)";
}

// Count Yes
$yesSQL = "SELECT COUNT(*) as count FROM survey_response_eng sr $where AND sr.response = 'Yes'";
$res = $db->query($yesSQL);
$yes = $res ? (int)$res->fetch_assoc()['count'] : 0;

// Count No
$noSQL = "SELECT COUNT(*) as count FROM survey_response_eng sr $where AND sr.response = 'No'";
$res = $db->query($noSQL);
$no = $res ? (int)$res->fetch_assoc()['count'] : 0;

// -------- Issue Chart by Category --------
$issueSQL = "
    SELECT 
        sc.name AS category,
        sr.response,
        COUNT(*) AS count
    FROM survey_response_eng sr
    LEFT JOIN survey_category_eng sc ON sc.id = sr.category_id
    $where
    AND sr.comment IS NOT NULL AND sr.comment != ''
    AND sr.response IN ('Yes', 'No')
    GROUP BY sr.category_id, sr.response
    ORDER BY sc.name
";

$res = $db->query($issueSQL);
$categoryLabels = [];
$yesData = [];
$noData = [];
$details = [];

if ($res && $res->num_rows > 0) {
    $grouped = [];

    while ($row = $res->fetch_assoc()) {
        $category = $row['category'] ?? 'Uncategorized';
        $responseType = $row['response'];
        $count = (int)$row['count'];
        $grouped[$category][$responseType] = $count;
    }

    foreach ($grouped as $category => $responses) {
        $categoryLabels[] = $category;
        $yesData[] = $responses['Yes'] ?? 0;
        $noData[] = $responses['No'] ?? 0;

        // Fetch issue details per category (with question)
        $detailSQL = "
            SELECT 
                DATE(sr.created_at) AS date,
                sr.comment,
                sr.response,
                COALESCE(d.name, CONCAT('Dealer ID: ', sr.dealer_id)) AS site_name,
                q.question AS question_text
            FROM survey_response_eng sr
            LEFT JOIN dealers d ON d.id = sr.dealer_id
            LEFT JOIN survey_category_eng sc ON sc.id = sr.category_id
            LEFT JOIN survey_category_questions_eng q ON q.id = sr.question_id
            $where
            AND sr.comment IS NOT NULL AND sr.comment != ''
            AND sc.name = '" . $db->real_escape_string($category) . "'
            ORDER BY sr.created_at DESC
            LIMIT 100
        ";

        $subres = $db->query($detailSQL);
        $details[$category] = [];

        if ($subres && $subres->num_rows > 0) {
            while ($subrow = $subres->fetch_assoc()) {
                $details[$category][] = [
                    'date'       => $subrow['date'],
                    'site_name'  => $subrow['site_name'],
                    'response'   => $subrow['response'],
                    'comment'    => $subrow['comment'],
                    'question'   => $subrow['question_text'] ?? 'N/A'
                ];
            }
        }
    }
}

// -------- Timeline Chart --------
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

// Prepare timeline chart data
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

// -------- Attachment Count --------
$attachmentWhere = "WHERE 1=1";
if ($from_date && $to_date) {
    $attachmentWhere .= " AND DATE(created_at) BETWEEN '$from_date' AND '$to_date'";
} elseif ($from_date) {
    $attachmentWhere .= " AND DATE(created_at) >= '$from_date'";
} elseif ($to_date) {
    $attachmentWhere .= " AND DATE(created_at) <= '$to_date'";
}

if (!empty($dealers)) {
    $dealerList = implode(",", array_map('intval', $dealers));
    $attachmentWhere .= " AND dealer_id IN ($dealerList)";
}
if (!empty($categories)) {
    $categoryList = implode(",", array_map('intval', $categories));
    $attachmentWhere .= " AND category_id IN ($categoryList)";
}
if (!empty($questions)) {
    $questionList = implode(",", array_map('intval', $questions));
    $attachmentWhere .= " AND question_id IN ($questionList)";
}

$attachmentSQL = "SELECT COUNT(*) as count FROM survey_response_files_eng $attachmentWhere";
$attachmentRes = $db->query($attachmentSQL);
$attachmentCount = $attachmentRes ? (int)$attachmentRes->fetch_assoc()['count'] : 0;

// -------- Final Response --------
echo json_encode([
    'yes' => $yes,
    'no' => $no,
    'issues' => [
        'labels' => $categoryLabels,
        'yes_data' => $yesData,
        'no_data' => $noData,
        'details' => $details
    ],
    'timeline' => $timelineData,
    'attachment_count' => $attachmentCount
]);
?>
