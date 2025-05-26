<?php
include_once("connect.php");
mysqli_query($db,"LOCK TABLES Users WRITE, Locations WRITE, Estates WRITE, Users_stats WRITE;");
$result = mysqli_query($db,"SELECT * FROM Locations WHERE 1");


while ( $listloc = mysqli_fetch_array( $result ) )
{
  if (!$listloc['isDestroyed'])
  {
    // new dice game every 1 minutes
    if (time() - ($listloc['lastdice']*60) >= 60)
    {
      if ($listloc['curr_dice']) $curr_dice = unserialize($listloc['curr_dice']);
      if($curr_dice == "" || is_null($curr_dice)) $curr_dice = array();
      if (count($curr_dice) > 1)
      {
        updateDice($curr_dice, $listloc['wager'], $listloc['gtype']);
        $listloc['prev_dice'] = serialize($curr_dice);
        $listloc['old_wager']=$listloc['wager'];
      }
      $newdice = [];
      for ($x=0; $x<5; $x++) 
      {
        $newdice[$x] = rand(1,6);
      }
      $next_dice['NPC'] = $newdice;
      $curr_dice = serialize($next_dice);
      $listloc['wager'] = pow(10, rand($listloc['minw'],$listloc['maxw']));
	  $listloc['lastdice'] = time()/60;
      mysqli_query($db,"UPDATE Locations SET curr_dice='$curr_dice', prev_dice='$listloc[prev_dice]', wager='$listloc[wager]', old_wager='$listloc[old_wager]', lastdice='$listloc[lastdice]' WHERE id='$listloc[id]'");
    }
  }
}
mysqli_query($db,"UNLOCK TABLES;");

?>