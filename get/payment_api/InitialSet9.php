<?php


$SalesOrder = $_GET['SalesOrder'];

if ($SalesOrder != "") {



    $curl = curl_init();

    curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://103.111.160.120:44302/sap/opu/odata/sap/ZP2P_TRACK_PROJ_SRV/InitialSet9(SalesOrder=\''.$SalesOrder.'\')',
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
            'Authorization: Basic U0FQLkFQSTpTYXBzYXBAMTI=',
            'Cookie: SAP_SESSIONID_PRD_100=ZJ9-5noLBAC-pLFmOiaqNQ0xFaPzMhHuvAkAUFa6A50%3d; sap-usercontext=sap-client=100'
        ),
    )
    );

    $response = curl_exec($curl);

    curl_close($curl);
    echo $response;
}