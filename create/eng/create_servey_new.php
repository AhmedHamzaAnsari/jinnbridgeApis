<?php
include("../../config.php");
session_start();

if (isset($_POST)) {
    $user_id = mysqli_real_escape_string($db, $_POST["user_id"]);
    $response = $_POST["response"];
    $dealer_id = $_POST["dealer_id"];
    $inspection_id = $_POST["inspection_id"];


    $datetime = date('Y-m-d H:i:s');

    $query_main = "INSERT INTO `survey_response_main_eng`
    (`dealer_id`,
    `inspection_id`,
    `data`,
    `created_at`,
    `created_by`)
    VALUES
    ('$dealer_id',
    '$inspection_id',
    '$response',
    '$datetime',
    '$user_id');";

    if (mysqli_query($db, $query_main)) {
        $active = mysqli_insert_id($db);
        $data = json_decode($response, true);

        // Check if decoding was successful
        if ($data === null && json_last_error() !== JSON_ERROR_NONE) {
            echo "Error decoding JSON: " . json_last_error_msg();
        } else {


            $data = $response;

            $arrayData = json_decode($data, true);

            foreach ($arrayData as $item) {
                foreach ($item as $key => $values) {
                    $category_id = $key;
                    foreach ($values as $innerKey => $innerValues) {
                        // echo "  Inner Key: $innerKey\n";
                        foreach ($innerValues as $innerInnerKey => $innerInnerValue) {
                            $jj = json_encode($innerInnerValue);
                            // echo   $innerInnerKey;
                            $arrayData = json_decode($jj, true);
                            $question_id = $innerInnerKey;
                            $answers = $arrayData['response'];
                            $comment = $arrayData['comment'];

                            // Print response and comment
                            // echo "$category_id:\n";
                            // echo "question_id " . $question_id. "\n";
                            // echo "Response: " . $arrayData['response'] . "\n";
                            // echo "Comment: " . $arrayData['comment'] . "\n";

                            $sql1 = "INSERT INTO `survey_response_eng`
                            (`category_id`,
                            `inspection_id`,
                            `main_id`,
                            `question_id`,
                            `response`,
                            `comment`,
                            `dealer_id`,
                            `created_at`,
                            `created_by`)
                            VALUES
                            ('$category_id',
                            '$inspection_id',
                            '$active',
                            '$question_id',
                            '$answers',
                            '$comment',
                            '$dealer_id',
                            '$datetime',
                            '$user_id');";

                            if (mysqli_query($db, $sql1)) {
                                $sql_check = "SELECT * FROM survey_category_questions_eng where category_id='$category_id' and id='$question_id' order by id desc";

                                // echo $sql;

                                $result_check = mysqli_query($db, $sql_check);
                                $row_check = mysqli_fetch_array($result_check);

                                $count_check = mysqli_num_rows($result_check);
                                if ($count_check > 0) {
                                    $accelaration = $row_check['answer'];
                                    $duration = $row_check['duration'];

                                    // Print the array using a loop
                                    if ($accelaration == $answers) {
                                        // echo $value . "<br>";
                                        $folow_ups = "INSERT INTO `follow_ups_eng`
                                            (`category_id`,
                                            `question_id`,
                                            `answer`,
                                            `task_id`,
                                            `form_id`,
                                            `dpt_id`,
                                            `dpt_users`,
                                            `form_name`,
                                            `response_id`,
                                            `table_name`,
                                            `cat_table`,
                                            `ques_table`,
                                            `created_at`,
                                            `created_by`)
                                            VALUES
                                            ('$category_id',
                                            '$question_id',
                                            '$answers',
                                            '$inspection_id',
                                            '',
                                            '',
                                            '',
                                            '',
                                            '$active',
                                            'survey_response_eng',
                                            'survey_category_eng',
                                            'survey_category_questions_eng',
                                            '$datetime',
                                            '$user_id');";

                                        if (mysqli_query($db, $folow_ups)) {
                                            $output = 1;
                                        } else {
                                            $output = 0;
                                        }


                                    } else {
                                        $output = 1;

                                    }
                                } else {
                                    $output = 1;
                                }

                            } else {
                                $output = 0;
                            }
                        }
                    }
                    // echo "\n";
                }
            }


            echo $output;
        }
    }





}


?>