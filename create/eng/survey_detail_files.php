<?php
include("../../config.php");
session_start();
if (isset($_POST)) {
    $user_id = $_POST['user_id'];
  
    $category_id = $_POST["category_id"];
    $question_id = $_POST["question_id"];
    $dealer_id = $_POST["dealer_id"];
    $inspection_id = $_POST["inspection_id"];

    

    $file = rand(1000, 100000) . "-" . $_FILES['files']['name'];
    $file_loc = $_FILES['files']['tmp_name'];
    $file_size = $_FILES['files']['size'];
    //  $file_type = $_FILES['file']['type'];
    $folder = "../../../jinnBridge_files/uploads/";
    move_uploaded_file($file_loc, $folder . $file);

   

    $tdate = date('Y-m-d H:i:s');
    $num = mt_rand(100000, 999999);




        $query_main = "INSERT INTO `survey_response_files_eng`
        (`category_id`,
        `inspection_id`,
        `question_id`,
        `file`,
        `dealer_id`,
        `created_at`,
        `created_by`)
        VALUES
        ('$category_id',
        '$inspection_id',
        '$question_id',
        '$file',
        '$dealer_id',
        '$tdate',
        '$user_id');";



        if (mysqli_query($db, $query_main)) {
            $output = 1;



        } else {
            $output = 'Error' . mysqli_error($db) . '<br>' . $query_main;

        }
    



    echo $output;
}
?>