<?php
$skipVerify = 1;
require_once("ayah.php");
$integration = new AYAH();


include_once("admin/connect.php");
include_once("admin/skills.php");
include_once("admin/charFuncs.php");
include_once("admin/itemFuncs.php");

//gather user variables from last page where inputed
$email=mysqli_real_escape_string($db,$_POST['email']);
$actualpass=mysqli_real_escape_string($db,$_POST['password']);
$actualpass2=mysqli_real_escape_string($db,$_POST['pass2']);
$password=sha1($actualpass);

$skipVerify = 1;
// clear stuff that could be transferred

$query = "SELECT * FROM Accounts WHERE email = '$email'";
$result = mysqli_query($db,$query);

if (mysqli_fetch_row($result)) {
  include('header.php');
  echo "<br/><br/>";
  echo "<center>The Account <b>$email</b> already exists. Please choose another email.</center>";
}
else if (strlen($actualpass) <= 4 || strlen($username) >= 16)
{
  include('header.php');
  echo "<br/><br/>";
  echo "<center>Invalid Password given: Must be 5-15 characters long.</center>";
}
else if ($actualpass != $actualpass2)
{
  include('header.php');
  echo "<br/><br/>";
  echo "<center>Invalid Password given: Password and confirmation password do not match.</center>";
}
else if (strlen($email) < 4 || strlen($email) > 40)
{
  include('header.php');
  echo "<br/><br/>";
  echo "<center>Invalid email given: Email must be between 4 and 40 characters long.</center>";
}
else if (!$_POST['nocrap'])
{
  include('header.php');
  echo "<br/><br/>";
  echo "<center>You must agree to accept the rules. Make sure you check the box accepting them.</center>";
}
else
{
  if ($score || 1) 
  {
  $ips[0]=$_SERVER['REMOTE_ADDR'];
  $alts = [];
  $ip_log = [];
  $users=[];
  for ($i = 0; $i < count($ips); $i++)  
  {
    $result = mysqli_query($db,"SELECT * FROM IP_logs WHERE addy='$ips[$i]'");
    $ip_log = mysqli_fetch_array($result); 
    $users= unserialize($ip_log['users']);
	if(!empty($users)){
		for ($j=0; $j < count($users); $j++)  
		{  $alts[$users[$j]] = 1; } 
	}
  }
  $maxnum=2;
  
 // if ($ip_log['num']) {$maxnum = $ip_log['maxnum'];}
	$maxnum = $ip_log['maxnum'];
  // DISABLE ALT LIMIT
  $limit_off = 0;
  if(is_null($maxnum) || intval($maxnum)<2){
	  $maxnum=2;
  }
  
  
  //Disable alt limit
  if (false && count($alts) >= $maxnum && $limit_off==0)
  {
    $altnum = count($alts);
    include('header.php');
    echo "<br/><br/>";
    echo "<center>You already have <b>$altnum</b> account(s).</center>";
  }
  else
  {



  $sql = "INSERT INTO Accounts (email, password) VALUES ('$email','$password')";
	$result = mysqli_query($db, $sql);


    setcookie("email", "$email", time()+99999999, "/");
    setcookie("password", "$password", time()+99999999, "/");

    header("Location: $server_name/create.php?time=$born");
    exit;

  }
  }
  else 
  {
    include('header.php');
    echo "<center><br/><br/><br/><b>This Character could not be created!<br/><br/><br/><br/>Be sure to complete the 'Are you a Human?' check!</center>";
  }
}

include('header.php');
//echo "<center><br/><br/><br/><b>This Character could not be created<br/><br/><br/><br/><table><tr><td class='littletext' align=left><b>1.</b> The first and last names must be between 3 and 10 characters in length <br/><br/><b>2.</b> The password must be between 5 and 10 characters in length<br/><br/><b>3.</b> Both parts of the name must consist only of letters (no spaces)<br/><br/><b>4.</b> The E-Mail address must not exceed 40 characters<br/><br/><b>5. <i>You must agree to the terms</i></b><br/><br/><b>6.</b> You must choose a nationality, class, and weapon focus.<br/><br/><b>7.</b> Complete the 'Are you a Human?' check.</td></tr></table></center>";

?>

<br/>

<?php

include('footer.htm');

?>

