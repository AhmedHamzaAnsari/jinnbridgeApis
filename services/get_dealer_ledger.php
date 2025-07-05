<?php


$sap = $_GET['sap'];
$currentDate = date('Y-m-d\TH:i');

// Convert current date to timestamp
$currentTimestamp = strtotime($currentDate);

// Calculate timestamp for 10 days ago
$tenDaysAgoTimestamp = $currentTimestamp - (1 * 24 * 60 * 60); // 10 days * 24 hours * 60 minutes * 60 seconds

// Convert timestamp to date in 'Y-m-d\TH:i' format
$tenDaysAgoFormatted = date('Y-m-d\TH:i', $tenDaysAgoTimestamp);

// Output the result
// echo "Current date and time: " . $currentDate . PHP_EOL;
// echo "10 days ago: " . $tenDaysAgoFormatted;

if ($sap != "") {
    $curl = curl_init();
    $dealer = $sap;
    // echo 'https://103.111.160.120:44300/sap/opu/odata/sap/ZP2P_TRACK_PROJ_SRV/InitialSet2(CustomerId=\''.$dealer.'\',FromDate=datetime\''.$tenDaysAgoFormatted.'\',ToDate=datetime\''.$currentDate.'\')';
    curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://103.111.160.120:44300/sap/opu/odata/sap/ZP2P_TRACK_PROJ_SRV/InitialSet2(CustomerId=\''.$dealer.'Z\',FromDate=datetime\''.$tenDaysAgoFormatted.'\',ToDate=datetime\''.$currentDate.'\')',
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
    )
    );

    $response = curl_exec($curl);

    curl_close($curl);
    echo $response;

}

