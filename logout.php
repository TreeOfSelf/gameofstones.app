<?php
include('admin/connect.php');

$id = null;
$email = null;
$password = null;
$name = null;
$lastname = null;

setcookie("id", "", time()-3600, "/");
setcookie("name", "", time()-3600, "/");
setcookie("lastname", "", time()-3600, "/");
setcookie("email", "", time()-3600, "/");
setcookie("password", "", time()-3600, "/");


header("Location: $server_name/index2.php"); exit;
?>