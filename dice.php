<?php

/* establish a connection with the database */


include_once("admin/connect.php");
include_once("admin/userdata.php");

$game = mysqli_real_escape_string($db,$_GET['game']);
$buyin = mysqli_real_escape_string($db,$_POST['buyin']);
$dwager = mysqli_real_escape_string($db,$_POST['dwager']);

$message="<b>Tavern Dice Games</b>";
$list = [];
$characterName = $char['name']." ".$char['lastname'];
$p_name = array("One","Two","Three","Four","Five");
$p_info = [];
$winning_score = 0;
$winners = 0;
$text = '';
$query = "SELECT * FROM Locations WHERE name='$char[location]'";
$result = mysqli_query($db,$query);
$location = mysqli_fetch_array($result);
if ($location['curr_dice']){ 
	$curr_dice = unserialize($location['curr_dice']);
}
if ($location['prev_dice']){
	$prev_dice = unserialize($location['prev_dice']);
}
$wager = $location['wager'];
$gtype = $location['gtype'];


if ($buyin)
{
  if (($wager > $char['gold'] || $wager <= 0)) {$message="Why don't you put your money where your mouth is?";} 
  elseif( $dwager != $wager) {$message="The wager amount changed while you held the dice. Toss again.";}
  elseif (!$curr_dice[$characterName])
  {
    $newdice = [];
    for ($x=0; $x<5; $x++) 
    {
      $newdice[$x] = rand(1,6);
    }
    $curr_dice[$characterName] = $newdice;
    $new_dice = serialize($curr_dice);
    $query = "UPDATE Locations SET curr_dice='$new_dice' WHERE name='$char[location]'"; 
    $result = mysqli_query($db,$query);
    $gold = $char['gold']-$wager;
    $query2 = "UPDATE Users SET gold='$gold' WHERE id='$char[id]'";
    $result2 = mysqli_query($db,$query2);
    $char['gold']=$gold;
    $ustats = mysqli_fetch_array(mysqli_query($db,"SELECT * FROM Users_stats WHERE id='".$id."'"));
    $ustats['dice_earn'] -= $wager;
    $result = mysqli_query($db,"UPDATE Users_stats SET dice_earn='".$ustats['dice_earn']."' WHERE id='".$id."'");
    //updateDice($curr_dice,$wager,$gtype);
  }
  else { $message = "You are already in this game! Wait for the next one to start."; }
}

// PREVIOUS RESULTS
if ($prev_dice)
{
 $owager = $location['old_wager'];
 // COMPUTE WINNER
 foreach ($prev_dice as $p => $a)
 {
  for ($x=0; $x<5; $x++)
  {
   $list[$p][$a[$x]-1] += 1;
  }
  $p_info[$p] = ComputeScore($list[$p],$gtype);
  if ($p_info[$p] > $winning_score) $winning_score=$p_info[$p];
 }
//  print_r($p_info);exit;
 // DISPLAY
 $text .= "<center><font class='littletext'><b>Previous Game Results:</b></font>";
 $text .= "<font class='littletext' style='font-size: 14px'>";
 $text .= "<table border='0' cellpadding='5' cellspacing='0'>";
 foreach ($prev_dice as $p => $a)
 {
  $text .= "<tr><td><font class='littletext'>";
  if ($p == "NPC") $text .= "<i>".str_replace('-ap-','&#39;',$char['location'])." Gambler's Roll: </i>"; 
  else $text .= "<i>".$p."'s Roll</i>";
  $text .= "</td><td width='5'></td><td><table rules='none' border='";
  if ($p_info[$p] == $winning_score) {$text .= "2"; $winners++;}
  else $text .= "0";
  $text .= "' bordercolor='ED3915' cellpadding='2' cellspacing='0'>";
  for ($x=0; $x<5; $x++) $text .= "<td><img src='dice/d".$gtype.$prev_dice[$p][$x].".gif'></td>";
  $text .= "</table></td></tr>";
 }
 $winnings = intval(($owager*count($prev_dice))/$winners);
 $text .= "<tr><td colspan='3'><center><font class='littletext'><i><b>This round was worth </i>".displayGold($winnings);
 if ($winners>1) $text .= "<i> - $winners way tie</i>";
 $text .= "</td></tr></table><br><br>";
}
// DISPLAY
include('header.php');
?>


<center>

<table border=0 cellpadding=4 cellspacing=0>
 <form method="post" action="dice.php">
  <tr><td rowspan='5'><img src='dice/dice.gif'></td>
    <td>&nbsp;<input type="hidden" name="buyin" id="buyin" value="1" class="form"><input type="hidden" name="dwager" id="dwager" value="<?php echo $wager;?>" class="form"></td></tr>
  <tr><td valign=center>
    <?php
      if (!$curr_dice[$characterName]) {
    ?>
      <center><input type="Submit" name="submit" value="Toss the Dice" class="btn btn-sm btn-success">
    <?php
      } else {
        $text2 = "<font class='littletext'>";
        $text2 .= "<i>".$characterName."'s Roll: </i>";
        for ($x=0; $x<5; $x++) $text2 .= "<img src='dice/d".$gtype.$curr_dice[$characterName][$x].".gif'>";
        echo $text2;
      }
    ?>
  </td></tr>
  <tr><td><font class='littletext'><b>Wager</b>: <?php echo displayGold($wager);?></td></tr>
  
  
  <tr><td><font class='littletext'><b>Players so far: <?php
  
  if(is_array($curr_dice)){
	echo count($curr_dice);
  }else{
	echo 0;
  }
  
  ?>
  <tr><td><font class='littletext'><b>Pot size: <?php
  
  if(is_array($curr_dice)){
	echo displayGold(count($curr_dice)*$wager);
  }else{
	echo displayGold(0);
  }
  
  
  ?>  
  
  
 </form>
</table>
<br><br><br>
<?php
echo $text;
?>
<?php
  include('footer.htm');
?>
