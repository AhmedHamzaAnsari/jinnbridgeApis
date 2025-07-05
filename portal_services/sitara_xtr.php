<?php

$curl = curl_init();

curl_setopt_array($curl, array(
  CURLOPT_URL => "http://202.142.175.117/goservice/TrackpointWebService.asmx",
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => "",
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 30,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => "POST",
  CURLOPT_POSTFIELDS => "<?xml version=\"1.0\" encoding=\"utf-8\"?>\n<soap:Envelope xmlns:xsi=\"http://www.w3.org/2001/XMLSchema-instance\" xmlns:xsd=\"http://www.w3.org/2001/XMLSchema\" xmlns:soap=\"http://schemas.xmlsoap.org/soap/envelope/\">\n<soap:Body>\n<GetLocation xmlns=\"http://tempuri.org/\">  \n<username>xt-go</username>\n<password>go12</password>\n</GetLocation>\n</soap:Body>\n</soap:Envelope>",
  CURLOPT_HTTPHEADER => array(
    "cache-control: no-cache",
    "content-type: text/xml",
    "postman-token: 345b00ed-0a8a-76d8-d1d0-9a5a65070aed"
  ),
));

$response = curl_exec($curl);
$err = curl_error($curl);

curl_close($curl);

if ($err) {
  echo "cURL Error #:" . $err;
} else {
  $finalresponse =  strip_tags($response);
  echo $finalresponse;
}