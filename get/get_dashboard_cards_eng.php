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

if ($from_date && $to_date) {
    $whereSurvey .= " AND DATE(sr.created_at) BETWEEN '$from_date' AND '$to_date'";
} elseif ($from_date) {
    $whereSurvey .= " AND DATE(sr.created_at) >= '$from_date'";
} elseif ($to_date) {
    $whereSurvey .= " AND DATE(sr.created_at) <= '$to_date'";
}

if (!empty($dealers) && is_array($dealers)) {
    $dealerList = implode(",", array_map('intval', $dealers));
    $whereSurvey .= " AND sr.dealer_id IN ($dealerList)";
}

if ($response) {
    $responseValue = ucfirst(strtolower($response));
    $whereSurvey .= " AND sr.response = '$responseValue'";
}

if (!empty($categories)) {
    $categoryList = implode(",", array_map(function ($c) {
        return "'" . addslashes($c) . "'";
    }, $categories));
    $whereSurvey .= " AND sr.category_id IN ($categoryList)";
}

if (!empty($questions)) {
    $questionList = implode(",", array_map(function ($q) {
        return "'" . addslashes($q) . "'";
    }, $questions));
    $whereSurvey .= " AND sr.question_id IN ($questionList)";
}

$sql = "SELECT COUNT(DISTINCT sr.main_id) as inspections FROM survey_response_eng sr $whereSurvey";
$res = $db->query($sql);
$inspections = $res ? (int)$res->fetch_assoc()['inspections'] : 0;

$sql = "SELECT COUNT(*) as yesCount FROM survey_response_eng sr $whereSurvey AND sr.response = 'Yes'";
$res = $db->query($sql);
$yesCount = $res ? (int)$res->fetch_assoc()['yesCount'] : 0;

$sql = "SELECT COUNT(*) as noCount FROM survey_response_eng sr $whereSurvey AND sr.response = 'No'";
$res = $db->query($sql);
$noCount = $res ? (int)$res->fetch_assoc()['noCount'] : 0;

// =================== Attachments ===================
$whereFiles = "WHERE 1=1";

if ($from_date && $to_date) {
    $whereFiles .= " AND DATE(f.created_at) BETWEEN '$from_date' AND '$to_date'";
} elseif ($from_date) {
    $whereFiles .= " AND DATE(f.created_at) >= '$from_date'";
} elseif ($to_date) {
    $whereFiles .= " AND DATE(f.created_at) <= '$to_date'";
}

if (!empty($dealers)) {
    $dealerList = implode(",", array_map('intval', $dealers));
    $whereFiles .= " AND f.dealer_id IN ($dealerList)";
}
if (!empty($categories)) {
    $categoryList = implode(",", array_map(function ($c) {
        return "'" . addslashes($c) . "'";
    }, $categories));
    $whereFiles .= " AND f.category_id IN ($categoryList)";
}
if (!empty($questions)) {
    $questionList = implode(",", array_map(function ($q) {
        return "'" . addslashes($q) . "'";
    }, $questions));
    $whereFiles .= " AND f.question_id IN ($questionList)";
}

$sql = "SELECT COUNT(*) AS attachmentCount FROM survey_response_files_eng f $whereFiles";
$res = $db->query($sql);
$attachmentCount = $res ? (int)$res->fetch_assoc()['attachmentCount'] : 0;

$sql = "
    SELECT 
        f.id,
        f.dealer_id,
        d.name AS dealer_name,
        f.category_id AS category,
        f.question_id AS question,
        f.file,
        DATE(f.created_at) AS date
    FROM survey_response_files_eng f
    LEFT JOIN dealers d ON d.id = f.dealer_id
    $whereFiles
    ORDER BY f.created_at DESC
";

$res = $db->query($sql);
$attachmentList = [];
$baseUrl = "http://192.168.3.5:5003/jinnBridge_files/uploads";
$sr = 1;

if ($res && $res->num_rows > 0) {
    while ($row = $res->fetch_assoc()) {
        $attachmentList[] = [
            'sr_no'        => $sr++,
            'dealer_name'  => $row['dealer_name'] ?: "Dealer #{$row['dealer_id']}",
            'category'     => $row['category'],
            'question'     => $row['question'],
            'file_url'     => "$baseUrl/{$row['file']}",
            'date'         => $row['date']
        ];
    }
}

// =================== Dispenser Setup ===================
$whereDispenser = "WHERE 1=1";

if ($from_date && $to_date) {
    $whereDispenser .= " AND DATE(d.created_at) BETWEEN '$from_date' AND '$to_date'";
} elseif ($from_date) {
    $whereDispenser .= " AND DATE(d.created_at) >= '$from_date'";
} elseif ($to_date) {
    $whereDispenser .= " AND DATE(d.created_at) <= '$to_date'";
}

if (!empty($dealers)) {
    $dealerList = implode(",", array_map('intval', $dealers));
    $whereDispenser .= " AND d.dealer_id IN ($dealerList)";
}

// Count setup records
$sql = "SELECT COUNT(*) AS dispenserCount FROM dealer_dispenser_setup d $whereDispenser";
$res = $db->query($sql);
$dispenserCount = $res ? (int)$res->fetch_assoc()['dispenserCount'] : 0;

// Fetch setup list (no button, just raw data)
$sql = "
    SELECT 
        d.id,
        d.dealer_id,
        deal.name AS dealer_name,
        d.no_fuel_system,
        DATE(d.created_at) AS created_at,
        u.name AS created_by
    FROM dealer_dispenser_setup d
    LEFT JOIN dealers deal ON deal.id = d.dealer_id
    LEFT JOIN users u ON u.id = d.created_by
    $whereDispenser
    ORDER BY d.created_at DESC
";

$res = $db->query($sql);
$dispenserList = [];
$sr = 1;

if ($res && $res->num_rows > 0) {
    while ($row = $res->fetch_assoc()) {
        $dispenserList[] = [
            'sr_no'        => $sr++,
            'id'           => $row['id'],              // record id
            'dealer_id'    => $row['dealer_id'],       // needed for encryption!
            'dealer_name'  => $row['dealer_name'] ?: "Dealer #{$row['dealer_id']}",
            'created_by'   => $row['created_by'] ?: 'Unknown',
            'created_at'   => $row['created_at']
        ];
    }
}
// =================== Work Orders ===================
$whereWorkOrder = "WHERE 1=1";

if ($from_date && $to_date) {
    $whereWorkOrder .= " AND DATE(sr.created_at) BETWEEN '$from_date' AND '$to_date'";
} elseif ($from_date) {
    $whereWorkOrder .= " AND DATE(sr.created_at) >= '$from_date'";
} elseif ($to_date) {
    $whereWorkOrder .= " AND DATE(sr.created_at) <= '$to_date'";
}

if (!empty($dealers)) {
    $dealerList = implode(",", array_map('intval', $dealers));
    $whereWorkOrder .= " AND sr.dealer_id IN ($dealerList)";
}
if (!empty($categories)) {
    $categoryList = implode(",", array_map(function ($c) {
        return "'" . addslashes($c) . "'";
    }, $categories));
    $whereWorkOrder .= " AND sr.category_id IN ($categoryList)";
}
if (!empty($questions)) {
    $questionList = implode(",", array_map(function ($q) {
        return "'" . addslashes($q) . "'";
    }, $questions));
    $whereWorkOrder .= " AND sr.question_id IN ($questionList)";
}

// Count work orders with non-empty value
$sql = "SELECT COUNT(*) AS workOrderCount FROM survey_response_eng_new sr $whereWorkOrder AND sr.work_order IS NOT NULL AND sr.work_order != ''";
$res = $db->query($sql);
$workOrderCount = $res ? (int)$res->fetch_assoc()['workOrderCount'] : 0;

// Fetch detailed list
$sql = "
    SELECT 
        sr.dealer_id,
        d.name AS dealer_name,
        sr.category_id,
        sr.question_id,
        sr.work_order
    FROM survey_response_eng_new sr
    LEFT JOIN dealers d ON d.id = sr.dealer_id
    $whereWorkOrder
    AND sr.work_order IS NOT NULL AND sr.work_order = 'true'
    ORDER BY sr.created_at DESC
";

$res = $db->query($sql);
$orderCountData = [];
$sr = 1;

if ($res && $res->num_rows > 0) {
    while ($row = $res->fetch_assoc()) {
        $orderCountData[] = [
            'sr_no'        => $sr++,
            'dealer_id'    => $row['dealer_id'], // ✅ Added this

            'dealer_name'  => $row['dealer_name'] ?: "Dealer #{$row['dealer_id']}",
            'category_id'  => $row['category_id'],
            'question_id'  => $row['question_id'],
            'work_order'   => $row['work_order']
        ];
    }
}

// =================== Final Output ===================
echo json_encode([
    'inspections'             => $inspections,
    'yes'                     => $yesCount,
    'no'                      => $noCount,
    'attachments'             => $attachmentCount,
    'files'                   => $attachmentList,
    'dispenser_setup_count'   => $dispenserCount,
    'dispenser_setups'        => $dispenserList,

    // ✅ ADD THESE TWO:
    'work_order_count'        => $workOrderCount,
    'work_orders'             => $orderCountData,
]);
?>