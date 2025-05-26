<?php
include_once(dirname(__FILE__).'/../../admin/config.php');
$db = @mysqli_connect($database_server, $database_username, $database_password,$database_name);
//mysql_select_db($database_name,$db);
if (!$db) {echo "<br><b>Cannot connect to the MySQL database.</b>"; exit;}
if (!$no_query)
{
	$result = mysqli_query($db,"SELECT Users.id, Users.gold, Users.bankgold, Users.society, Users.lastbank, Users.travelmode, Users.travelmode_name, Users.feedneed, Users.travelmode2, Users.location, Users.travelto, Users.arrival, Users.depart, Users.password, Users.traveltype FROM Users LEFT JOIN Users_data ON Users.id=Users_data.id WHERE Users.id='$id'");
	$char = mysqli_fetch_array($result);
	$id=$char['id'];
}
else {
	$result = mysqli_query($db,"SELECT password FROM Users WHERE id='$id'");
	$char = mysqli_fetch_array($result);
}
if ($char['password'] != $password) {
	echo "<center><font color='white'><br>Nice try, cheater.";
	exit;
}
?>