<?php
//fetch.php  
include("../config.php");


$access_key = '03201232927';

$pass = $_GET["key"];
$pre = $_GET["pre"];
$id = $_GET["user_id"];
if ($pass != '') {
    if ($pass == $access_key) {

        function last($t)
        {
            $dateTimeString = $t;

            // Convert the string to a DateTime object
            $dateTime = new DateTime($dateTimeString);

            // Set the time to the end of the day (23:59:59)
            $dateTime->setTime(23, 59, 59);

            // Format and display the result
            $lastMomentOfDay = $dateTime->format('Y-m-d H:i:s');
            return $lastMomentOfDay;
        }

        if ($pre == 'ZM') {

            $sqlEvents = "SELECT it.*,tr.*,us.name as inspector_name,dl.name as dealer_name,it.time as start_date,it.time as end_date,tr.description as response,
            CASE
                    WHEN it.status = 0 THEN 'Pending'
                    WHEN it.status = 1 THEN 'Complete'
                    WHEN it.status = 2 THEN 'Cancel'
                    END AS current_status FROM inspector_task as it 
            left join inspector_task_response as tr on tr.task_id=it.id
            left join dealers as dl on dl.id=it.dealer_id
            left join users as us on us.id=it.user_id where dl.zm='$id' group by it.id";
        } elseif ($pre == 'TM') {

            $sqlEvents = "SELECT it.*,tr.*,us.name as inspector_name,dl.name as dealer_name,it.time as start_date,it.time as end_date,tr.description as response,
            CASE
                    WHEN it.status = 0 THEN 'Pending'
                    WHEN it.status = 1 THEN 'Complete'
                    WHEN it.status = 2 THEN 'Cancel'
                    END AS current_status FROM inspector_task as it 
            left join inspector_task_response as tr on tr.task_id=it.id
            left join dealers as dl on dl.id=it.dealer_id
            left join users as us on us.id=it.user_id where dl.tm='$id' group by it.id;";
        } elseif ($pre == 'ASM') {
            $sqlEvents = "SELECT it.*,tr.*,us.name as inspector_name,dl.name as dealer_name,it.time as start_date,it.time as end_date,tr.description as response,
            CASE
                    WHEN it.status = 0 THEN 'Pending'
                    WHEN it.status = 1 THEN 'Complete'
                    WHEN it.status = 2 THEN 'Cancel'
                    END AS current_status FROM inspector_task as it 
            left join inspector_task_response as tr on tr.task_id=it.id
            left join dealers as dl on dl.id=it.dealer_id
            left join users as us on us.id=it.user_id where dl.asm='$id' group by it.id;";

        } else {

            $sqlEvents = "SELECT it.*,tr.*,us.name as inspector_name,dl.name as dealer_name,it.time as start_date,it.time as end_date,tr.description as response,
            CASE
                    WHEN it.status = 0 THEN 'Pending'
                    WHEN it.status = 1 THEN 'Complete'
                    WHEN it.status = 2 THEN 'Cancel'
                    END AS current_status FROM inspector_task as it 
            left join inspector_task_response as tr on tr.task_id=it.id
            left join dealers as dl on dl.id=it.dealer_id
            left join users as us on us.id=it.user_id group by it.id";
        }



        // echo $sqlEvents;

        // $sqlEvents = "SELECT id, title, start_date, end_date FROM events LIMIT 20";


        $resultset = mysqli_query($db, $sqlEvents) or die("database error:" . mysqli_error($db));
        $calendar = array();
        while ($rows = mysqli_fetch_assoc($resultset)) {
            // convert  date to milliseconds
            $start = strtotime($rows['start_date']) * 1000;
            $end = strtotime(last($rows['start_date'])) * 1000;
            $title = 'Dealer : ' . $rows['dealer_name'] . ' | Inspector : ' . $rows['inspector_name'] . ' | Status : ' . $rows['current_status'] . '| Response : ' . $rows['response'];
            $calendar[] = array(


                'id' => $rows['id'],
                'title' => $title,
                'url' => "#",
                "class" => 'event-important',
                'start' => "$start",
                'end' => "$end"
            );
        }
        $calendarData = array(
            "success" => 1,
            "result" => $calendar
        );
        // echo json_encode($calendarData);

        $calendarData = utf8ize($calendarData);
        $json = json_encode($calendarData, JSON_PRETTY_PRINT);

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