<?php 
include("../../config.php");
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = mysqli_real_escape_string($db, $_POST["user_id"]);
    $response = $_POST["response"];
    $dealer_id = mysqli_real_escape_string($db, $_POST["dealer_id"]);
    $inspection_id = mysqli_real_escape_string($db, $_POST["inspection_id"]);
    $response = str_replace("'", '', $response); // Optional: sanitize single quotes

    $datetime = date('Y-m-d H:i:s');

    $query_main = "INSERT INTO `survey_response_eng_new_main`
        (`dealer_id`, `inspection_id`, `data`, `created_at`, `created_by`)
        VALUES ('$dealer_id', '$inspection_id', '$response', '$datetime', '$user_id')";

    if (mysqli_query($db, $query_main)) {
        $main_id = mysqli_insert_id($db);
        $data = json_decode($response, true);

        if ($data === null && json_last_error() !== JSON_ERROR_NONE) {
            echo "Error decoding JSON: " . json_last_error_msg();
            exit;
        }

        foreach ($data as $section) {
            $category_title = mysqli_real_escape_string($db, $section['title']);
            $fields = $section['fields'];

            foreach ($fields as $field) {
                $question = mysqli_real_escape_string($db, $field['question']);
                $answer = mysqli_real_escape_string($db, $field['answer']);
                $comment = mysqli_real_escape_string($db, $field['comment'] ?? '');
                $photo = isset($field['photo']) ? mysqli_real_escape_string($db, $field['photo']) : null;
                $progress = isset($field['progress']) ? mysqli_real_escape_string($db, $field['progress']) : null;
                $target_date = isset($field['target_date']) ? mysqli_real_escape_string($db, $field['target_date']) : null;
                $start_date = isset($field['start_date']) ? mysqli_real_escape_string($db, $field['start_date']) : null;
                $end_date = isset($field['end_date']) ? mysqli_real_escape_string($db, $field['end_date']) : null;
                $work_order = isset($field['work order']) ? mysqli_real_escape_string($db, $field['work order']) : null;

                $sql = "INSERT INTO `survey_response_eng_new`
                    (`category_id`, `inspection_id`, `main_id`, `question_id`, `response`, `comment`, `dealer_id`, `created_at`, `created_by`, `photo`, `progress`, `target_date`, `start_date`, `end_date`, `work_order`)
                    VALUES (
                        '$category_title', '$inspection_id', '$main_id', '$question', '$answer', '$comment', '$dealer_id', '$datetime', '$user_id',
                        " . ($photo ? "'$photo'" : "NULL") . ",
                        " . ($progress ? "'$progress'" : "NULL") . ",
                        " . ($target_date ? "'$target_date'" : "NULL") . ",
                        " . ($start_date ? "'$start_date'" : "NULL") . ",
                        " . ($end_date ? "'$end_date'" : "NULL") . ",
                        " . ($work_order ? "'$work_order'" : "NULL") . "
                    )";

                if (!mysqli_query($db, $sql)) {
                    echo "Error inserting detail: " . mysqli_error($db);
                    exit;
                }
            }
        }

        echo 1; // success
    } else {
        echo "Main insert error: " . mysqli_error($db);
    }
}
?>
