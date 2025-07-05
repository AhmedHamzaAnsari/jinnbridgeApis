<?php

$curl = curl_init();

curl_setopt_array($curl, array(
  CURLOPT_URL => 'https://trackpoint.omnicombalfourbeatty.com/tpapi/convert/lonlattoetmy?lon=-1.462184974454799&lat=52.915079498476224&tolerance=10&mapversion=0',
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => '',
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 0,
  CURLOPT_FOLLOWLOCATION => true,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => 'GET',
  CURLOPT_HTTPHEADER => array(
    'Authorization: Basic dHJhY2stdHJhY2tlcjpmc2xqa3NEZ241eXUk'
  ),
));

$response = curl_exec($curl);

curl_close($curl);
echo $response;
