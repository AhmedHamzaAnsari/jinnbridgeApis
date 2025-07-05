<?php
	ini_set('max_execution_time', -1);
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

		$inlist ="SELECT * FROM Jinn.devices WHERE latestPosition_id NOT IN (SELECT id FROM Jinn.positions)";
	$sql_inlist = mysqli_query( $connection,$inlist);
	mysqli_error($connection);
	
		while($rowinlist = mysqli_fetch_array($sql_inlist)){
	$name = $rowinlist['name'];
	$latestPosition_id = $rowinlist['latestPosition_id'];
	$datetimee = $rowinlist['devicescol'];
	$deviceid = $rowinlist['uniqueId'];
	$mainid = $rowinlist['vehicle_make'];
	$insert_positions = "INSERT into Jinn.positions(id,address,altitude,course,latitude,longitude,other,power,speed,time,valid,device_id,AlarmStatus,imileage,ikey,ireason,ireasoncode,syscode,vehicle_id,dtype,chk1,vlocation,overspeed,record_creation_time, recorddate)
	VALUES ('$latestPosition_id','','$name','360','0','0','','0','0','$datetimee','1','$deviceid','0','33006','0','0','','','$name','$mainid','','NR','','$datetimee','');";
	$basit= mysqli_query($connection,$insert_positions);
	
	
		}
mysqli_close($connection);

?>
 <!DOCTYPE html>
 <html>
 <head>
 	<title>NR Recovery</title>
 </head>
 <body style="background: #fff;">
 	<br>
 	<?php echo date("d-m-Y H:i:s", time()); ?>
 </body>
 </html>