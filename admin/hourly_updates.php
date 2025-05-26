<?php
// load basic data


echo "Connecting to DB...";
include_once("connect.php");
include_once('displayFuncs.php');
include_once('busiFuncs.php');
echo "Starting Hourly Updates...";

$curtime=time();
$check=intval(time()/3600);
$lastBattleDone = mysqli_num_rows(mysqli_query($db,"SELECT id FROM Contests WHERE type='99' AND done='1'"));
$msgs = mysqli_fetch_array(mysqli_query($db,"SELECT * FROM messages WHERE id='0'"));
    
// UPDATE CLAN DATA
echo "Updating Clans...";

include_once("clandata.php");

// Determine what a new hordes health would be
// Needs to be done before City data, as it's used by city defense calculations
$row = mysqli_fetch_assoc(mysqli_query($db,'SELECT SUM(vitality) AS value_sum FROM Users WHERE nation != 0')); 
$totvit = $row['value_sum'];
$resulth = mysqli_query($db,"SELECT id, level FROM Users WHERE nation != 0 ORDER BY level DESC, exp DESC LIMIT 1");
$topchar = mysqli_fetch_array($resulth);
echo "\n</br> Top char: ".json_encode($topchar);
echo "\n</br> Top char is: ".$topchar['name']." ".$topchar['lastname'];
$hhealth = $totvit*$topchar['level']/20;
if ($hhealth < 1000) $hhealth = 1000;
echo "\n</br> hhealth is : ".json_encode($hhealth)." \n</br>so therefore max army size is: ".json_encode( $hhealth*1.5);

// UPDATE CLAN BATTLE PARTICIPATION PTS
echo "Updating Clan Battle Participation Points...";
include_once('cb_hourly_update.php');
// UPDATE CITY DATA
echo "Updating Cities...";
include_once('citydata.php');
// UPDATE HORDES
echo "Updating Hordes...";
include_once('hordedata.php');

// DO LAST BATTLE STUFF
echo "Updating Last Battle...";
mysqli_query($db,"LOCK TABLES Hordes WRITE, Contests WRITE, Locations WRITE, Estates WRITE, messages WRITE, Items WRITE, Profs WRITE, Users WRITE;");
// Get the number of Broken Seals
$numBroken = mysqli_num_rows(mysqli_query($db,"SELECT id FROM Items WHERE type=0 AND owner='99999'"));
echo("</br>\nSEALS BROKEN: ".$numBroken);
if ($numBroken >= 7) // if all seals are broken
{
 echo("</br>\nEnough seans broken running last battle"); 
  include_once('lastbattle.php');
} // All seals broken
mysqli_query($db,"UNLOCK TABLES;");

?>