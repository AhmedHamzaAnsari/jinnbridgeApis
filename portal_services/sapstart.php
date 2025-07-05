<?php
	ini_set('max_execution_time', -1);
	$username="root";
	$password="";
	$database="jinnbridge";

	// Opens a connection to a MySQL server
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

function clean($string) {
   $string = str_replace('', '-', $string); // Replaces all spaces with hyphens.

   return preg_replace('/[^A-Za-z0-9]/', '', $string); // Removes special chars.
}

	$Date = date("Y-m-d");
	$sapdata ="SELECT * FROM Jinn.sapdata where tripstart = 1  and datetime >='$Date';";
	$sql_sapdata = mysqli_query( $connection,$sapdata);
	mysqli_error($connection);
	
		while($rowdata = mysqli_fetch_array($sql_sapdata)){
			
				$loadnum = $rowdata['deliveryno'];
				$deliveryno = $rowdata['deliveryno'];
				$tlno = $rowdata['tlno'];
				$driverid = $rowdata['driverid'];
				$drivername = $rowdata['dname'];
				$drivercnic = $rowdata['dcnic'];
				$drivernum = $rowdata['dnumber'];
				$tripstart = $rowdata['tripstart'];
				$tripend = $rowdata['tripend'];
				$alerts = $rowdata['alerts'];
				$Datesap = $rowdata['datetime'];
			echo "<br>";
			$sapstart ="SELECT * FROM Jinn.sapstart where deliveryno ='$loadnum' and tripstart=1;";
			$sql_start = mysqli_query( $connection,$sapstart);	
			if(mysqli_num_rows($sql_start) == 0){
				$sql = "INSERT INTO Jinn.sapstart(deliveryno, tlno, driverid, dname, dcnic, dnumber, tripstart, tripend, alerts, datetime,status) VALUES 
				('$deliveryno','$tlno','$driverid','$drivername','$drivercnic','$drivernum','$tripstart','$tripend','$alerts','$Datesap','0')";
				mysqli_query( $connection,$sql);
			}else{
				echo "Do Nothing".$loadnum.". <br>";
			}			
		}
		
	$sapdatastart ="SELECT * FROM Jinn.sapdata where tripend = 1 and datetime >='$Date';";
	$sql_sapdatastart = mysqli_query( $connection,$sapdatastart);
	mysqli_error($connection);
	
		while($rowdataend = mysqli_fetch_array($sql_sapdatastart)){
			
				$loadnum_end = $rowdataend['deliveryno'];
				$deliveryno_end = $rowdataend['deliveryno'];
				$tlno_end = $rowdataend['tlno'];
				$driverid_end = $rowdataend['driverid'];
				$drivername_end = $rowdataend['dname'];
				$drivercnic_end = $rowdataend['dcnic'];
				$drivernum_end = $rowdataend['dnumber'];
				$tripstart_end = $rowdataend['tripstart'];
				$tripend_end = $rowdataend['tripend'];
				$alerts_end = $rowdataend['alerts'];
				$Datesap_end = $rowdataend['datetime'];
			echo "<br>";
				$sapend ="SELECT * FROM Jinn.sapend where deliveryno ='$loadnum_end' and tripend=1;";
			$sql_end = mysqli_query( $connection,$sapend);	
			if(mysqli_num_rows($sql_end) == 0){
				$sqlend = "INSERT INTO Jinn.sapend(deliveryno, tlno, driverid, dname, dcnic, dnumber, tripstart, tripend, alerts, datetime,status) VALUES 
				('$deliveryno_end','$tlno_end','$driverid_end','$drivername_end','$drivercnic_end','$drivernum_end','$tripstart_end','$tripend_end','$alerts_end','$Datesap_end','0')";
				$truend = mysqli_query( $connection,$sqlend);
				if($truend == true){
					$q_update_end = "UPDATE sapstart set status = '1' where deliveryno ='$loadnum_end';";
					mysqli_query( $connection,$q_update_end);
				}else{
					
				}
			}else{
				echo "Do Nothing".$loadnum_end.". <br>";
			}			
		}


mysqli_close($connection);
?>
 <!DOCTYPE html>
 <html>
 <head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
 	<meta http-equiv="refresh" content="20">
 	<title>Jinn Data</title>
	<style>
	.progress {
    height: 3px !important;
    margin-bottom: 1px !important;
}
	</style>
 </head>
 <body style="background: #fff;">
 <div class="col-md-8">

<div class="col-md-12">
 	<br>
 	<?php echo "Successfully done"."<br>"; echo date("d-m-Y H:i:s", time()); ?>
	</div>
 </body>
 </html>