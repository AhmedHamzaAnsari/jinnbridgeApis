<!DOCTYPE html>
<html>

<head>
	<meta http-equiv="refresh" content="1">
	<title>Bulk Importer</title>
</head>

<body style="background: #fff;">
	<br>
	<?php echo date("d-m-Y H:i:s", time()); 
	echo "<br>";
	?>
</body>

</html>
<?php
for($i=0; $i <=1000000; $i++){
echo $i;
echo "<br>";
}
ini_set('max_execution_time', -1);
date_default_timezone_set("Asia/Karachi");


?>
<!DOCTYPE html>
<html>

<head>
	<meta http-equiv="refresh" content="30">
	<title>Bulk Importer</title>
</head>

<body style="background: #fff;">
	<br>
	<?php echo date("d-m-Y H:i:s", time()); 
	echo "<br>";
	?>
</body>

</html>