<?php
//fetch.php  
include("../../config.php");


$access_key = '03201232927';

$pass = $_GET["key"];
$pre=$_GET["pre"];
$user_id=$_GET["user_id"];
if ($pass != '') {
    if ($pass == $access_key) {
        // $sql_query1 = "SELECT *,us.name as user_name,dd.name as dealer_name,
        // CASE
        //         WHEN it.status = 0 THEN 'Pending'
        //         WHEN it.status = 1 THEN 'Complete'
        //         WHEN it.status = 2 THEN 'Cancel'
        //         END AS current_status
        //  FROM inspector_task as it
        //         join dealers as dd on dd.id=it.dealer_id
        //         join users as us on us.id=it.user_id;";
        $co = '';

        if($pre=='TM'){
            $co = 'AND dd.tm='.$user_id.'';
        }else{
            $co = '';
        }

        $sql_query1 = "SELECT
        distinct(it.dealer_id),
        us.name AS user_name,
        us.title,
        us.region,
        us.privilege,
        SUM(it.status = 0 AND DATE(it.time) = CURDATE() OR it.status = 0 AND DATE(it.time) > CURDATE()) AS sum_pending,
        SUM(it.status = 0 AND DATE(it.time) < CURDATE()) AS sum_Late,
        SUM(it.status = 0 AND DATE(it.time) > CURDATE()) AS sum_Upcoming,
        COUNT(DISTINCT CASE WHEN it.status = 1 THEN it.dealer_id END) AS sum_Complete, -- Distinct dealer_id for status = 1
        COUNT(DISTINCT it.dealer_id ) AS total_visits, -- Distinct dealer_id for status = 1
        (
            SELECT COUNT(DISTINCT dd_inner.id) 
            FROM dealers AS dd_inner
            WHERE dd_inner.asm = us.id
        ) AS total_dealers -- Subquery to count unique dealers associated with the ASM
        FROM 
        inspector_task AS it
        JOIN 
        dealers AS dd ON dd.id = it.dealer_id
        JOIN 
        users AS us ON us.id = it.user_id
        JOIN 
        users AS usz ON usz.id = dd.zm
        JOIN 
        users AS ust ON ust.id = dd.tm
        JOIN 
        users AS usa ON usa.id = dd.asm
        WHERE 
        DATE(it.time) >= DATE_FORMAT(CURDATE(), '%Y-%m-01') -- Start of the current month
        AND DATE(it.time) <= LAST_DAY(CURDATE())            -- End of the current month
        AND us.privilege = 'ASM' $co                         -- Filter for ASMs only
        GROUP BY 
        us.name";

        $result1 = $db->query($sql_query1) or die("Error :" . mysqli_error());

        $thread = array();

        $t_sum_pending = 0;
        $t_sum_Late = 0;
        $t_sum_Upcoming = 0;
        $t_sum_Complete = 0;
        $t_total_visits = 0;
        $t_total_dealers = 0;
        $currentWeek = getCurrentWeekOfMonth();
        // Avoid division by zero
        $totalWeeks = getTotalWeeksInMonth();
        while ($user = $result1->fetch_assoc()) {
            $complete_task = $user['sum_Complete'];
            // $total_dealers = $user['total_dealers'];
            $sum_pending = $user['sum_pending'];
            $sum_Late = $user['sum_Late'];
            $sum_Upcoming = $user['sum_Upcoming'];
            $sum_Complete = $user['sum_Complete'];
            $total_visits = $user['total_visits'];
            $total_dealers = $user['total_dealers'];


            $t_sum_pending += $sum_pending;
            $t_sum_Late += $sum_Late;
            $t_sum_Upcoming += $sum_Upcoming;
            $t_sum_Complete += $sum_Complete;
            $t_total_visits += $total_visits;
            $t_total_dealers += $total_dealers;



            $action_visit = $total_dealers / $totalWeeks;
            $action_visit = $action_visit * $currentWeek;

            $tour_percentage = ($action_visit > 0) ? ($complete_task / $action_visit * 100) : 0;

            // Add the tour percentage to the current user data
            $user['tour_percentage'] = $tour_percentage;

            // Push the updated user data into the thread array
            $thread[] = $user;
        }
        usort($thread, function ($a, $b) {
            return $b['tour_percentage'] <=> $a['tour_percentage'];
        });
        

        $t_action_visit = $t_total_dealers / $totalWeeks;
        $t_action_visit = $t_action_visit * $currentWeek;

        $t_tour_percentage = ($t_action_visit > 0) ? ($t_sum_Complete / $t_action_visit * 100) : 0;

        $data = [
            "dealer_id" => "---",
            "user_name" => "Total",
            "privilege" => "---",
            "title" => "---",
            "region" => "---",
            "sum_pending" => $t_sum_pending,
            "sum_Late" => $t_sum_Late,
            "sum_Upcoming" => $t_sum_Upcoming,
            "sum_Complete" => $t_sum_Complete,
            "total_visits" => $t_total_visits,
            "total_dealers" => $t_total_dealers,
            "tour_percentage" => $t_tour_percentage
        ];
        $thread[] = $data;
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

function getCurrentWeekOfMonth()
{
    // Get the current date
    $currentDate = new DateTime();

    // Find the first day of the current month
    $firstDayOfMonth = new DateTime($currentDate->format('Y-m-01'));

    // Get the current day of the month
    $currentDay = (int) $currentDate->format('j');

    // Calculate the week number (weeks start on 1)
    $weekNumber = ceil($currentDay / 7);

    return $weekNumber;
}

function getTotalWeeksInMonth()
{
    // Get the current year and month
    $year = date('Y'); // Current year
    $month = date('m'); // Current month

    // Get the first and last day of the month
    $firstDayOfMonth = new DateTime("$year-$month-01");
    $lastDayOfMonth = new DateTime("$year-$month-01");
    $lastDayOfMonth->modify('last day of this month');

    // Get the day of the week for the first day
    $startDayOfWeek = $firstDayOfMonth->format('w'); // 0 (Sunday) to 6 (Saturday)
    $daysInMonth = $lastDayOfMonth->format('j'); // Total number of days in the month

    // Calculate total weeks
    $totalWeeks = ceil(($startDayOfWeek + $daysInMonth) / 7);

    return $totalWeeks;
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
// Example Usage:

// echo "The current week of the month is: Week $currentWeek\n";

?>