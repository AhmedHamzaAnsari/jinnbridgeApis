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
		function clean($string) {
   $string = str_replace('', '-', $string); // Replaces all spaces with hyphens.

   return preg_replace('/[^A-Za-z0-9]/', '', $string); // Removes special chars.
}
	$inlisttracker ="SELECT * FROM Jinn.devicesnew;";
	$sql_inlist_tracker = mysqli_query( $connection,$inlisttracker);
	mysqli_error($connection);
	while($rowinlist_tracker = mysqli_fetch_array($sql_inlist_tracker)){
		$mainid = $rowinlist_tracker['id'];
		$name = $rowinlist_tracker['name'];
		echo $imei = clean($rowinlist_tracker["name"]);
		echo "<br>";
		$update_inlistname = "UPDATE Jinn.devicesnew SET imei = '$imei' WHERE id = '$mainid'";
		$update_inlistname_result = mysqli_query($connection,$update_inlistname);
		
	}
	
?>
 <!DOCTYPE html>
 <html>
 <head>
 	<title>UPDATE devicesnew</title>
 </head>
 <body style="background: #fff;">
 	<br>
 	<?php echo date("d-m-Y H:i:s", time()); ?>
 </body>
 </html>