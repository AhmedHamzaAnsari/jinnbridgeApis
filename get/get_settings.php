<?php  
 //fetch.php  
 include("../config.php");

 
    $access_key = '03201232927';

    $pass = $_GET["key"];
    if($pass!=''){
        if($pass==$access_key){
            $query = "SELECT * FROM settings order by id desc limit 1;";  
            $result = mysqli_query($db, $query);  
            $row = mysqli_fetch_array($result);  
            echo json_encode($row);  
    
        }
        else{
            echo 'Wrong Key...';
        }

    }else{
        echo 'Key is Required';
    }
    
  
 ?>