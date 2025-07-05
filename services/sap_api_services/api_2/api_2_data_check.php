<?php


$sap = $_GET['sap'];
$currentDate = date('Ymd');
$currentTimestamp = strtotime($currentDate);

// Calculate timestamp for 10 days ago
$tenDaysAgoTimestamp = $currentTimestamp - (1 * 24 * 60 * 60); // 10 days * 24 hours * 60 minutes * 60 seconds

// Convert timestamp to date in 'Y-m-d\TH:i' format
$tenDaysAgoFormatted = date('Ymd', $tenDaysAgoTimestamp);

// Output the result
//  echo "Current date and time: " . $currentDate . PHP_EOL;
//  echo "10 days ago: " . $tenDaysAgoFormatted;

// Output the result
// echo "Current date and time: " . $currentDate . PHP_EOL;
// echo "10 days ago: " . $tenDaysAgoFormatted;

if ($sap != "") {
    // CURLOPT_URL => 'https://103.111.160.120:44300/sap/opu/odata/sap/ZP2P_TRACK_PROJ_SRV/InitialSet2(CustomerId=\''.$dealer.'Z\',FromDate=datetime\''.$tenDaysAgoFormatted.'\',ToDate=datetime\''.$currentDate.'\')',


    $curl = curl_init();

    curl_setopt_array(
        $curl,
        array(
            CURLOPT_URL => 'https://103.111.160.120:44302/sap/opu/odata/sap/ZP2P_TRACK_PROJ_SRV/InitialSet1(CustomerId=\''.$sap.'\')',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false),
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false),
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => array(
                'Authorization: Basic VE1DLlNVUFBPUlQ6c2FwQDEyMzQ1',
                'Cookie: SAP_SESSIONID_PRD_100=olHIy0Hii30aAKMGvjH4NR2Zeh3MChHuoE0AUFa6A50%3d; sap-usercontext=sap-client=100'
            ),
        )
    );

    $response = curl_exec($curl);

    curl_close($curl);
    echo $response;


}