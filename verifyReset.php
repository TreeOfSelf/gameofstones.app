<?php

function getName($n) { 
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ'; 
    $randomString = ''; 
  
    for ($i = 0; $i < $n; $i++) { 
        $index = rand(0, strlen($characters) - 1); 
        $randomString .= $characters[$index]; 
    } 
  
    return $randomString; 
} 
  


/* get the incoming email */
if (!isset($_POST["email"])) {
    exit("Error: Email address not provided."); 
}
$recoveryEmail = $_POST["email"];
$time=time();
$skipVerify = 1;

/* establish a connection with the database */
include_once("admin/connect.php");
  
/* SQL statement to query the database */
$query = "SELECT * FROM Accounts WHERE email = '$recoveryEmail'";

/* query the database */
$result = mysqli_query($db,$query);
$result = mysqli_fetch_array( $result );

/* Allow access if a matching record was found and cookies enabled, else deny access. */
if ($result)
{
include('header.php');
echo "<center><b>Check your email for code to reset!</b> Make sure to check your spam folder.";

mysqli_query($db,"DELETE FROM Reset WHERE email='$recoveryEmail'");

$code = getName(rand(10,20));


$message = "Your reset code is: \r".$code."\r Do not give this code to anyone!";

// In case any of our lines are larger than 70 characters, we should use wordwrap()
$message = wordwrap($message, 70, "\r\n");

// Send
mail($recoveryEmail, 'Password Reset', $message);

$sql = "INSERT INTO Reset (email, code) VALUES ('$recoveryEmail','$code')";
$result = mysqli_query($db, $sql);
?>

 <div class="row"align='left' style="background-color:#00000096; border-radius:25px; padding:20px; margin:20px">
      <form role="form" action="verifyCode.php" method="post">
        <div class="form-group form-group-sm">
          <label for="userid">Code:</label>
          <input type="text" class="form-control gos-form" id="userid" name="userid" maxlength="30">
        </div>
        <div class="form-group form-group-sm">
          <label for="pwod">New Password:</label>
          <input type="password" class="form-control gos-form" id="pwod" name="pwod" maxlength="15">
        </div>
        <div class="form-group form-group-sm">
          <label for="confirm">Confirm Password:</label>
          <input type="password" class="form-control gos-form" id="confirm" name="confirm" maxlength="15">
        </div>
        <button type="submit" class="btn btn-block btn-danger">Reset Password</button>
      </form>     
      <br/><br/>
    </div>
  </div>

<?php

}
elseif (!$_GET['enabled'])
{
$skipVerify = 1;
include('header.php');
?>

<text class="littletext">

<br><br>
<?php
echo "<center><b>Access Denied:</b> No such matching Email";
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

