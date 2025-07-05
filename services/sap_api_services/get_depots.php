<?php
// fetch.php
include("../../config.php");
ini_set('max_execution_time', 0);

$url1 = $_SERVER['REQUEST_URI'];
header("Refresh: 600; URL=$url1");

// Configuration
$access_key = '03201232927';
$pass = $_GET["key"] ?? ''; // Handle undefined index
echo 'Sap Depots : ' . date('Y-m-d H:i:s') . '<br>';

if ($pass !== '') {
    if ($pass === $access_key) {
        // Initialize cURL
        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL => 'http://localhost:8080/api_server/get/get_all_depots.php',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
        ]);

        $response = curl_exec($curl);
        if (curl_errno($curl)) {
            echo 'cURL error: ' . curl_error($curl);
            curl_close($curl);
            exit; // Stop further execution
        }
        curl_close($curl);

        // Decode JSON response
        $response_data = json_decode($response, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            echo 'JSON decode error: ' . json_last_error_msg();
            exit; // Stop further execution
        }

        // Process each item
        foreach ($response_data as $item) {
            $WhsCode = $item['WhsCode'] ?? null;
            $WhsName = $item['WhsName'] ?? null;

            // Skip if critical fields are missing
            if (!$WhsCode || !$WhsName) {
                continue;
            }

            // Escape variables for security
            $WhsCode = mysqli_real_escape_string($db, $WhsCode);
            $WhsName = mysqli_real_escape_string($db, $WhsName);

            // Prepare SQL query
            $sql = "
                INSERT INTO `portalflow`.`geofenceing`
                (`code`, `consignee_name`, `location`, `type`, `geotype`)
                VALUES ('$WhsCode', '$WhsName', '$WhsName', 'circle', 'Depot')
            ";

            // Execute the query
            if (mysqli_query($db, $sql)) {
                echo "Geofence entry created for: " . htmlspecialchars($WhsName) . "<br>";
            } else {
                echo "Database error: " . mysqli_error($db) . "<br>" . $sql;
            }
        }
    } else {
        echo 'Wrong Key...';
    }
} else {
    echo 'Key is Required';
}

echo date('Y-m-d H:i:s');
?>
