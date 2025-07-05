<?php
// URL of the API
include("../../config.php");
session_start();
$order_data = $_GET['data'];
$type = $_GET['type'];
$dealer_sap = $_GET['dealer_sap'];



 $apiUrl = 'http://110.38.69.114:5003/jinnbridgeApis/create/order/sap_order_push.php?data='.$order_data.'&type='.$type.'&dealer_sap='.$dealer_sap.'';

// Fetching XML content from the API
$xmlString = file_get_contents($apiUrl);

// Create SimpleXML object
$xml = simplexml_load_string($xmlString);

// Access the SaleOrder value
$SaleOrder = (string) $xml->content->children('m', true)->properties->children('d', true)->SaleOrder;

// Output the SaleOrder value
// echo 'Sales' .$SaleOrder;
echo $SaleOrder;


?>
