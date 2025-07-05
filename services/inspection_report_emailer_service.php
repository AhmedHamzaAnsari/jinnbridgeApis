<?php
//fetch.php  
ini_set('max_execution_time', '0');
$url1 = $_SERVER['REQUEST_URI'];
header("Refresh: 70; URL=$url1"); // Refresh the page every 70 seconds
include("../config.php");

$access_key = '03201232927'; // Define the access key

$pass = $_GET["key"] ?? ''; // Get the 'key' parameter from the URL
if ($pass != '') {
    if ($pass == $access_key) {
        $sql_query1 = "SELECT * FROM reports_emailers WHERE status=0 AND report_name != ''";

        $result1 = $db->query($sql_query1) or die("Error: " . mysqli_error($db)); // Handle any query errors

        $count = mysqli_num_rows($result1);
        if ($count > 0) {
            while ($user = $result1->fetch_assoc()) {
                $id = $user['id'];
                $task_id = $user['task_id'];
                $dealer_id = $user['dealer_id'];
                $tm_id = $user['tm_id'];
                $report_name = $user['report_name'];

                if ($report_name == 'Inspection') {
                    $sql = "SELECT * FROM survey_response_main WHERE inspection_id='$task_id'";
                    $result = mysqli_query($db, $sql);

                    if (mysqli_num_rows($result) > 0) {
                        send_report('inspection_emailer', $task_id, $dealer_id, $tm_id, $id, $db);
                    }
                } elseif ($report_name == 'Stock Reconciliation') {
                    $sql = "SELECT * FROM dealer_stock_recon_new WHERE task_id='$task_id'";
                    $result = mysqli_query($db, $sql);

                    if (mysqli_num_rows($result) > 0) {
                        send_report('recon_emailer', $task_id, $dealer_id, $tm_id, $id, $db);
                    }
                }
            }
        } else {
            echo 'No Requests Found';
        }
    } else {
        echo 'Wrong Key...';
    }
} else {
    echo 'Key is Required';
}

echo 'Last run: ' . date('Y-m-d H:i:s');

// Function to send the report via cURL
function send_report($link, $task_id, $dealer_id, $tm_id, $row_id, $db)
{
    $curl = curl_init();
    $url = 'http://110.38.69.114:5003/jinnbridgeApis/emailer/' . $link . '.php?dealer_id=' . $dealer_id . '&task_id=' . $task_id . '&tm_id=' . $tm_id . '&row_id=' . $row_id;

    curl_setopt_array($curl, [
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'GET',
    ]);

    $response = curl_exec($curl);

    // Handle cURL error
    if (curl_errno($curl)) {
        echo 'cURL error: ' . curl_error($curl);
    } else {
        echo 'Response: ' . $response;
    }

    curl_close($curl);

    // Assuming if response is positive, we mark the report as sent
    // if ($response) {
    $date_time = date('Y-m-d H:i:s');
    $query_update = "UPDATE reports_emailers
                         SET status = 1, updated_at = '$date_time'
                         WHERE id = '$row_id'";

    if (mysqli_query($db, $query_update)) {
        echo "Report status updated successfully.";
    } else {
        echo "Failed to update report status.";
    }
    // }
}
?>