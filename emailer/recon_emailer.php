<?php

// Define constants for database connection
define('DB_SERVER', 'localhost');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', '');
define('DB_DATABASE', 'jinnbridge');
$db = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_DATABASE);



// Set PHP configurations
ini_set('memory_limit', '-1');
set_time_limit(500);
error_reporting(0);
// Include necessary files
include('class/class.phpmailer.php');
include('pdf.php');

$today = date("Y-m-d");
$_to_today = date("Y-m-d H:i:s");
echo $_to_today . ' Run time <br>';

$dealer_id = $_GET['dealer_id'] ?? '';
$task_id = $_GET['task_id'] ?? '';
$tm_id = $_GET['tm_id'] ?? '';
$row_id = $_GET['row_id'];

// Check if necessary parameters are present
if ($dealer_id !== "" && $task_id !== "" && $tm_id !== ""){

    // SQL query to get the necessary data
    $sql_get_cartraige_no = "SELECT re.*, dl.name AS dealer_name, us_tm.login AS tm_email, us_rm.login AS rm_email, us_grm.login AS grm_email 
    FROM reports_emailers AS re
    JOIN dealers AS dl ON dl.id = re.dealer_id
    JOIN users AS us_tm ON us_tm.id = dl.asm
    JOIN users AS us_rm ON us_rm.id = dl.tm
    JOIN users AS us_grm ON us_grm.id = dl.zm
    WHERE re.status = 0 and re.report_name='Stock Reconciliation' and re.id=$row_id";

    $result_contact = mysqli_query($db, $sql_get_cartraige_no);
    $count_contact = mysqli_num_rows($result_contact);

    if ($count_contact > 0) {
        while ($row = mysqli_fetch_array($result_contact)) {
            $tm_email = $row["tm_email"];
            $rm_email = $row["rm_email"];
            $grm_email = $row["grm_email"];
            $dealer_name = $row["dealer_name"];
            $dealer_id = $row["dealer_id"];

            // smtp_mailer($tm_email, date('Y-m-d H:i:s'), $dealer_name, $dealer_id, $task_id, $db);
            // smtp_mailer($rm_email, date('Y-m-d H:i:s'), $dealer_name, $dealer_id, $task_id, $db);
            smtp_mailer('abasit9119@gmail.com', date('Y-m-d H:i:s'), $dealer_name, $dealer_id, $task_id, $db);
            smtp_mailer('usmanhameed@gmail.com', date('Y-m-d H:i:s'), $dealer_name, $dealer_id, $task_id, $db);
            smtp_mailer('demo@jinn.com', date('Y-m-d H:i:s'), $dealer_name, $dealer_id, $task_id, $db);

            // smtp_mailer('faisal.inayat@jinn.com', date('Y-m-d H:i:s'), $dealer_name, $dealer_id, $task_id, $db);
            // smtp_mailer('Amjad.khan@jinn.com', date('Y-m-d H:i:s'), $dealer_name, $dealer_id, $task_id, $db);
            // smtp_mailer('ahmedhamzaansari.99@gmail.com.com', date('Y-m-d H:i:s'), $dealer_name, $dealer_id, $task_id, $db);
        }
    }
} else {
    echo "Required parameters missing.";
}

// Function to send emails with stock reconciliation report
function smtp_mailer($to, $time, $dealer_name, $dealer_id, $task_id, $db)
{
    $alert_today = date("Y-m-d");
    $file_name = 'files/Stock_Reconciliation_Report_' . md5(rand()) . '.pdf';

    // Generate the PDF report
    $html_code = get_task_inspection_response($task_id, $dealer_id, $db);
    $pdf = new Pdf();
    $pdf->load_html($html_code);
    $pdf->render();
    $file = $pdf->output();
    file_put_contents($file_name, $file);

    // Configure PHPMailer
    $mail = new PHPMailer();
    $mail->SMTPDebug = 3;
    $mail->IsSMTP();
    $mail->SMTPAuth = true;
    $mail->SMTPSecure = 'tls';
    $mail->Host = "smtp.gmail.com";
    $mail->Port = 587;
    $mail->IsHTML(true);
    $mail->Username = "jinn.alertinfo@gmail.com";
    $mail->Password = "lbbsvqnavfl333333";
    $mail->SetFrom("jinn.alertinfo@gmail.com");
    $mail->AddAddress($to);
    $mail->Subject = $dealer_name . ' Stock Reconciliation Audit Report ' . $time;
    $mail->Body = '<h3>Dear Team,<br>Following is the Stock Reconciliation Audit Report attached in PDF format for your review and action.<br>Regards,</h3>';
    $mail->AddAttachment($file_name);

    if ($mail->Send()) {
        echo 1;
    } else {
        echo 0;
    }
    unlink($file_name);
}

// Function to generate task inspection response
function get_task_inspection_response($task_id, $dealer_id, $db)
{
    // Initialize output
    $output = '';

    // Query to get basic task and user details
    $sql = "SELECT ii.*, us.name AS user_name, ii.created_at AS task_create_time, rr.created_at AS task_complete_time,
            dd.name AS dealer_name  
            FROM inspector_task as ii
            JOIN users AS us ON us.id = ii.user_id
            JOIN dealers AS dd ON dd.id = ii.dealer_id
            JOIN dealer_stock_recon_new as rr ON rr.task_id = ii.id
            WHERE ii.id = $task_id AND ii.dealer_id = $dealer_id
            GROUP BY ii.id";

    $result = mysqli_query($db, $sql);

    if ($row = mysqli_fetch_array($result)) {
        $output .= 'Report Name : Stock Reconciliation <br/>
                    Date of Audit : ' . $row["task_create_time"] . ' <br/>
                    Complete Time : ' . $row['task_complete_time'] . ' <br/>
                    Site Name : ' . $row["dealer_name"] . ' <br/>
                    Name of Auditor(s) : ' . $row['user_name'] . '<hr>';
    }

    // Query to get detailed product data
    $sql_query1 = "SELECT rn.*, dp.name as product_name, dd.name as dealer_name 
                   FROM dealer_stock_recon_new as rn
                   JOIN dealers_products as dp ON dp.id = rn.product_id
                   JOIN dealers AS dd ON dd.id = rn.dealer_id
                   WHERE rn.task_id = $task_id AND rn.dealer_id = $dealer_id
                   GROUP BY rn.product_id";

    $result1 = mysqli_query($db, $sql_query1);

    while ($taskDetails = mysqli_fetch_assoc($result1)) {
        $total_days = $taskDetails["total_days"];
        $last_recon_date = $taskDetails["last_recon_date"];
        $sum_of_opening = $taskDetails["sum_of_opening"];
        $sum_of_closing = $taskDetails["sum_of_closing"];
        $total_sales = $taskDetails["total_sales"];
        $total_recipt = $taskDetails["total_recipt"];
        $book_value = $taskDetails["book_value"];
        $variance = $taskDetails["variance"];
        $remark = $taskDetails["remark"];
        $shortage_claim = $taskDetails["shortage_claim"];
        $variance_of_sales = $taskDetails["variance_of_sales"];
        $average_daily_sales = $taskDetails["average_daily_sales"];
        $created_at = $taskDetails["created_at"];
        $product_name = $taskDetails["product_name"];
        $dealer_name = $taskDetails["dealer_name"];
        $tanks = $taskDetails["tanks"];
        $nozzle = $taskDetails["nozzel"];
        $is_totalizer = $taskDetails["is_totalizer_data"];
        $variance_of_sales = $taskDetails["variance_of_sales"];
        $average_daily_sales = $taskDetails["average_daily_sales"];

        // Building the report content
        $output .= '<p style="background: #e3dede;padding: 5px 7px;text-align: center;">Stock Reconciliation ' . $product_name . '</p>
                    Product : ' . $product_name . ' <br/>
                    Total Days : ' . $total_days . ' <br/>
                    From : ' . $last_recon_date . ' <br/>
                    To  : ' . $created_at . ' <br/>';

        // Tank opening and closing details
        $output .= "<hr>";
        $output .= '
                    <style>
                        table, th, td {
                            border: 1px solid black;
                            border-collapse: collapse;
                        }
                        th, td {
                            padding:10px;
                        }
                        th {
                            border: 1px solid;
                            padding: 8px;
                            text-align: left;
                            background-color: #f2f2f2;
                        }
                    </style>';
        $output .= '
                <p style="background: #e3dede;padding: 5px 7px;text-align: center;">
                Opening and Closing Dips</p>
            <table class="dynamic_table" style="width:100%">
                <tr>
                    <th></th>
                    <th colspan="2" style="text-align: center;">Opening</th>
                    <th></th>
                    <th colspan="2" style="text-align: center;">Closing</th>
                </tr>
                <tr>
                    <th>Tanks</th>
                    <th>Dip mm</th>
                    <th>Qty in Ltrs</th>
                    <td></td>
                    <th>Dip mm</th>
                    <th>Qty in Ltrs</th>
                </tr>';

        $data_main = json_decode($tanks, true);

        if (is_array($data_main)) {
            foreach ($data_main as $item) {
                $output .= '<tr>
                            <th>' . $item["name"] . '</th>
                            <td>' . format_amount($item["opening_dip"]) . '</td>
                            <td>' . format_amount($item["opening"]) . '</td>
                            <td></td>
                            <td>' . format_amount($item["closing_dip"]) . '</td>
                            <td>' . format_amount($item["closing"]) . '</td>
                        </tr>';
            }
        } else {
            echo "Failed to decode JSON.";
        }

        $output .= '<tr>
                    <th colspan="2">Opening Stock</th>
                    <td>' . format_amount($sum_of_opening) . '</td>
                    <th colspan="2">Physical Stock</th>
                    <td>' . format_amount($sum_of_closing) . '</td>
                </tr>';
        $output .= '</table>';

        // Opening and closing meter readings
        $output .= '<p style="background: #e3dede;padding: 5px 7px;text-align: center;">Opening and Closing Meter Readings</p>
                    <table class="dynamic_table" style="width:100%">
                        <tr>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th>Opening (A)</th>
                            <th>Closing (B)</th>
                            <th>Sales (B-A)</th>
                        </tr>';

        $data_array = json_decode($nozzle, true);

        foreach ($data_array as $item) {
            $output .= '<tr>
                            <th>' . $item["dispenser_name"] . ' - ' . $item["name"] . '</th>
                            <td></td>
                            <td></td>
                            <td>' . format_amount($item["opening"]) . '</td>
                            <td>' . format_amount($item["closing"]) . '</td>
                            <td>' . format_amount(floatval($item["closing"]) - floatval($item["opening"])) . '</td>
                        </tr>';
        }

        $data_is_totalizer_data = json_decode($is_totalizer, true);

        foreach ($data_is_totalizer_data as $item) {
            $output .= '<tr>
                            <th>' . $item["dispenser_name"] . ' - ' . $item["name"] . ' (Change Totalizer)</th>
                            <td></td>
                            <td></td>
                            <td>' . format_amount($item["opening"]) . '</td>
                            <td>' . format_amount($item["closing"]) . '</td>
                            <td>' . format_amount(floatval($item["closing"]) - floatval($item["opening"])) . '</td>
                        </tr>';
        }

        $output .= '<tr>
                    <td></td>
                    <td></td>
                    <td></td>
                    <th colspan="2">Total Sales for the Period</th>
                    <td>' . format_amount($total_sales) . '</td>
                </tr>';
        $output .= '</table>';

        // Final Receipts Summary
        // jo final_recipt hai wo  total_recipt hai 
        $output .= '<div class="col-md-12">
        <h6 style="text-align: center;padding: 3px 11px;background: #f2f2f2;">
        </h6>
        <table class="dynamic_table" style="width:100%">
           
            <tr>
                <th>Final Reciepts</th>
                <td class="">' . $taskDetails["total_recipt"] . ' (IN LTRS)</td>

            </tr>
        </table>
    </div>';

        // Final Analysis
        $output .= '<div class="col-md-12">
        <p style="background: #e3dede;padding: 5px 7px;text-align: center;">Final Analysis
        </p>
    </div><div class="col-md-12">
    <table class="dynamic_table" style="width:100%">
        <tr>
            <th>(C) Opening Stock</th>
            <th>(D) Final Receipts</th>
            <th>(E) Sales</th>
            <th>(C+D-E) Equals to</th>
            <th>Book Stock</th>
        </tr>
        <tr>
            <td>' . format_amount($sum_of_opening) . '</td>
            <td>' . format_amount($total_recipt) . '</td>
            <td>' . format_amount($total_sales) . '</td>
            <td style="text-align: center;">=</td>
            <td>' . format_amount($book_value) . '</td>
        </tr>
    </table>
</div>';
        $output .= ' <div class="col-md-12">
<table class="dynamic_table" style="width:100%">
    <tr>
        <th>(F) Physical Stock</th>
        <th>(G) Book Stock</th>
        <th>(F-G) Equals to</th>
        <th>Variance</th>
    </tr>
    <tr>
        <td>' . format_amount($sum_of_closing) . '</td>
        <td>' . format_amount($book_value) . '</td>
        <td style="text-align: center;">=</td>
        <td>' . format_amount($variance) . '</td>
    </tr>
</table>
</div>';

        $output .= '
<div class="col-md-12">
<p style="background: #e3dede;padding: 5px 7px;text-align: center;">
</p>
<table class="dynamic_table" style="width:100%">
    <tr>
        <th class="w-50">Shortage Claim for the period (TLs short received by in Ltrs)</th>
        <td class="w-50" class="">' . format_amount($shortage_claim) . '</td>
    </tr>
</table>
</div>
<div class="col-md-12">
<h6 style="background: #e3dede;padding: 5px 7px;text-align: center;">
</h6>
<table class="dynamic_table" style="width:100%">
    <tr>
        <th class="w-50">Net Gain or Loss</th>
        <td class="w-50" class="">' . format_amount(floatval($shortage_claim) + floatval($variance)) . '</td>
    </tr>
</table>
</div>';
        $output .= '<div class="col-md-12">
<p style="background: #e3dede;padding: 5px 7px;text-align: center;">
</p>
<table class="dynamic_table" style="width:100%">
    <tr>
        <th class="w-50">Variance as % of Sales (for the period.)</th>
        <td class="w-50" class="">' . format_amount($variance_of_sales) . '</td>
    </tr>
</table>
</div>
<div class="col-md-12">
<p style="background: #e3dede;padding: 5px 7px;text-align: center;">
</p>
<table class="dynamic_table" style="width:100%">
    <tr>
        <th class="w-50">Average Daily sales</th>
        <td class="w-50" class="">' . format_amount($average_daily_sales) . '</td>
    </tr>
</table>
</div>
<div class="col-md-12">
<p style="background: #e3dede;padding: 5px 7px;text-align: center;">
</p>
<table class="dynamic_table" style="width:100%">
    <tr>
        <th class="w-50">Remarks</th>
        <td class="w-50" class="">' . $remark . '</td>
    </tr>
</table>
</div>';
    }

    return $output;
}


// Helper function to format amounts
function format_amount($amount)
{
    return is_numeric($amount) ? number_format($amount, 2) : $amount;
}

?>