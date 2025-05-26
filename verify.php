<?php
$skipVerify = 1;
/* get the incoming ID and password hash */
$user = isset($_POST["email"]) ? $_POST["email"] : null;

if ($user === null || empty($user)) {
    // $_POST["userid"] is not valid; perform the redirect to the root URL
    header("Location: https://" . $_SERVER["HTTP_HOST"]);
    exit; // Ensure that no further code is executed after the redirect
}

/* establish a connection with the database */
include_once("admin/connect.php");


$email = $_POST["email"];
$pass = sha1($_POST["pswd"]);
$time=time();


  
/* SQL statement to query the database */
//$query = "SELECT * FROM Users WHERE name = '$user' AND lastname = '$last' AND password = '$pass'";
$query = "SELECT * FROM Accounts WHERE email = '$email' AND password = '$pass'";
$result = mysqli_query($db,$query);


/* Allow access if a matching record was found and cookies enabled, else deny access. */
if (!is_null($result) && $account = mysqli_fetch_array($result))
{


$mode = 0;
if ($_POST["mode"]) $mode = mysqli_real_escape_string($db,$_POST["mode"]);

setcookie("email", "$email", time()+99999999, "/");
setcookie("password", "$pass", time()+99999999, "/");
setcookie("mode", "$mode", time()+99999999, "/");


$query = "SELECT * FROM Users WHERE email = '$email'";
$result = mysqli_query($db,$query);


if (mysqli_num_rows($result) > 0) {

    $char = mysqli_fetch_array($result);
    $id = $char['id'];
    $user = $char['name'];
    $lastname = $char['lastname'];
    setcookie("id", "$id", time()+99999999, "/");
    setcookie("name", "$user", time()+99999999, "/");
    setcookie("lastname", "$lastname", time()+99999999, "/");
        
	header("Location: $server_name/bio.php?time=$time");
	exit;
} else {
    header("Location: $server_name/create.php");
    exit;
}




}
elseif (!$_GET['enabled'])
{
include('header.php');
?>

<text class="littletext">

<br><br>
<?php
echo "<center><b>Access Denied:</b> No such matching Account and Password";
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

