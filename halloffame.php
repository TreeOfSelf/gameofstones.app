<?php

/* establish a connection with the database */
include_once("admin/connect.php");
include_once("admin/userdata.php");
include_once("admin/locFuncs.php");

$wikilink = "Controlling+Cities";

$time=time();
$sort= $_REQUEST['sort'];


$message = "View hall of fame history";


$age = $_GET["age"];

if(!is_null($age)){
	$message = "Viewing hall of fame from ".$age;
}

//HEADER
include('header.php');





if(is_null($age)){
	
	?>
		</br>
		</br>
		</br>
	<?php
	
	$fileList = glob('history/ages/*');
	//Loop through the array that glob returned.
	foreach($fileList as $filename){
	   //Simply print them out onto the screen.
	   $prettyName = str_replace("history/ages/","",$filename);
	   $prettyName = str_replace(".html","",$prettyName);
	   ?> <a class='btn btn-default btn-sm btn-block' href="halloffame.php?age=<?php echo $prettyName ?>" style="max-width:300px"> <?php echo $prettyName?></a>
	   
	   <?php 
	 
	}

}else{
	$data = file_get_contents("https://".$_SERVER['SERVER_NAME']."/history/ages/".$age.".html");
	echo $data;	
}

include("footer.htm");
?>

