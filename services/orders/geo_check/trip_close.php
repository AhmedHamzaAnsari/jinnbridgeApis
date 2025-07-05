
<?php
ini_set('max_execution_time', '0');
$url1=$_SERVER['REQUEST_URI'];
header("Refresh: 20; URL=$url1");


define('DB_SERVER', 'localhost');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', '');
define('DB_DATABASE', 'sitara');
$db = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_DATABASE);
echo "<h1>Trip Close service .</h1><br>";
$vehicle_name;
$date_time = date('Y-m-d h:i:s',strtotime("-15 days"));
$sql_1="SELECT * FROM sitara.trip_sub where status='0' and consignee_id!='' and start_time >='$date_time';";
// echo $sql_1 ."<br>";
$result_1 = mysqli_query($db,$sql_1);
$count_1 = mysqli_num_rows($result_1);
// echo $count;
if($count_1 > 0) {
    while( $row = mysqli_fetch_array($result_1) ){
        $consignee_id = $row['consignee_id'];
        $vehicle_id = $row['vehicle_id'];
        $st_id = $row['id'];

        $sql="SELECT ts.*,pos.lat as latitude, pos.lng as longitude FROM sitara.trip_sub as ts 
        join sitara.devicesnew as pos on ts.vehicle_id=pos.id 
        where ts.vehicle_id='$vehicle_id'and ts.consignee_id='$consignee_id' and ts.id='$st_id' order by pos.id desc limit 1;";
        $result = mysqli_query($db,$sql);
        $count = mysqli_num_rows($result);
        // echo $count;
        if($count > 0) {
            while( $row = mysqli_fetch_array($result) ){
                // $userid = $row['id'];
              

                 $v_lat = $row["latitude"];
                 $v_lng = $row["longitude"];
                 $v_num = $row["vehicle_name"];
                 $v_id = $row["vehicle_id"];
                 $geo_id = $row["consignee_id"];
                 $sub_id = $row["id"];
                 $consignee_no = $row["consignee_contact_1"];
                 $consignee_no2 = $row["consignee_contact_2"];
                 $consignee_no3 = $row["consignee_contact_3"];

                 echo '---------------------------------------------------<br>';
                 echo "sub id ".$sub_id . '<br>';
                
                echo '<br/>';
                echo 'car name = '. $v_num . ' id = ' .$v_id;
                echo '<br/>';
                // echo 'Lat Lng => ' .$v_lat. ' ' .$v_lng ;
                // echo '<br/>';
                echo '<br/>';
    
                get_geo($v_lat, $v_lng, $v_num, $v_id, $geo_id, $sub_id);
                
                
               
            }
        
        
        
        
        }
        else{
            echo '<h1>No Records Found to send Msg</h1>';
        }

    }
}



function get_geo($v_lat, $v_lng, $v_num, $v_id, $consignee_id, $sub_id) {
   
    $dbs = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_DATABASE);
    
    $sql_geo="SELECT id,consignee_name,location,Coordinates,type FROM `geofenceing` where id='$consignee_id'";
    $result_geo = mysqli_query($dbs,$sql_geo);
    $count_geo = mysqli_num_rows($result_geo);
    // echo $count;
    if($count_geo > 0) {
        while( $row = mysqli_fetch_array($result_geo)){
            

            $co = $row['Coordinates'];
            $c_name = $row['consignee_name'];
            $location = $row['location'];
             $id = $row['id'];

            
            // echo '<br/>';
            echo 'name = '. $c_name . ' id = ' .$id;
            // echo '<br/>';

            
            

            $mychars = explode(',', $co);
            // echo 'Lati => ' .$mychars[0] . ' longi ' . $mychars[1] ;
            // echo '<br/>';


            $c_lat = $mychars[0];
            $c_lng = $mychars[1]; 
            $km = 0.155;
            //console.log("Samad" + co+" sss "+v_num)
            $ky = 40000 / 360;
            $kx = cos(pi() * $c_lat / 180.0) * $ky;
            $dx = abs($c_lng - $v_lng) * $kx;
            $dy = abs($c_lat - $v_lat) * $ky;
            echo '<br/>';
            echo sqrt(($dx * $dx) + ($dy * $dy)). '<=' . $km;
            echo '<br/>';
            // echo $km;

            
            
            if (sqrt(($dx * $dx) + ($dy * $dy)) <= $km == true) {
                // echo ($v_lat + "," + $v_lng + "," + $v_id + "     " + $c_lat + "," + $c_lng + "," + $v_num + "," + $id + "," + $c_name + "," + $location);
                $in_time = date('Y-m-d H:i:s');
                echo '<br/>';

                echo 'IN TIME =>' .$in_time;
                echo '<br/>';
                echo sqrt(($dx * $dx) + ($dy * $dy)). '<=' . $km;
                echo '<br/>';

                // insert($v_id, $id, $in_time);
                insert($v_id, $id, $in_time, $sub_id, $c_name, $v_num);
            }else{

                echo '<br/>';
                echo 'Not IN';
                echo '<br/>';
            }

           
        }




    }

  

}



function insert($v_id, $geo_id, $in_time, $sub_id, $consignee_name, $v_num) {
   
    $dbss = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_DATABASE);
    
    $sql_check_geo="UPDATE sitara.trip_sub set status='1' where id='$sub_id'";
    $result_check_geo = mysqli_query($dbss,$sql_check_geo);
    // echo $count;
    if($result_check_geo ) {
        echo '<br/>';
        echo 'Already IN';
        echo '<br/>';

        $sql_insert = "INSERT INTO `trip_close`(`sub_id`, `sms`, `close_time`) VALUES ('$sub_id','0','$in_time')";
        if (mysqli_query($dbss, $sql_insert)) {
           echo "New record created successfully !<br>";
        } else {
           echo "Error: " . $sql_insert . "
    " . mysqli_error($dbss);
        }



    }
    else{
        echo "Status Not Updated !<br>";

    }

  

}


mysqli_close($db);
?>