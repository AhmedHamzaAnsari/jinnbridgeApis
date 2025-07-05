<?php
ini_set('max_execution_time', -1);
$username = "root";
$password = "";
$database = 'jinnbridge';

// Opens a connection to a MySQL server
$connection = mysqli_connect('localhost', $username, $password, $database);
if (!$connection) {
	die('Not connected : ' . mysqli_error());
}

// Set the active MySQL database
$db_selected = mysqli_select_db($connection, $database);
if (!$db_selected) {
	die('Can\'t use db : ' . mysqli_error());
}

function clean($string)
{
	$string = str_replace('', '-', $string); // Replaces all spaces with hyphens.

	return preg_replace('/[^A-Za-z0-9]/', '', $string); // Removes special chars.
}




//Tpl_puma


$filetpl = "https://mytrakker.tpltrakker.com/TrakkerServices_Stag/Api/Home/GetVLL/00903315646/5646";
$datatpl = file_get_contents($filetpl);
$arraytpl = json_decode($datatpl, true);

foreach ($arraytpl as $Jinntpl) {

	$imeitplnew = "tpl" . clean($Jinntpl["RegNo"]);
	$nametplnew = $Jinntpl["RegNo"];
	$lattplnew = $Jinntpl["Lat"];
	$lngtplnew = $Jinntpl["Long"];
	$angletplnew = $Jinntpl["Direction"];
	$speedtplnew = $Jinntpl["Speed"];
	$datetimetplnew = $Jinntpl["GpsDateTime"];
	$datebbtpl = date_create($datetimetplnew);
	$datetimetpl = date_format($datebbtpl, "Y-m-d H:i:s");
	$licensepntplnew = '112113114115';
	$odometertplnew = $Jinntpl["Odo"];
	$ignetiontplnew = $Jinntpl["Ignition"];
	$protocoltplnew = "TPL";
	$last_idletplnew = '000';
	$last_movetplnew = '000';
	$last_stoptplnew = $Jinntpl["Location"];

	$sqltplnew = "INSERT INTO bulkdatanew (id,imei,st_server,lat,lng,angle,speed,name,sim_number,odometer,list,protocol,last_idle,last_move,last_stop,status)
VALUES ('TPL','$imeitplnew','$datetimetpl','$lattplnew','$lngtplnew','$angletplnew','$speedtplnew','$nametplnew','$licensepntplnew','$odometertplnew','$ignetiontplnew','$protocoltplnew','$last_idletplnew','$last_movetplnew','$last_stoptplnew','0');";


	mysqli_query($connection, $sqltplnew);
}

//tpl_puma end


?>





<div class="progress">
	<div class="progress-bar" role="progressbar" aria-valuenow="70" aria-valuemin="0" aria-valuemax="100"
		style="width:<?php echo $i; ?>%">
		<span class="sr-only">
			<?php ?>
		</span>
	</div>
</div>
</div>
<?php

if ($sqltplnew == true) {
	echo "<br> New record created successfully yeahoo TPL ";

} else {
	echo "Error: " . $sqltplnew . "<br>" . mysqli_error($connection);

}


$sql1 = mysqli_query($connection, "SELECT COUNT(*) as num FROM bulkdatanew");

$result = mysqli_fetch_assoc($sql1);
echo '<br>' . $result['num'];
$t_row = $result['num'];
mysqli_close($connection);
?>
<!DOCTYPE html>
<html>

<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta http-equiv="refresh" content="400">
	<title>Puma Data</title>
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
			<?php echo "Successfully done" . "<br>";
			echo date("d-m-Y H:i:s", time()); ?>
		</div>
</body>

</html>