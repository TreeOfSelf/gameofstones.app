<?php

echo "Starting 15 minute update";

// load basic data
//echo "Connecting to DB...";
//include_once("connect.php");

//echo "Loading Display...";
//include_once('displayFuncs.php');
//echo "Loading Biz...";
//include_once('busiFuncs.php');
//echo "Loading chars...";
//include_once('charFuncs.php');
//echo "Loading map...";
//include_once('mapData.php');

echo "Starting Updates...";

$curtime=time();
$check=intval($curtime/3600);
$qcheck = intval($curtime/900);

// if we are on an hour change

// Do quarter hour updates
include_once('quarterly_updates.php');


if ($check*4 == $qcheck)
{

 echo "Starting hourly update";

  include_once('hourly_updates.php');
}else{
	echo "</br>\n Hourly update not running this iteration";
}

?>