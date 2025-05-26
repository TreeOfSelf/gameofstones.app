<?php

include_once('connect.php');
include_once('questFuncs.php');

$nqcat = 1;
$nqtype = 30;  
$nqofferer = "Green Man";
$nqstart = time()/3600;
$nqlocation = "Anywhere";
$nqrewarded[0]= "T";
$nqrewarded[1]= 0;
$nqrewards = serialize($nqrewarded);


$nqgoals=[];
$nqreq=[];
$nqname="Intro to Escort Quests";
$sloc = "Mayene";
$nqgoals[0] = 6;
$route = mysqli_fetch_array(mysqli_query($db,"SELECT * FROM Routes WHERE start='".$sloc."' AND length >'".$goals[0]."' ORDER BY rand()"));
$nqgoals[1] = $route['id'];
$path = unserialize($route['path']);
$nqgoals[2] = $path[$nqgoals[0]];  
$nqgoal = serialize($nqgoals);
$nqreq['Q'] = 5;
$nqreqs = serialize($nqreq); 
$reward[0] = "LG";
$reward[1] = 20*$goals[0];
$sreward = serialize($reward);
$special[9]="Battle_Track#Intro_to_Escort_Quests";
$sspecial = serialize($special);
$stype = 4;

$sql = "INSERT INTO Quests (id,name,      type,     cat,      offerer,      num_avail, num_done, started,    expire, align, location, reqs,      goals,     special,    reward,    done) 
				  VALUES (6, '$nqname', '$stype', '$nqcat', '$nqofferer', '-1',      0,        '$nqstart', '-1',   0,     '$sloc',  '$nqreqs', '$nqgoal', '$sspecial','$sreward','0')";
$resultt = mysqli_query($db,$sql);

?>