<?php
$url = 'http://151.106.17.246:8080/sitara/services/ledger.php'; // Replace '...' with the provided data string
$data = file_get_contents($url);

// Extracting the relevant information from the data string
$startPos = strpos($data, '[');
$endPos = strrpos($data, ']');
$jsonData = substr($data, $startPos, $endPos - $startPos + 1);

// Decoding the JSON string into a PHP array
$arrayData = json_decode($jsonData, true);

// Encoding the array as JSON for better formatting
$jsonFormatted = json_encode($arrayData, JSON_PRETTY_PRINT);

// Output the formatted JSON
echo $jsonFormatted;
?><?php

$curl = curl_init();
$dealer = '25860275';
curl_setopt_array($curl, array(
  CURLOPT_URL => 'https://103.111.160.120:44300/sap/opu/odata/sap/ZP2P_TRACK_PROJ_SRV/InitialSet2(CustomerId=\'$dealer\',FromDate=datetime\'2023-08-17T12:00\',ToDate=datetime\'2024-01-04T12:00\')',
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => '',
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 0,
  CURLOPT_FOLLOWLOCATION => true,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => 'GET',
  curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false),
  curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false),
  CURLOPT_HTTPHEADER => array(
	'Authorization: Basic dG1jX2FzaW06dG1jQDEyMw==',
    'Cookie: SAP_SESSIONID_DEV_200=QM0kpuYJPVhgnaB_Nf4biCduPmWrBBHuikAAUFa6fv0%3d; sap-usercontext=sap-client=200'
  ),
));

$response = curl_exec($curl);

curl_close($curl);
echo $response;
