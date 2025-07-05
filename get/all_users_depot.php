<?php
include("../config.php");
header('Content-Type: application/json');

$access_key = '03201232927';
$pass = $_GET["key"] ?? '';

if (empty($pass)) {
    echo json_encode(['error' => 'Key is Required']);
    exit;
}
if ($pass !== $access_key) {
    echo json_encode(['error' => 'Wrong Key']);
    exit;
}

// Fetch users with depot name from geofenceing
$sql = "
SELECT
    u.id,
    u.name,
    u.privilege,
    u.login,
    u.email,
    u.telephone,
    u.status,
    u.subacc_id,
    u.depot,
    u.description,
    g.consignee_name AS depot_name
FROM users_depot u
LEFT JOIN geofenceing g ON u.depot = g.id
ORDER BY u.id DESC
";

$result = $db->query($sql);
if (!$result) {
    echo json_encode(['error' => mysqli_error($db)]);
    exit;
}

$users = [];
while ($row = $result->fetch_assoc()) {
    // You can hide password if needed, but not selecting it here
    $users[] = $row;
}

echo json_encode($users);
?>
