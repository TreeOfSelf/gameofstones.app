<?php

/* establish a connection with the database */
include_once("admin/connect.php");
include_once("admin/userdata.php");
include_once("admin/locFuncs.php");
include_once('map/mapdata/coordinates.inc');

$current_char_location = $char['location']; // Store current location from $char array
$no_query=1;

// Get parameters from request
// $message is escaped as it might be used in SQL or directly output later.
$message_param = isset($_REQUEST['message']) ? mysqli_real_escape_string($db, $_REQUEST['message']) : '';
// $fromLoc_param is kept raw for string comparison.
$fromLoc_param = isset($_REQUEST['fromLoc']) ? $_REQUEST['fromLoc'] : '';
// Numeric parameters are converted to integers.
$toLoc_param = isset($_REQUEST['toLoc']) ? intval($_REQUEST['toLoc']) : -1;
$escortId_param = isset($_REQUEST['escortId']) ? intval($_REQUEST['escortId']) : 0;
$waysId_param = isset($_REQUEST['waysId']) ? intval($_REQUEST['waysId']) : 0;

$surrounding_area = $map_data[$current_char_location]; // Use original current location as key for map_data

// Compare raw fromLoc_param with the cleaned version of character's current location.
// Use integer versions of toLoc_param, escortId_param, waysId_param for numeric comparisons.
if ($fromLoc_param == $current_char_location && (($toLoc_param >= 0 && $toLoc_param < 4) || $escortId_param > 0 || $waysId_param > 0))
{
  // Escape current character location for use in SQL query
  $sql_safe_current_char_loc = mysqli_real_escape_string($db, $current_char_location);
  $result3 = mysqli_query($db,"SELECT * FROM Hordes WHERE done='0' AND location='$sql_safe_current_char_loc'");
  $numhorde = mysqli_num_rows($result3);
  // SET TRAVELING
  if ($travel_mode[$char['travelmode']][1]<=$char['feedneed']) $char['travelmode']=0; // WALK IF HORSE IS TOO HUNGRY
  $newstamina = $char['stamina'];
  if ($char['travelmode']) $char['feedneed']++;
  else {
    if ($debug_mode != true) $newstamina--;
  }
  if ($numhorde && $debug_mode != true) $newstamina = $newstamina-2;
  if ($newstamina < 0) $newstamina = 0;

  $next_destination_loc = ''; // Initialize variable for the next location

  if ($toLoc_param > -1)
  {
    $next_destination_loc = $surrounding_area[$toLoc_param];
  }
  else if ($escortId_param > 0)
  { 
    $myquests= unserialize($char['quests']);
    $quest = mysqli_fetch_array(mysqli_query($db,"SELECT * FROM Quests WHERE id='$escortId_param'"));
    $goals = unserialize($quest['goals']);
    $route = mysqli_fetch_array(mysqli_query($db,"SELECT * FROM Routes WHERE id='".$goals[1]."'"));
    $rpath = unserialize($route['path']);
    $next_destination_loc = $rpath[$myquests[$escortId_param][1]+1];
    $myquests[$escortId_param][1] += 1;
    $myquests[$escortId_param][2] = 0;
    $myquests2 = serialize($myquests);
    $char['quests'] = $myquests2;
    mysqli_query($db,"UPDATE Users_data SET quests='".$myquests2."' WHERE id='$id'");
	
  }
  else if ($waysId_param > 0 && $waysId_param == $char['route'])
  {
    $route = mysqli_fetch_array(mysqli_query($db,"SELECT * FROM Routes WHERE id='".$waysId_param."'"));
    $rpath = unserialize($route['path']);
    $next_destination_loc = $rpath[$char['routepoint']+1];
    $char['routepoint'] = $char['routepoint']+1;
    if ($char['routepoint'] >= $route['length']-1)
    {
      mysqli_query($db,"UPDATE Users SET route='0', routepoint='0' WHERE id='$id'");
      mysqli_query($db,"UPDATE Users_stats SET ways_use= ways_use + 1 WHERE id='$id'");
    }
    else
    {
      mysqli_query($db,"UPDATE Users SET routepoint='".$char['routepoint']."' WHERE id='$id'");
    }
  }
  
  if ($next_destination_loc && $next_destination_loc != $current_char_location)
  {
    $char['location'] = $next_destination_loc; // Update $char array for current script execution
    // mysqli_real_escape_string is correctly used here for the new location value
    mysqli_query($db,"UPDATE Users SET stamina='".$newstamina."', feedneed='".$char['feedneed']."', location='".mysqli_real_escape_string($db, $next_destination_loc)."' WHERE id='$char[id]'");
  }
}

$loc_name = $char['location']; // This will be the new location if travel occurred, otherwise original
if ($location_array[$char['location']][2]) $wikilink = "Cities";
else $wikilink = "Wilderness+Areas";

include("map/places/banker.php");

if (!$message_param) $message = $loc_name; // If no request message, use loc_name. $message is used by header.php
else $message = $message_param; // Otherwise, use the (escaped) message from request.

include('header.php');
?>

<?php
  $gop=0;
  $bg = "";
  if ($char['arrival']<=time() && $location_array[$char['location']][2]) 
  {
    $link1 = "town.php";
    $town_img_name = str_replace(' ','_',strtolower($char['location']));
    if ($mode != 1) $bg = "background-image:url('images/townback/".$town_img_name.".jpg'); ";
  } 
  elseif ($char['arrival']<=time()) 
  {
    $link1 = "wild.php";
  }  
  else
  { 
    $gop =1;
    $link1 = "";

  }  
?>            
  <div class="row solid-back">
    <div class="col-sm-12">
      <div id="TownMap" name="TownMap" height="1000" marginwidth="0" marginheight="0" scrolling="no" frameborder="0" onload='adjustMyFrameHeight();'>
              <?php 
                if (!$gop) include ($link1); ?>
      </div>
    </div>
  </div>

<noscript>
Your browser will not currently run Javascript, which is required for this site.<br>I would strongly advise you turn it on and download the <a href="http://www.getfirefox.com">Firefox</a> web browser.
</noscript>

<?php
include('footer.htm');
?>