<?php

$curl = curl_init();

curl_setopt_array($curl, array(
    CURLOPT_URL => 'https://asco.flowpetroleum.com.pk:50000/b1s/v1/Login',
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => '',
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 0,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_SSL_VERIFYPEER => false, // Disable SSL verification
    CURLOPT_SSL_VERIFYHOST => false, // Disable SSL host verification
    CURLOPT_CUSTOMREQUEST => 'POST',
    CURLOPT_POSTFIELDS => '{
        "CompanyDB": "ASCO_PROD",
        "Password": "1234",
        "UserName": "manager"
    }',
    CURLOPT_HTTPHEADER => array(
        'Content-Type: text/plain',
        'Cookie: ROUTEID=.node2'
    ),
));

$response = curl_exec($curl);

curl_close($curl);
echo $response;
?>
