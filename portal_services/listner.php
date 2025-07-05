<?php
	ini_set('max_execution_time', -1);
	date_default_timezone_set("Asia/Karachi");
	$username="root";
	$password="";
	$database="jinnbridge";
	$connection=mysqli_connect('localhost', $username, $password,$database);
	if (!$connection)
	{
	  die('Not connected : ' . mysqli_error());
	}

	// Set the active MySQL database
	$db_selected = mysqli_select_db( $connection,$database);
	if (!$db_selected)
	{
	  die ('Can\'t use db : ' . mysqli_error());
	}
	$q_update = "UPDATE bulkdatanew set status = '1'";
	mysqli_query( $connection,$q_update);
	
$sql = "SELECT * FROM bulkdatanew WHERE status = '1' order by imei asc, st_server desc";
$sql_select = mysqli_query( $connection,$sql);
mysqli_error($connection);
echo $todate = date("Y-m-d H:i:s", time());
echo "<br>";
$i='0';
//foreach( mysqli_fetch_array($sql_select) as $row){
	while($row = mysqli_fetch_array($sql_select)){
	$id = $row['imei'];
	$imei = $row['imei'];
	$mainid = $row["protocol"];
	$dt_time = $row["st_server"];
	$timein = $row["st_server"];
	$lat = $row["lat"];
	$lng = $row["lng"];
	$angle = $row["angle"];
	$speed = $row["speed"];
	$name = $row["name"];
	$vehicle = $row["name"];
	$licensepn = $row["sim_number"];
	$odometer = $row["odometer"];
	$list = $row["list"];
	$protocol = $row["protocol"];
	$last_idle = $row["last_idle"];
	$last_move = $row["last_move"];
	$last_stop = $row["last_stop"];
	if($mainid == 'tw_Jinn'){
		$userid = 198;
	}elseif($mainid == 'al_shyma'){
		$userid = 230;
	}elseif($mainid == 'Universal'){
		$userid = 231;
	}
	elseif($mainid == 'topflay'){
		$userid = 232;
	}
	elseif($mainid == 'anytracker'){
		$userid = 233;
	}
	elseif($mainid == 'PTSL'){
		$userid = 262;
	}
	elseif($mainid == 'united_Jinn'){
		$userid = 314;
	}
	elseif($mainid == 'resq911'){
		$userid = 345;
	}
	elseif($mainid == 'TPL'){
		$userid = 370;
	}
	elseif($mainid == 'tellogix'){
		$userid = 375;
	}
	elseif($mainid == 'xtream'){
		$userid = 387;
	}
	elseif($mainid == 'teltonika'){
		$userid = 389;
	}
	else{
		$userid = 30;
	}
	$d_select = "SELECT * FROM devicesnew WHERE imei ='$imei'";
		$sql_devices = mysqli_query( $connection,$d_select);
		mysqli_error($connection);
		if(mysqli_num_rows($sql_devices) > 0){
			$Select1 = "SELECT time,id FROM devicesnew WHERE imei ='$imei'";
			$exe_Select1 = mysqli_query( $connection,$Select1);
			mysqli_error($connection);
			$data_Select1= mysqli_fetch_assoc($exe_Select1);

			if(!isset($data_Select1['time'])){
				$lasttime = '2020-01-01 10:10:10';
			}
			else{
				$lasttime = $data_Select1['time']; //--------------err
			}
			// echo $lasttime.' imei '.$imei .' servertime '. $dt_time. '<br>';
			if($dt_time > $lasttime){
				$dviceid = $data_Select1['id'];
			$update_devices = "UPDATE devicesnew SET time = '$dt_time', lat = '$lat' , lng = '$lng', angle = '$angle' , ignition = '$list', speed ='$speed' , odometer = '$odometer' , lasttime = '$todate' ,location = '$last_stop' where imei ='$imei'";
			$basit1 = mysqli_query($connection,$update_devices);
			echo "Record update $imei <br>";
			
			if($basit1 == TRUE){
			$insert_positions = "INSERT into positionsnew(latitude,longitude,address,speed,power,odometer,course,tracker,time,vehicle_name,device_id)
	VALUES ('$lat','$lng','$last_stop','$speed','$list','$odometer','$angle','$protocol','$dt_time','$name','$dviceid');";
	$basit= mysqli_query($connection,$insert_positions);
	echo mysqli_error($connection);
		}
		}
		
		}
		
		else{
			//New installation
			
  $sql_insert_dev = mysqli_query($connection,"INSERT INTO  devicesnew 
	(name, uniqueId, latestPosition_id, device_type, trackername, organisation, tracker, speed, speedlimit, lat, lng, location, time,
	angle, imei, odometer, ignition, lasttime, activedate)  
	VALUES 
	('$vehicle','','','1','$mainid','','$mainid','$speed','$speed', '$lat', '$lng','$last_stop','$dt_time','$angle','$imei','$odometer','$list','$todate','$todate')") or die(mysqli_error($connection));
			$Selectnew = "SELECT time,id FROM devicesnew WHERE imei ='$imei'";
			$exe_Selectnew = mysqli_query( $connection,$Selectnew);
			mysqli_error($connection);
			$data_Selectnew= mysqli_fetch_assoc($exe_Selectnew);
			$dviceidnew = $data_Selectnew['id'];
	
	$insert_positions_new = "INSERT into positionsnew(latitude,longitude,address,speed,power,odometer,course,tracker,time,vehicle_name,device_id)
	VALUES ('$lat','$lng','$last_stop','$speed','$list','$odometer','$angle','$protocol','$dt_time','$name','$dviceidnew');";
	$basit_new= mysqli_query($connection,$insert_positions_new);
		}
	}
	
	if($sql_select == true){
$q_delete = "DELETE FROM bulkdatanew WHERE status = '1'";
$sql_delete = mysqli_query( $connection,$q_delete);
}
echo "done delete";
?>
 <!DOCTYPE html>
 <html>
 <head>
	<meta http-equiv="refresh" content="30">
 	<title>Bulk Importer</title>
 </head>
 <body style="background: #fff;">
 	<br>
 	<?php echo date("d-m-Y H:i:s", time()); ?>
 </body>
 </html>