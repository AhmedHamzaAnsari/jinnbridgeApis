<?php
// Define database connection once at the top
define('DB_SERVER', 'localhost');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', '');
define('DB_DATABASE', 'jinnbridge');
$db = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_DATABASE);


error_reporting(0); // Turn off error reporting
ini_set('memory_limit', '-1'); // Remove memory limits
set_time_limit(500); // Increase time limit

// Include required files
include('class/class.phpmailer.php');
include('pdf.php');

// Variables
$today = date("Y-m-d");
$_to_today = date("Y-m-d H:i:s");
$report_time = 1;
$report = 'vehicle';
$dealer_id = $_GET['dealer_id'];
$task_id = $_GET['task_id'];
$tm_id = $_GET['tm_id'];
$row_id = $_GET['row_id'];


// Only run if dealer_id, task_id, and tm_id are provided
if ($dealer_id != "" && $task_id != "" && $tm_id != "") {
    $sql_get_cartraige_no = "SELECT re.*, dl.name AS dealer_name, us_tm.login AS tm_email, us_rm.login AS rm_email, us_grm.login AS grm_email 
    FROM reports_emailers AS re
    JOIN dealers AS dl ON dl.id = re.dealer_id
    JOIN users AS us_tm ON us_tm.id = dl.asm
    JOIN users AS us_rm ON us_rm.id = dl.tm
    JOIN users AS us_grm ON us_grm.id = dl.zm
    WHERE re.status = 0 and re.report_name='Inspection' and re.id=$row_id";

    $result_contact = mysqli_query($db, $sql_get_cartraige_no);
    if ($result_contact && mysqli_num_rows($result_contact) > 0) {
        while ($row = mysqli_fetch_assoc($result_contact)) {
            $tm_email = $row["tm_email"];
            $rm_email = $row["rm_email"];
            $grm_email = $row["grm_email"];
            $dealer_name = $row["dealer_name"];
            $dealer_id = $row["dealer_id"];

        //     // Send emails
        //    smtp_mailer($tm_email, date('Y-m-d H:i:s'), $dealer_name, $dealer_id, $task_id, $db);
        //     smtp_mailer($rm_email, date('Y-m-d H:i:s'), $dealer_name, $dealer_id, $task_id, $db);
            smtp_mailer('abasit9119@gmail.com', date('Y-m-d H:i:s'), $dealer_name, $dealer_id, $task_id, $db);
            smtp_mailer('usmanhameed@gmail.com', date('Y-m-d H:i:s'), $dealer_name, $dealer_id, $task_id, $db);
            smtp_mailer('demo@jinn.com', date('Y-m-d H:i:s'), $dealer_name, $dealer_id, $task_id, $db);

        //     smtp_mailer('faisal.inayat@jinn.com', date('Y-m-d H:i:s'), $dealer_name, $dealer_id, $task_id, $db);
        //     smtp_mailer('Amjad.khan@jinn.com', date('Y-m-d H:i:s'), $dealer_name, $dealer_id, $task_id, $db);

        // smtp_mailer('ahmedhamzaansari.99@gmail.com.com', date('Y-m-d H:i:s'), $dealer_name, $dealer_id, $task_id, $db);

        }
    } else {
        echo "No matching records found.";
    }
} else {
    echo "IO Required.";
}

// Function to get task inspection response
function get_task_inspection_response($db, $task_id, $dealer_id)
{
    $output = '';

    // Fetch all survey categories
    $query = "SELECT * FROM survey_category ORDER BY name ASC";
    $statement = mysqli_query($db, $query);
    $surveyCategories = mysqli_fetch_all($statement, MYSQLI_ASSOC);


   
    // Query to get the inspector task details
    $sql = "SELECT ii.*, us.name AS user_name, ii.created_at AS task_create_time, rr.created_at AS task_complete_time, 
    dd.name AS dealer_name  
    FROM inspector_task AS ii
    JOIN users AS us ON us.id = ii.user_id
    JOIN dealers AS dd ON dd.id = ii.dealer_id
    JOIN survey_response AS rr ON rr.inspection_id = ii.id
    WHERE ii.id = $task_id AND ii.dealer_id = $dealer_id 
    GROUP BY ii.id";

    $result = mysqli_query($db, $sql);
    $row = mysqli_fetch_assoc($result);
    // if ($row) {
        $output .= 'Report Name : Inspection Report <br/>
                    Date of Audit : ' . $row["task_create_time"] . ' <br/>
                    Complete Time : ' . $row['task_complete_time'] . ' <br/>
                    Site Name : ' . $row["dealer_name"] . ' <br/>
                    Name of Auditor(s) : ' . $row['user_name'] . '<hr>';
    // }
    // $output .= "<hr>";
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
    $output .= '<h2 style="text-align: center;padding: 3px 11px;background: #f2f2f2;">Inspection</h2>';
    $output .= count_per($db, $task_id, $dealer_id);

    // Append survey category results
    foreach ($surveyCategories as $category) {
        $cat_id = $category['id'];
        $query1 = "SELECT sr.*, sq.question, rf.file AS cancel_file 
            FROM survey_response AS sr 
            JOIN survey_category_questions AS sq ON sq.id = sr.question_id
            LEFT JOIN survey_response_files rf ON (rf.question_id = sr.question_id AND rf.inspection_id = sr.inspection_id)
            WHERE sr.category_id = $cat_id AND sr.inspection_id = $task_id AND sr.dealer_id = $dealer_id";

        $statement1 = mysqli_query($db, $query1);
        $responses = mysqli_fetch_all($statement1, MYSQLI_ASSOC);

        $output .= '<h3>' . $category["name"] . '</h3>';
        $output .= '<table><tr><th>S #</th><th>Question</th><th>Response</th><th>Comments</th></tr>';

        $counter = 1;
        foreach ($responses as $response) {
            $output .= '<tr>
                            <td>' . $counter . '</td>
                            <td>' . $response["question"] . '</td>
                            <td>' . $response["response"] . '</td>
                            <td>' . $response["comment"] . '</td>
                        </tr>';
            $counter++;
        }
        $output .= '</table>';
    }
    return $output;
}

// Count percentage function
function count_per($db, $task_id, $dealer_id)
{
    $get_orders = "SELECT count(*) total_count, response 
                   FROM survey_response 
                   WHERE inspection_id = $task_id AND dealer_id = $dealer_id 
                   GROUP BY response";
    $result_orders = mysqli_query($db, $get_orders);

    $r_yes = $r_no = $r_n_a = 0;
    while ($row_2 = mysqli_fetch_assoc($result_orders)) {
        switch ($row_2['response']) {
            case 'Yes':
                $r_yes = $row_2['total_count'];
                break;
            case 'No':
                $r_no = $row_2['total_count'];
                break;
            case 'N/A':
                $r_n_a = $row_2['total_count'];
                break;
        }
    }
    $total_sum = $r_yes + $r_no + $r_n_a;
    $percentage = ($total_sum > 0) ? ($r_yes / ($total_sum - $r_n_a)) * 100 : 0;

    return '<table class="dynamic_table" id="questions_total">
                <thead>
                    <tr>
                        <th>Total Questions</th>
                        <th>Yes</th>
                        <th>No</th>
                        <th>N/A</th>
                        <th>%</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>' . $total_sum . '</td>
                        <td>' . $r_yes . '</td>
                        <td>' . $r_no . '</td>
                        <td>' . $r_n_a . '</td>
                        <td>' . round($percentage) . '</td>
                    </tr>
                </tbody>
            </table>';
}

// Email sending function
function smtp_mailer($to, $time, $dealer_name, $dealer_id, $task_id, $db)
{
    $file_name5 = 'files/Inspection_Report_' . md5(rand()) . '.pdf';
    $html_code5 = get_task_inspection_response($db, $task_id, $dealer_id);

    $pdf5 = new Pdf();
    $pdf5->load_html($html_code5);
    $pdf5->render();
    $file5 = $pdf5->output();
    file_put_contents($file_name5, $file5);

    $mail = new PHPMailer();
    $mail->IsSMTP();
    $mail->SMTPAuth = true;
    $mail->SMTPSecure = 'tls';
    $mail->Host = "smtp.gmail.com";
    $mail->Port = 587;
    $mail->Username = "jinn.alertinfo@gmail.com";
    $mail->Password = "lbbsvqnavfl333333";
    $mail->SetFrom("jinn.alertinfo@gmail.com");
    $mail->AddAddress($to);
    $mail->IsHTML(true);
    $mail->AddAttachment($file_name5);
    $mail->Subject = $dealer_name . ' Inspection Report ' . $time;
    $mail->Body = '<h3>Dear Team,<br>Attached is the Inspection Report in PDF Format.<br>Regards,</h3>';

    if ($mail->Send()) {
        echo "Email sent successfully.";
    } else {
        echo "Failed to send email.";
    }
    unlink($file_name5);
}

?>