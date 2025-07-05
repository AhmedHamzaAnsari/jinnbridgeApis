<?php
// fetch.php
ini_set('max_execution_time', 0);
set_time_limit(5000);
header("Refresh: 4000; URL=" . $_SERVER['REQUEST_URI']);

include("../../../config.php");

echo 'Start Time: ' . date('Y-m-d H:i:s') . '<br>';

$access_key = '03201232927';
$pass = $_GET["key"] ?? '';
$date = date('Y-m-d H:i:s');

if (empty($pass)) {
    echo 'Key is Required<br>';
    exit;
}

if ($pass !== $access_key) {
    echo 'Wrong Key...<br>';
    exit;
}

$sql_query1 = "SELECT * FROM dealers WHERE privilege='Dealer' AND indent_price=1 ;";
$result1 = $db->query($sql_query1) or die("Error: " . mysqli_error($db));

while ($user = $result1->fetch_assoc()) {
    $dealers_id = $user['id'];
    $sap = $user['sap_no'];
    $acount = $user['acount'];

    // Set up cURL
    $curl = curl_init();
    curl_setopt_array($curl, [
        CURLOPT_URL => "http://192.168.3.5:5003/api_server/get/get_dealer_legders.php?dealerCode=$sap",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'GET',
    ]);

    $response = curl_exec($curl);
    curl_close($curl);

    // Check for cURL error
    if (!$response) {
        echo "cURL Error for SAP $sap: " . curl_error($curl) . "<br>";
        continue;
    }

    $arrayData = json_decode($response, true);

    if (json_last_error() !== JSON_ERROR_NONE) {
        echo "JSON decode error for SAP $sap: " . json_last_error_msg() . "<br>";
        continue;
    }

    // If data is not in array format
    if (!is_array($arrayData)) {
        echo "Invalid JSON or no data for SAP: $sap<br>";
        continue;
    }

    // Check required keys
    if (empty($arrayData['BP Code']) || !isset($arrayData['Current Balance'])) {
        echo "Missing keys in response for SAP: $sap<br>";
        continue;
    }

    // Extract values
    $LEDGER_BALANCE = $arrayData['Current Balance'];
    $BP_CODE = $arrayData['BP Code'];
    $BP_NAME = $arrayData['BP Name'] ?? '';

    // Insert into log
    $log = "INSERT INTO `dealer_ledger_log`
        (`type`,`dealer_id`, `old_ledger`, `new_ledger`, `datetime`, `description`, `doc_no`, `debit_no`, `assignment_no`, `document_type`, `sap_no`, `ledger_balance`, `created_at`, `created_by`)
        VALUES
        ('Service Update','$dealers_id', '$acount', '$LEDGER_BALANCE', '$date', '', '', '', '', '', '$sap', '$LEDGER_BALANCE', '$date', '1')";

    if (!mysqli_query($db, $log)) {
        echo 'Log Insert Error for SAP ' . $sap . ': ' . mysqli_error($db) . '<br>';
    }

    // Update the dealer's current account
    $updateQuery = "UPDATE `dealers` SET `acount`='$LEDGER_BALANCE' WHERE `id`='$dealers_id' AND `sap_no`='$sap'";

    if (!mysqli_query($db, $updateQuery)) {
        echo 'Update Error for SAP ' . $sap . ': ' . mysqli_error($db) . '<br>';
    } else {
        echo "SAP $sap updated successfully. New Balance: $LEDGER_BALANCE<br>";
    }
}

echo 'End Time: ' . date('Y-m-d H:i:s');
?>
