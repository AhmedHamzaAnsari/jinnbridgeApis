<?php
//fetch.php  
include("../../config.php");


$access_key = '03201232927';

$pass = $_GET["key"];
// $id=$_GET["id"];
if ($pass != '') {
	if ($pass == $access_key) {
		$id = $_GET["id"];


		$sql_query1 = "SELECT 
		oi.id AS sub_id,
		dl.sap_no AS dealer_sap,
		dl.name,
		pp.name AS product_name,
		geo.consignee_name,
		si.rate as product_rate,
		oi.*,
		dc.id AS vehicle_id,
		dc.name AS vehicle_name,
		IF(dc.name IS NOT NULL, 'With-Tracker', 'Without-Tracker') AS tracker_status,
		geo.consignee_name AS depot_name,
		dc.id AS uniqueId,
		CASE
			WHEN oi.status = 0 THEN 'Pending'
			WHEN oi.status = 1 THEN 'Start'
			WHEN oi.status = 2 THEN 'Complete'
		END AS current_status,
		CASE
			WHEN oi.is_shortage = 0 THEN 'Shortage Not Submit'
			WHEN oi.is_shortage = 1 THEN 'Shortage Submitted'
		END AS is_shortage,
		os.file,os.sign,os.product_json
	FROM order_info AS oi
	LEFT JOIN devicesnew AS dc ON TRIM(SUBSTRING_INDEX(dc.name, ' ', 1)) = oi.vehicle
	JOIN geofenceing AS geo ON geo.code = oi.carrier_code
	JOIN all_products AS pp ON pp.sap_no = oi.item
	JOIN dealers AS dl ON dl.sap_no = oi.customer_id
	join order_sales_invoice as si on si.order_no=oi.order_no
    left join order_shortage as os on os.order_id=oi.order_no && os.invoice_no=oi.invoice
	WHERE oi.order_no = $id
	ORDER BY oi.id DESC;";

		$result1 = $db->query($sql_query1) or die("Error :" . mysqli_error());

		$thread = [];
		while ($user = $result1->fetch_assoc()) {
			$thread[] = $user;
		}
		// echo json_encode($thread);

		$thread = utf8ize($thread);
		$json = json_encode($thread, JSON_PRETTY_PRINT);

		if ($json === false) {
			echo json_encode(["error" => "JSON encoding failed", "details" => json_last_error_msg()]);
		} else {
			echo $json;
		}

	} else {
		echo 'Wrong Key...';
	}

} else {
	echo 'Key is Required';
}
function utf8ize($data)
{
	if (is_array($data)) {
		return array_map('utf8ize', $data);
	} elseif (is_string($data)) {
		return mb_convert_encoding($data, 'UTF-8', 'UTF-8');
	}
	return $data;
}
?>