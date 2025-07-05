<?php

$data_order = $_GET['data'];
$type = $_GET['type'];
$dealer_sap = $_GET['dealer_sap'];



$dataArray = json_decode($data_order, true);

// Check if the decoding was successful
if (is_array($dataArray)) {
    // Iterate through the array using a foreach loop
    $data = array();

    // Loop to generate data
    $itr = 0;
    for ($i = 0; $i <= count($dataArray) - 1; $i++) {
        // $line_item = sprintf('%06d', $i * 10); // Generating Line_Item with leading zeros
        // $material = sprintf('%019d', $i * 2); // Generating Material with leading zeros
        // print_r($dataArray[$i]);
        $itr += 10;
        $qty = $dataArray[$i]['quantity'];
        if ($qty > 0) {
            $item = array(
                "Line_Item" => '0000' . $itr . '',
                "Material" => $dataArray[$i]['product_sap'],
                "Quantity" => $dataArray[$i]['quantity']
            );

            // Pushing the item into the data array
            $data[] = $item;

        }
        // Creating the item array
    }

    // Encode the data to JSON format
    $jsonString = json_encode($data);

    // Output the JSON string
    // echo $jsonString;



    // $curl = curl_init();

    // curl_setopt_array(
    //     $curl,
    //     array(
    //         CURLOPT_URL => 'https://103.111.160.120:44300/sap/opu/odata/sap/ZP2P_TRACK_PROJ_SRV/InitialSet7(CustomerId=\'' . $dealer_sap . '\',DeliveryType=\'' . $type . '\',LvData=\'' . $jsonString . '\')',
    //         CURLOPT_RETURNTRANSFER => true,
    //         CURLOPT_ENCODING => '',
    //         CURLOPT_MAXREDIRS => 10,
    //         CURLOPT_TIMEOUT => 0,
    //         CURLOPT_FOLLOWLOCATION => true,
    //         CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    //         curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false),
    //         curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false),
    //         CURLOPT_CUSTOMREQUEST => 'GET',
    //         CURLOPT_HTTPHEADER => array(
    //             'Authorization: Basic dGFsaGE6dG1jMTIzNDU2Nw==',
    //             'Cookie: SAP_SESSIONID_DEV_200=nEtW7ZikLH3rL9qCaSXR8WI0pH3P-xHuoAAAUFa6fv0%3d; sap-usercontext=sap-client=200'
    //         ),
    //     )
    // );

    // $response = curl_exec($curl);

    // curl_close($curl);

   

    // // Echo the XML response
    // echo $response;



    $curl = curl_init();

    curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://103.111.160.120:44302/sap/opu/odata/sap/ZP2P_TRACK_PROJ_SRV/InitialSet7(CustomerId=\'' . $dealer_sap . '\',DeliveryType=\'' . $type . '\',LvData=\'' . $jsonString . '\')',
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
            'Cookie: SAP_SESSIONID_PRD_100=B4HxeRJeZbtRuJOr4iSeqX8noHfQrhHuu0YAUFa6A50%3d; sap-usercontext=sap-client=100'
        ),
    )
    );

    $response = curl_exec($curl);

    curl_close($curl);
    header('Content-Type: application/xml');
    echo $response;



    // foreach ($dataArray as $item) {
    //     // echo "Product ID: " . $item['p_id'] . "<br>";
    //     // echo "Quantity: " . $item['quantity'] . "<br>";
    //     // echo "Indent Price: " . $item['indent_price'] . "<br>";
    //     // echo "Product Name: " . $item['product_name'] . "<br>";
    //     // echo "Amount: " . $item['amount'] . "<br>";
    //     // echo "<hr>";

    //     $quantity = $item['quantity'];
    //     if ($quantity > 0) {

    //         $product_id = $item['p_id'];
    //         $indent_price = $item['indent_price'];
    //         $product_name = $item['product_name'];
    //         $amount = $item['amount'];

    //         $dealer_sap = $item['dealer_sap'];
    //         $dealer_order_type = $item['dealer_order_type'];
    //         $product_sap = $item['product_sap'];

    //         // $curl = curl_init();

    //         // curl_setopt_array(
    //         //     $curl,
    //         //     array(
    //         //         CURLOPT_URL => 'https://103.111.160.120:44300/sap/opu/odata/sap/ZP2P_TRACK_PROJ_SRV/InitialSet4(CustomerId=\'' . $dealer_sap . '\',OrderType=\'' . $dealer_order_type . '\',LineItem=\'000010\',Material=\'' . $product_sap . '\',Quantity=' . $amount . ')',
    //         //         CURLOPT_RETURNTRANSFER => true,
    //         //         CURLOPT_ENCODING => '',
    //         //         CURLOPT_MAXREDIRS => 10,
    //         //         CURLOPT_TIMEOUT => 0,
    //         //         CURLOPT_FOLLOWLOCATION => true,
    //         //         CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    //         //         curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false),
    //         //         curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false),
    //         //         CURLOPT_CUSTOMREQUEST => 'GET',
    //         //         CURLOPT_HTTPHEADER => array(
    //         //             'Authorization: Basic dG1jX2FzaW06dG1jQDEyMw==',
    //         //             'Cookie: SAP_SESSIONID_DEV_200=BuAjI9hdAhLBCEmDfya0cjQONsHMwxHut-IAUFa6fv0%3d; sap-usercontext=sap-client=200'
    //         //         ),
    //         //     )
    //         // );

    //         // $response = curl_exec($curl);

    //         // curl_close($curl);

    //         // -----------------------------------new----------------------------------------

    //         // $curl = curl_init();

    //         // curl_setopt_array($curl, array(
    //         //     CURLOPT_URL => 'https://103.111.160.120:44302/sap/opu/odata/sap/ZP2P_TRACK_PROJ_SRV/InitialSet4(CustomerId=\'' . $dealer_sap . '\',OrderType=\''.$dealer_order_type.'\',LineItem=\'000010\',Material=\''.$product_sap.'\',Quantity='.$amount.')',
    //         //     CURLOPT_RETURNTRANSFER => true,
    //         //     CURLOPT_ENCODING => '',
    //         //     CURLOPT_MAXREDIRS => 10,
    //         //     CURLOPT_TIMEOUT => 0,
    //         //     CURLOPT_FOLLOWLOCATION => true,
    //         //     CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    //         //     curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false),
    //         //     curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false),
    //         //     CURLOPT_CUSTOMREQUEST => 'GET',
    //         //     CURLOPT_HTTPHEADER => array(    
    //         //         'Authorization: Basic U0FQLkFQSTpTYXBzYXBAMTI='
    //         //     ),
    //         // )
    //         // );

    //         // $response = curl_exec($curl);

    //         // curl_close($curl);
    //         // echo $response;

    //         // header('Content-Type: application/xml');

    //         // // Echo the XML response
    //         // echo $response;
    //     }
    // }
}




?>