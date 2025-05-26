<?php

/* get the incoming ID and password hash */
$user = $_POST["userid"];
$pwod = ($_POST["pwod"]);
$confirm =  ($_POST["confirm"]);


$skipVerify = 1;
/* establish a connection with the database */
include_once("admin/connect.php");
 

//Find code
$query = "SELECT * FROM Reset WHERE code = '$user'";
$result = mysqli_query($db,$query);
$codeResult = mysqli_fetch_array( $result );

/* Allow access if a matching record was found and cookies enabled, else deny access. */
if ($codeResult)
{

	include('header.php');
	//Make sure passwords match
	if(strcmp($pwod,$confirm) == 0 ){
	
	$pwod = sha1($pwod);
	
	mysqli_query($db,"DELETE FROM Reset WHERE code='$user'");
	
	mysqli_query($db,"UPDATE Accounts SET password = '$pwod' WHERE email = '$codeResult[email]'");
	
	echo "<center><b>Password Updated:</b> Please relog.";
	}else{
	echo "<center><b>Password Mismatch:</b> Please type your confirmation password correctly. ";
	}
}
elseif (!$_GET['enabled'])
{
$skipVerify = 1;
include('header.php');
?>

<text class="littletext">

<br><br>
<?php
echo "<center><b>Access Denied:</b> Incorrect code! Please try again.";
}
else
{
include('headerno.htm');
?>
<br><br>
<?php
echo "<center><b>You must have cookies enabled in order to log in.</b><br><br>The fact that you are viewing this message likely means that you do not.</center>";
?>
<br><br><center>This website will help you to enable your cookies<br><a href="http://scholar.google.com/cookies.html">Google's Help Website on Enabling Cookies</a>
<?php
} 

include('footer.htm');
?>

