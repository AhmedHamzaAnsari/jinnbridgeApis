<?php
// fetch.php  
include("../../../config.php");
ini_set('max_execution_time', '0');
$url1 = $_SERVER['REQUEST_URI'];
header("Refresh: 30; URL=$url1");
set_time_limit(5000);

$access_key = '03201232927';

$pass = $_GET["key"];
$date = 'Dealers Product Price : ' . date('Y-m-d H:i:s');
echo $date . '<br>';
$user_id = 1;

if ($pass != '') {
    if ($pass == $access_key) {
        $sql_query1 = "SELECT * FROM dealers WHERE privilege='Dealer' AND indent_price=1 and id!=11;";
        $result1 = $db->query($sql_query1) or die("Error: " . $db->error);

        while ($user = $result1->fetch_assoc()) {
            $dealers_id = $user['id'];
            $sap = $user['sap_no'];
            $acount = $user['acount'];

            // Call function with corrected parameter
            get_dealers_product($db, $sap, $dealers_id, $user_id);
        }

    } else {
        echo 'Wrong Key...';
    }

} else {
    echo 'Key is Required';
}

function get_dealers_product($db, $sap_no, $d_id, $user_id)
{
    $sql_query1 = "SELECT dp.*,dl.sap_no FROM dealers_products as dp
    join dealers as dl on dl.id=dp.dealer_id
    where dp.dealer_id='$d_id'";
    $result1 = $db->query($sql_query1);

    if (!$result1) {
        die("Error: " . $db->error);
    }

    while ($user = $result1->fetch_assoc()) {
        $product = $user['name'];
        $rows_id = $user['id'];
        $sap_no = $user['sap_no'];

        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => 'http://localhost:8080/api_server/get/get_dealers_product.php?sap_no=' . urlencode($sap_no) . '&product=' . urlencode($product),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
        ));

        $response = curl_exec($curl);

        if (curl_errno($curl)) {
            echo 'cURL error: ' . curl_error($curl);
            curl_close($curl);
            continue;
        }

        curl_close($curl);

        $response_data = json_decode($response, true);

        if (!is_array($response_data)) {
            echo "Error: Invalid response data.<br>";
            // Continue to the next iteration if inside a loop
            continue;
        }
        
        // Check for "No data found" message
        if (isset($response_data['message']) && $response_data['message'] === 'No data found') {
            echo "No data found, Skipping site . ".$sap_no."<br>";
            // Break the loop
            break;
        }

        $retail_price = 0;
        $indent_price = 0;
        $indent_price_with_freight = 0;
        $secondary_freight_details = null;

        $indent_price_with_tax = 0;
        $indent_price_with_freight_with_tax = 0;

        foreach ($response_data as $item) {
            $unit_price = floatval($item['Unit Price']);
            $tax_per = floatval($item['VAT Rate']);
            $U_RetailPrice = floatval($item['U_RetailPrice']);
            $U_NegativeQty = $item['U_NegativeQty'];
            $U_TaxOnly = $item['U_TaxOnly'];
            $unit_price = ($U_NegativeQty == 'Y') ? -abs($unit_price) : $unit_price;


            if ($U_TaxOnly != 'Y') {
                if ($item['ItemName'] !== $product . " BOM - Secondary Freight") {
                    $indent_price += $unit_price;

                    $indent_price_with_tax += $unit_price + ($unit_price * $tax_per / 100);

                }

                $retail_price += $U_RetailPrice;
                $indent_price_with_freight += $unit_price;

                $indent_price_with_freight_with_tax += $unit_price + ($unit_price * $tax_per / 100);


                if ($item['ItemName'] === $product . " BOM - Secondary Freight") {
                    $secondary_freight_details = [
                        'Unit Price' => $unit_price,
                        'retail_price' => $retail_price,
                        'ValidFrom' => $item['ValidFrom'],
                        'ValidTo' => $item['ValidTo']
                    ];
                }

            }

        }

        $Freight_value = $secondary_freight_details['Unit Price'] ?? 0;
        $ValidFrom = $secondary_freight_details['ValidFrom'] ?? '';
        $ValidTo = $secondary_freight_details['ValidTo'] ?? '';
        $retail_price = $secondary_freight_details['retail_price'] ?? '';

        $assign_product = "UPDATE `dealers_products`
                            SET 
                            `from` = '$ValidFrom',
                            `to` = '$ValidTo',
                            `indent_price` = '$indent_price',
                            `nozel_price` = '$retail_price',
                            `indent_with_freigth` = '$indent_price_with_freight',
                            `update_time` = NOW(),
                            `freight_value`= '$Freight_value',
                            `indent_price_with_tax`= '$indent_price_with_tax',
                            `indent_with_freigth_with_tax`= '$indent_price_with_freight_with_tax',
                            `description` = 'Using Service'
                            WHERE `id` = $rows_id";

        if ($db->query($assign_product)) {
            $dp_id = $rows_id;

            $backlog = "INSERT INTO dealer_nozel_price_log
                        (`dealer_id`, `product_id`, `indent_price`, `nozel_price`, `freight_value`, `from`, `to`, `description`, `created_at`, `created_by`)
                        VALUES
                        ('$d_id', '$dp_id', '$indent_price', '$retail_price', '$Freight_value', '$ValidFrom', '$ValidTo', '', NOW(), '$user_id')";

            if ($db->query($backlog)) {
                create_bom_data($db, $response_data, $dp_id, $product, $d_id, $user_id, $rows_id);
            } else {
                echo "Error: " . $db->error . "<br>" . $backlog;
            }
        } else {
            echo "Error: " . $db->error . "<br>" . $assign_product;
        }
    }
}

function create_bom_data($db, $data, $dp_id, $product, $dealer_id, $user_id, $rows_id)
{
    $first_delete = "DELETE FROM dealers_products_bom_list WHERE main_id=$rows_id;";

    if ($db->query($first_delete)) {
        foreach ($data as $item) {
            $ItemCode = $item['ItemCode'];
            $ItemCode2 = $item['ItemCode2'];
            $ITEM_TYPE = $item['ITEM TYPE'];
            $ItemName = $item['ItemName'];
            $CardCode = $item['CardCode'];
            $CardName = $item['CardName'];
            $GroupCode = $item['GroupCode'];
            $GroupName = $item['GroupName'];
            $Unit_Price = $item['Unit Price'];
            $ValidFrom = $item['ValidFrom'];
            $ValidTo = $item['ValidTo'];
            $VatGourpSa = $item['VatGourpSa'];
            $VAT_Rate = $item['VAT Rate'];


            $U_NegativeQty = $item['U_NegativeQty'];
            $U_TaxOnly = $item['U_TaxOnly'];
            $Active = $item['Active'];

            $bom_list = "INSERT INTO `dealers_products_bom_list`
                        (`main_id`, `product_sap`, `line_item_sap`, `item_type`, `product_name`, `line_item_name`, `dealer_id`, `dealer_name`, `group_code`, `group_name`, `unit_price`, `from`, `to`, `VatGourpSa`, `vat_rate`, `created_at`, `created_by`, `active`, `U_NegativeQty`, `U_TaxOnly`)
                        VALUES
                        ('$rows_id', '', '$ItemCode', '$ITEM_TYPE', '$product', '$ItemName', '$dealer_id', '$CardName', '$GroupCode', '$GroupName', '$Unit_Price', '$ValidFrom', '$ValidTo', '$VatGourpSa', '$VAT_Rate', NOW(), '$user_id', '$Active', '$U_NegativeQty', '$U_TaxOnly')";

            if ($db->query($bom_list)) {
                $bom_list_log = "INSERT INTO `dealers_products_bom_list_log`
                                (`main_id`, `product_sap`, `line_item_sap`, `item_type`, `product_name`, `line_item_name`, `dealer_id`, `dealer_name`, `group_code`, `group_name`, `unit_price`, `from`, `to`, `VatGourpSa`, `vat_rate`, `created_at`, `created_by`, `active`, `U_NegativeQty`, `U_TaxOnly`)
                                VALUES
                                ('$dp_id', '', '$ItemCode', '$ITEM_TYPE', '$product', '$ItemName', '$dealer_id', '$CardName', '$GroupCode', '$GroupName', '$Unit_Price', '$ValidFrom', '$ValidTo', '$VatGourpSa', '$VAT_Rate', NOW(), '$user_id', '$Active', '$U_NegativeQty', '$U_TaxOnly')";

                if (!$db->query($bom_list_log)) {
                    echo "Error: " . $db->error . "<br>" . $bom_list_log;
                }
            } else {
                echo "Error: " . $db->error . "<br>" . $bom_list;
            }
        }
    } else {
        echo "Error: " . $db->error . "<br>" . $first_delete;
    }
}

echo date('Y-m-d H:i:s');
?>