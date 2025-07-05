<?php
// fetch.php  
include("../config.php");

$access_key = '03201232927';

$pass = $_GET["key"] ?? '';
$pre = $_GET["pre"] ?? '';
$id = $_GET["user_id"] ?? '';

if (!$pass) {
    echo 'Key is Required';
    exit;
}

if ($pass !== $access_key) {
    echo 'Wrong Key...';
    exit;
}

$where_clause = '';

if ($pre == 'ZM') {
    $where_clause = "dl.zm=$id";
} elseif ($pre == 'TM') {
    $where_clause = "dl.tm=$id";
} elseif ($pre == 'ASM') {
    $where_clause = "dl.asm=$id";
}else{
    $where_clause = '1=1';
}

$sql_query1 = "SELECT 
    dl.*, 
    dl.`co-ordinates` AS co_ordinates, 
    usz.name AS zm_name, 
    ust.name AS tm_name, 
    usa.name AS asm_name  
FROM dealers AS dl 
LEFT JOIN users AS usz ON usz.id = dl.zm
LEFT JOIN users AS ust ON ust.id = dl.tm
LEFT JOIN users AS usa ON usa.id = dl.asm
WHERE dl.privilege = 'Dealer' AND indent_price=1 AND $where_clause 
ORDER BY dl.id DESC;";

$result1 = $db->query($sql_query1) or die("Error: " . mysqli_error($db));

$thread = [];
while ($user = $result1->fetch_assoc()) {
    $thread[] = $user;
}

$thread = utf8ize($thread);
$json = json_encode($thread, JSON_PRETTY_PRINT);

if ($json === false) {
    echo json_encode(["error" => "JSON encoding failed", "details" => json_last_error_msg()]);
} else {
    echo $json;
}

function utf8ize($data) {
    if (is_array($data)) {
        return array_map('utf8ize', $data);
    } elseif (is_string($data)) {
        return mb_convert_encoding($data, 'UTF-8', 'UTF-8');
    }
    return $data;
}
?>