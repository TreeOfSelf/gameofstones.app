<link REL="StyleSheet" TYPE="text/css" HREF="style.css">

<html>
	<head>
<?php
	include('info.php');
	include('connect_info.php');
	include($server_name.'/map/mapdata/coordinates.inc');
	
	$town_type=array(
		'settlement',
		'village',
		'small town',
		'town',
		'large town',
	);
?>	
	</head>
	<body bgcolor="black">
		<font class="littletext">
		<b>Mayor's House:</b>
		<br>
		<br>
		Population: 
		<?php
			$result = mysqli_fetch_array(mysqli_query($db,"SELECT COUNT(*) FROM Users WHERE location='".$char['location']."'"));
			echo $result[0]."<br><br><font class='foottext'>";
		?>
		<br>
		
	
	</body>
	<footer>
	</footer>
</html>