<?php

$curl = curl_init();

curl_setopt_array($curl, array(
  CURLOPT_URL => 'labs2.unitedtracker.com/api/Trackers/GetUserVehicleLastLocationbyUserName?UserName=ubaid1&Password=HUZAFA@1',
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => '',
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 0,
  CURLOPT_FOLLOWLOCATION => true,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => 'GET',
  CURLOPT_HTTPHEADER => array(
    'API_KEY: 7b6f169d544e4eda4b2b263e6bffe50d',
    'Authorization: Basic UElUQjo4NTMwNWFlMjg1ZjVhNzk1OWY4OWE4YWY5Y2FhNWY1Nw=='
  ),
));

$response = curl_exec($curl);

curl_close($curl);
echo $response;