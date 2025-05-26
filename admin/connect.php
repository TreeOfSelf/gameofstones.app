<?php
include_once('config.php');
global $db;
$db = @mysqli_connect($database_server, $database_username, $database_password,$database_name);
if (!$db) {echo "<br><b>Could not connect to the MySQL database. Please try again in a few minutes.</b>"; exit;}
$time=time();
$div_img = "<table border=0 cellpadding=0 cellspacing=0 width=550 height=1 background=\"images/divider.gif\"><tr><td></td></tr></table>";

// CONSTANTS
$battlelimit = 50;
$max_gold = 10000000000;
$enable_producers = 0;
$base_inv_max = 15;
$is_firefox=substr_count($_SERVER["HTTP_USER_AGENT"],"Firefox");
$maxalts = 2;
$maxquests = 2;
$maxsocalts = 99;

date_default_timezone_set('America/Los_Angeles');


$id = $_COOKIE['id'];
$name = $_COOKIE['name'];
$lastname = $_COOKIE['lastname'];
$email  = $_COOKIE['email'];
$password = $_COOKIE['password'];
$mode = $_COOKIE['mode'];


if ($email && $password) {
    $result = mysqli_query($db,"SELECT * From Accounts WHERE email='$email' AND password='$password'");
    if (mysqli_num_rows($result) != 0) {
        if ($id){
            $result = mysqli_query($db,"SELECT * From Users WHERE email='$email' AND id='$id'");
            if (mysqli_num_rows($result) == 0) {
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
                if (!headers_sent()) {
                    $time = time();
                    header("Location: $server_name/index2.php?time=$time"); exit;
                }
            }
        }
    }else{
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
        if (!headers_sent()) {
            $time = time();
            header("Location: $server_name/index2.php?time=$time"); exit;
        }
    }
}else{
    if($skipVerify != 1){
        if (!headers_sent()) {
            $time = time();
            header("Location: $server_name/index2.php?time=$time"); exit;
        }       
    }
}


?>