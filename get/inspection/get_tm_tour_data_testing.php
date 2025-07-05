<?php
// fetch.php  
include("../../config.php");

$access_key = '03201232927';
$pass = $_GET["key"];

if ($pass != '') {
    if ($pass == $access_key) {
        $months = $_GET["months"] ?? '';

        if (empty($months)) {
            echo json_encode(["error" => "No month provided"]);
            exit();
        }

        $startDate = $months . '-01';
        $endDate = $months . '-31';  // Adjust if necessary for dynamic end date

        // SQL query to fetch data
        $sql_query1 = "SELECT DISTINCT(it.dealer_id),
            us.name AS user_name,
            us.title,
            us.region,
            us.privilege,
            SUM(it.status = 0 AND DATE(it.time) >= '$startDate' AND DATE(it.time) <= '$endDate') AS sum_pending,
            SUM(it.status = 0 AND DATE(it.time) >= '$startDate' AND DATE(it.time) <= '$endDate') AS sum_Late,
            SUM(it.status = 0 AND DATE(it.time) >= '$startDate' AND DATE(it.time) <= '$endDate') AS sum_Upcoming,
            COUNT(DISTINCT CASE WHEN it.status = 1 THEN it.dealer_id END) AS sum_Complete,
            COUNT(DISTINCT it.dealer_id) AS total_visits,
            (SELECT COUNT(DISTINCT dd_inner.id) FROM dealers AS dd_inner WHERE dd_inner.asm = us.id) AS total_dealers
            FROM inspector_task AS it
            JOIN dealers AS dd ON dd.id = it.dealer_id
            JOIN users AS us ON us.id = it.user_id
            JOIN users AS usz ON usz.id = dd.zm
            JOIN users AS ust ON ust.id = dd.tm
            JOIN users AS usa ON usa.id = dd.asm
            WHERE DATE(it.time) >= '$startDate' AND DATE(it.time) <= '$endDate' AND us.privilege = 'ASM' and it.status = 1
            GROUP BY us.name";

        $result1 = $db->query($sql_query1) or die("Error :" . mysqli_error());

        // Initialize totals for the current month
        $t_sum_pending = 0;
        $t_sum_Late = 0;
        $t_sum_Upcoming = 0;
        $t_sum_Complete = 0;
        $t_total_visits = 0;
        $t_total_dealers = 0;

        // Get the current week of the selected month
        $currentWeek = getCurrentWeekOfMonth($months);

        // Get the total weeks in the selected month
        $totalWeeks = getTotalWeeksInMonth($months);

        // Initialize result array
        $thread = array();

        // Iterate through the result
        while ($user = $result1->fetch_assoc()) {
            $complete_task = $user['sum_Complete'];
            $sum_pending = $user['sum_pending'];
            $sum_Late = $user['sum_Late'];
            $sum_Upcoming = $user['sum_Upcoming'];
            $total_visits = $user['total_visits'];
            $total_dealers = $user['total_dealers'];

            // Accumulate totals
            $t_sum_pending += $sum_pending;
            $t_sum_Late += $sum_Late;
            $t_sum_Upcoming += $sum_Upcoming;
            $t_sum_Complete += $complete_task;
            $t_total_visits += $total_visits;
            $t_total_dealers += $total_dealers;

            // Calculate action visits for the current user
            // $action_visit = $total_dealers / $totalWeeks;
            // $action_visit = $action_visit * $currentWeek;

            // Calculate the tour percentage
            // $tour_percentage = ($action_visit > 0) ? ($complete_task / $action_visit * 100) : 0;
            // $tour_percentage = ($action_visit > 0) ? ($complete_task / $action_visit * 100) : 0;
            $tour_percentage = ($user['total_visits']/$user['total_dealers'])*100;


            // Add the tour percentage to the current user data
            $user['tour_percentage'] = $tour_percentage;

            // Add updated user data to the result array
            $thread[] = $user;
        }
        usort($thread, function ($a, $b) {
            return $b['tour_percentage'] <=> $a['tour_percentage'];
        });
        // Calculate total action visits for all users
        // $t_action_visit = $t_total_dealers / $totalWeeks;
        // $t_action_visit = $t_action_visit * $currentWeek;

        // Calculate the total tour percentage
        // $t_tour_percentage = ($t_action_visit > 0) ? ($t_sum_Complete / $t_action_visit * 100) : 0;
        $t_tour_percentage = ($t_total_visits/$t_total_dealers)*100;

        // Add total data to the result array
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

        // Push total data to the result array
        $thread[] = $data;

        // Output the JSON result
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

// Function to get the current week of the month based on the selected month
function getCurrentWeekOfMonth($month)
{
    // Get the first day of the selected month
    $firstDayOfMonth = new DateTime($month . '-01');  // Month passed as parameter

    // Get the current day in the selected month
    $currentDate = new DateTime();
    $currentDay = (int) $currentDate->format('j');

    // Calculate the week number (weeks start on 1)
    $weekNumber = ceil($currentDay / 7);

    return $weekNumber;
}

// Function to get the total weeks in the selected month
function getTotalWeeksInMonth($month)
{
    // Get the year and month from the selected month
    $year = substr($month, 0, 4);
    $monthNumber = substr($month, 5, 2);

    // Get the first and last day of the selected month
    $firstDayOfMonth = new DateTime("$year-$monthNumber-01");
    $lastDayOfMonth = new DateTime("$year-$monthNumber-01");
    $lastDayOfMonth->modify('last day of this month');

    // Get the day of the week for the first day
    $startDayOfWeek = $firstDayOfMonth->format('w');
    $daysInMonth = $lastDayOfMonth->format('j');

    // Calculate total weeks
    $totalWeeks = ceil(($startDayOfWeek + $daysInMonth) / 7);

    return $totalWeeks;
}

// Function to convert data to UTF-8 encoding
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
