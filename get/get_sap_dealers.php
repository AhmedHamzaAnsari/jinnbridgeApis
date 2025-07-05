<?php
// fetch.php  
include("../config.php");

header('Content-Type: application/json');

$access_key = '03201232927';

$data = get_dealers();
$decodedData = json_decode($data, true);

if ($decodedData === null || !isset($decodedData['value'])) {
    echo json_encode(["error" => "Invalid API response", "details" => json_last_error_msg()]);
    exit;
}

$filteredData = $decodedData['value'];

echo json_encode($filteredData, JSON_PRETTY_PRINT);

function login() {
    $curl = curl_init();

    curl_setopt_array($curl, array(
        CURLOPT_URL => 'http://jppl.vdc.services:50001/b1s/v1/Login',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => json_encode([
            "CompanyDB" => "JINN_31012025",
            "Password" => "Mike@123",
            "UserName" => "MobApp"
        ]),
        CURLOPT_HTTPHEADER => array(
            'Content-Type: application/json'
        ),
    ));

    $response = curl_exec($curl);
    curl_close($curl);

    return json_decode($response, true);
}

function get_dealers() {
    $sessionData = login();
    
    if (!isset($sessionData['SessionId'])) {
        return json_encode(["error" => "Failed to obtain session"]);
    }

    $sessionId = $sessionData['SessionId'];

    $curl = curl_init();
    curl_setopt_array($curl, array(
        CURLOPT_URL => 'http://jppl.vdc.services:50001/b1s/v1/SQLQueries(\'GetDealerInfo\')/List',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'GET',
        CURLOPT_HTTPHEADER => array(
            "Cookie: B1SESSION=$sessionId"
        ),
    ));

    $response = curl_exec($curl);
    curl_close($curl);

    return $response;
}
?>
