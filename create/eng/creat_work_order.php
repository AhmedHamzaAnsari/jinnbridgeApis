<?php
header('Content-Type: application/json');
include("../../config.php");

// Only allow POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'error' => 'Invalid request method']);
    exit;
}



// Upload directory
$uploadDir = 'C:/xampp/htdocs/jinnbridgeApis/create/eng/uploads/';
if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);

// File Upload Function
function saveUploadedFile($field, $uploadDir) {
    if (!empty($_FILES[$field]['name']) && $_FILES[$field]['error'] === UPLOAD_ERR_OK) {
        $filename = uniqid() . '_' . basename($_FILES[$field]['name']);
        $path = $uploadDir . $filename;
        if (move_uploaded_file($_FILES[$field]['tmp_name'], $path)) {
            return 'uploads/' . $filename;
        }
    }
    return '';
}

// Base64 Signature Save
function saveBase64Image($base64, $uploadDir, $prefix = 'sign_') {
    $data = explode(',', $base64);
    if (count($data) == 2 && preg_match('/^data:image\/(\w+);base64$/', $data[0], $matches)) {
        $ext = $matches[1];
        $decoded = base64_decode($data[1]);
        if ($decoded !== false) {
            $filename = $prefix . uniqid() . '.' . $ext;
            $path = $uploadDir . $filename;
            if (file_put_contents($path, $decoded)) {
                return 'uploads/' . $filename;
            }
        }
    }
    return '';
}

// Handle arrays as CSVs
function csvField($key) {
    return isset($_POST[$key]) && is_array($_POST[$key]) ? implode(', ', $_POST[$key]) : ($_POST[$key] ?? '');
}

// POST fields
$order_num         = $_POST['order_num'] ?? '';
$date_issued       = $_POST['date_issued'] ?? '';
$requested_by      = $_POST['requested_by'] ?? '';
$desgination       = $_POST['desgination'] ?? '';
$station_location  = $_POST['station_location'] ?? '';
$code              = $_POST['code'] ?? '';

$description_of_work = csvField('description_of_work');
$priority            = csvField('priority');
$quantity            = csvField('quantity');
$unit                = csvField('unit');

$reason         = csvField('reason');
$equip_name     = csvField('equip_name');
$tag_no         = csvField('tag_no');
$make_model     = csvField('make_model');
$last_date      = csvField('last_date');

$technican_name = $_POST['technican_name'] ?? '';
$dealer         = $_POST['dealer'] ?? '';
$compnay        = $_POST['compnay'] ?? '';
$contact_number = $_POST['contact_number'] ?? '';
$deaprtment     = $_POST['deaprtment'] ?? '';
$start_date     = $_POST['start_date'] ?? '';
$complete_data  = $_POST['complete_data'] ?? '';
$remarks        = $_POST['remarks'] ?? '';
$date_completed = $_POST['date_completed'] ?? '';
$material_used  = $_POST['material_used'] ?? '';
$issues         = $_POST['issues'] ?? '';
$created_by     = $_POST['created_by'] ?? 'admin';

// File uploads or base64 fallback
$requested_by_sign = saveUploadedFile('requested_by_sign', $uploadDir);
$appoved_by_sign   = saveUploadedFile('appoved_by_sign', $uploadDir);
$tech_sign         = saveUploadedFile('tech_sign', $uploadDir);

if (!$requested_by_sign && !empty($_POST['requested_signature_drawn'])) {
    $requested_by_sign = saveBase64Image($_POST['requested_signature_drawn'], $uploadDir, 'requested_');
}
if (!$appoved_by_sign && !empty($_POST['manager_signature_drawn'])) {
    $appoved_by_sign = saveBase64Image($_POST['manager_signature_drawn'], $uploadDir, 'manager_');
}
if (!$tech_sign && !empty($_POST['technician_signature_drawn'])) {
    $tech_sign = saveBase64Image($_POST['technician_signature_drawn'], $uploadDir, 'technician_');
}

// SQL
$sql = "
INSERT INTO work_order (
    order_num, date_issued, requested_by, desgination, station_location, code,
    description_of_work, priority, quantity, unit, reason,
    equip_name, tag_no, make_model, last_date,
    technican_name, dealer, compnay, contact_number, deaprtment,
    start_date, complete_data, remarks,
    requested_by_sign, appoved_by_sign, date_completed,
    material_used, issues, tech_sign,
    created_at, created_by
) VALUES (
    '$order_num', '$date_issued', '$requested_by', '$desgination', '$station_location', '$code',
    '$description_of_work', '$priority', '$quantity', '$unit', '$reason',
    '$equip_name', '$tag_no', '$make_model', '$last_date',
    '$technican_name', '$dealer', '$compnay', '$contact_number', '$deaprtment',
    '$start_date', '$complete_data', '$remarks',
    '$requested_by_sign', '$appoved_by_sign', '$date_completed',
    '$material_used', '$issues', '$tech_sign',
    NOW(), '$created_by'
)
";

if ($db->query($sql)) {
    echo json_encode(['success' => true, 'message' => 'Work order created successfully']);
} else {
    echo json_encode(['success' => false, 'error' => 'DB Error: ' . $db->error]);
}

$db->close();