<?php
include_once("connect.php");
include_once("displayFuncs.php");
$lastBattleDone = mysqli_num_rows(mysqli_query($db,"SELECT id FROM Contests WHERE type='99' AND done='1'"));


mysqli_query($db,"LOCK TABLES Users WRITE, Users_stats WRITE, messages WRITE, Profs WRITE, Estates WRITE, Soc WRITE, Soc_stats WRITE;");
if (!$lastBattleDone)
{
 echo "</br>\nstarting heroes update";
  $heroes = [];
  $herids = "";
  
  // Update stats of all characters
  
  
  $result = mysqli_query($db,"SELECT Users.*, Users_stats.* FROM Users LEFT JOIN Users_stats ON Users.id=Users_stats.id WHERE (Users.nation!='0')");
  while ( $listchar = mysqli_fetch_array( $result ) )
  {
    $listchar['coin']= isset($listchar['gold']) ? intval($listchar['gold']) : 0;
    $listchar['xp']= isset($listchar['exp']) ? intval($listchar['exp']) : 0;
    $enum = 0;
    $elvl = 0;
    $tot_estate = 0;
    $estquery = mysqli_query($db,"SELECT id, owner, value, level FROM Estates WHERE owner='".$listchar['id']."' ORDER BY value DESC");

      while ($tmpEstate = mysqli_fetch_array($estquery))
      {
        if (!$enum)
        {
          $listchar['top_estate']=$tmpEstate['value'];
        }
        if ($tmpEstate['level'] > $elvl) $elvl = $tmpEstate['level']; 
        $tot_estate += $tmpEstate['value'];
        $enum++;
      }
	
    if ($enum) $elvl++;
    $listchar['num_estates']=$enum;
    $listchar['highest_estate']=$elvl;
    $listchar['tot_estate']=$tot_estate;
    $bnum = 0;
    $tot_business = 0;
    $bquery = mysqli_query($db,"SELECT id, owner, value FROM Profs WHERE owner='".$listchar['id']."' ORDER BY value DESC");
	
    while ($tmpBiz = mysqli_fetch_array($bquery))
    {
      if (!$bnum)
      {
        $listchar['top_business']=$tmpBiz['value'];
      }
      $tot_business += $tmpBiz['value'];
      $bnum++;
    }
	
    $listchar['num_biz']=$bnum;
    $listchar['tot_business']=$tot_business;
    $listchar['net_worth'] = $listchar['tot_business'] + $listchar['tot_estate'] + $listchar['coin'] + (isset($listchar['bankcoin']) ? intval($listchar['bankcoin']) : 0);

    // Ensure all values are at least 0 if not set, or properly cast
    $top_estate_val = isset($listchar['top_estate']) ? intval($listchar['top_estate']) : 0;
    $tot_estate_val = isset($listchar['tot_estate']) ? intval($listchar['tot_estate']) : 0;
    $top_business_val = isset($listchar['top_business']) ? intval($listchar['top_business']) : 0;
    $tot_business_val = isset($listchar['tot_business']) ? intval($listchar['tot_business']) : 0;
    $net_worth_val = isset($listchar['net_worth']) ? intval($listchar['net_worth']) : 0;
    $num_estates_val = isset($listchar['num_estates']) ? intval($listchar['num_estates']) : 0;
    $highest_estate_val = isset($listchar['highest_estate']) ? intval($listchar['highest_estate']) : 0;

    mysqli_query($db,"UPDATE Users_stats SET coin='".$listchar['coin']."', xp='".$listchar['xp']."', top_estate='".$top_estate_val."', tot_estate='".$tot_estate_val."', top_business='".$top_business_val."', tot_business='".$tot_business_val."', net_worth='".$net_worth_val."', num_estates='".$num_estates_val."', highest_estate='".$highest_estate_val."' WHERE id='".$listchar['id']."'");
 }
    
  // loop over all ranks and deterime top 10. If LB is has started, don't update coin ranks.
  $cityRumors = mysqli_fetch_array(mysqli_query($db,"SELECT * FROM messages WHERE id='50000'"));
  $hoursFromBreak = intval(((time()/3600)-$cityRumors['checktime']));
  $loops =count($rank_data);
  if ($cityRumors['checktime'] != 0 && $hoursFromBreak >= 720) 
  { 
    $loops = $loops;
  }

  for ($y = 0; $y < $loops; $y++)
  {
    $rank_by=$rank_data[$y][0];
    $x=0;
    $query_string = "SELECT Users.*, Users_stats.* FROM Users LEFT JOIN Users_stats ON Users.id=Users_stats.id WHERE (Users.nation!='0') ORDER BY ".$rank_by." DESC, exp DESC LIMIT 0,10 ";
    $result = mysqli_query($db, $query_string);
	$numchar = mysqli_num_rows($result);
    while ( $listchar = mysqli_fetch_array( $result ) )
    {
      // Ensure that the value for $rank_by exists and is an integer, default to 0 otherwise
      $heroes[$x+1][$rank_by] = isset($listchar[$rank_by]) ? intval($listchar[$rank_by]) : 0;
      $heroes[$x+11][$rank_by] = $listchar['id'];
      $x++;
    }
  }
  // updated the database for the top 10 for each rank (10 for number and 10 for the id)
  if ($numchar > 0 )
  {
    for ($z=1; $z<=20; $z++)
    {
      // Initialize all hero stats to 0 if not set to prevent errors with non-integer values
      $h_xp = isset($heroes[$z]['xp']) ? intval($heroes[$z]['xp']) : 0;
      $h_ji = isset($heroes[$z]['ji']) ? intval($heroes[$z]['ji']) : 0;
      $h_achieved = isset($heroes[$z]['achieved']) ? intval($heroes[$z]['achieved']) : 0;
      $h_wins = isset($heroes[$z]['wins']) ? intval($heroes[$z]['wins']) : 0;
      $h_duel_wins = isset($heroes[$z]['duel_wins']) ? intval($heroes[$z]['duel_wins']) : 0;
      $h_enemy_wins = isset($heroes[$z]['enemy_wins']) ? intval($heroes[$z]['enemy_wins']) : 0;
      $h_ally_wins = isset($heroes[$z]['ally_wins']) ? intval($heroes[$z]['ally_wins']) : 0;
      $h_off_wins = isset($heroes[$z]['off_wins']) ? intval($heroes[$z]['off_wins']) : 0;
      $h_npc_wins = isset($heroes[$z]['npc_wins']) ? intval($heroes[$z]['npc_wins']) : 0;
      $h_shadow_wins = isset($heroes[$z]['shadow_wins']) ? intval($heroes[$z]['shadow_wins']) : 0;
      $h_military_wins = isset($heroes[$z]['military_wins']) ? intval($heroes[$z]['military_wins']) : 0;
      $h_ruffian_wins = isset($heroes[$z]['ruffian_wins']) ? intval($heroes[$z]['ruffian_wins']) : 0;
      $h_channeler_wins = isset($heroes[$z]['channeler_wins']) ? intval($heroes[$z]['channeler_wins']) : 0;
      $h_animal_wins = isset($heroes[$z]['animal_wins']) ? intval($heroes[$z]['animal_wins']) : 0;
      $h_exotic_wins = isset($heroes[$z]['exotic_wins']) ? intval($heroes[$z]['exotic_wins']) : 0;
      $h_quests_done = isset($heroes[$z]['quests_done']) ? intval($heroes[$z]['quests_done']) : 0;
      $h_play_quests_done = isset($heroes[$z]['play_quests_done']) ? intval($heroes[$z]['play_quests_done']) : 0;
      $h_find_quests_done = isset($heroes[$z]['find_quests_done']) ? intval($heroes[$z]['find_quests_done']) : 0;
      $h_npc_quests_done = isset($heroes[$z]['npc_quests_done']) ? intval($heroes[$z]['npc_quests_done']) : 0;
      $h_item_quests_done = isset($heroes[$z]['item_quests_done']) ? intval($heroes[$z]['item_quests_done']) : 0;
      $h_horde_quests_done = isset($heroes[$z]['horde_quests_done']) ? intval($heroes[$z]['horde_quests_done']) : 0;
      $h_escort_quests_done = isset($heroes[$z]['escort_quests_done']) ? intval($heroes[$z]['escort_quests_done']) : 0;
      $h_my_quests_done = isset($heroes[$z]['my_quests_done']) ? intval($heroes[$z]['my_quests_done']) : 0;
      $h_coin = isset($heroes[$z]['coin']) ? intval($heroes[$z]['coin']) : 0;
      $h_bankcoin = isset($heroes[$z]['bankcoin']) ? intval($heroes[$z]['bankcoin']) : 0;
      $h_coin_donated = isset($heroes[$z]['coin_donated']) ? intval($heroes[$z]['coin_donated']) : 0;
      $h_duel_earn = isset($heroes[$z]['duel_earn']) ? intval($heroes[$z]['duel_earn']) : 0;
      $h_dice_earn = isset($heroes[$z]['dice_earn']) ? intval($heroes[$z]['dice_earn']) : 0;
      $h_item_earn = isset($heroes[$z]['item_earn']) ? intval($heroes[$z]['item_earn']) : 0;
      $h_quest_earn = isset($heroes[$z]['quest_earn']) ? intval($heroes[$z]['quest_earn']) : 0;
      $h_prof_earn = isset($heroes[$z]['prof_earn']) ? intval($heroes[$z]['prof_earn']) : 0;
      $h_top_estate = isset($heroes[$z]['top_estate']) ? intval($heroes[$z]['top_estate']) : 0;
      $h_tot_estate = isset($heroes[$z]['tot_estate']) ? intval($heroes[$z]['tot_estate']) : 0;
      $h_top_business = isset($heroes[$z]['top_business']) ? intval($heroes[$z]['top_business']) : 0;
      $h_tot_business = isset($heroes[$z]['tot_business']) ? intval($heroes[$z]['tot_business']) : 0;
      $h_net_worth = isset($heroes[$z]['net_worth']) ? intval($heroes[$z]['net_worth']) : 0;
      $h_align_high = isset($heroes[$z]['align_high']) ? intval($heroes[$z]['align_high']) : 0;
      $h_align_low = isset($heroes[$z]['align_low']) ? intval($heroes[$z]['align_low']) : 0;
      $h_win_tourney = isset($heroes[$z]['win_tourney']) ? intval($heroes[$z]['win_tourney']) : 0;
      $h_horde_wins = isset($heroes[$z]['horde_wins']) ? intval($heroes[$z]['horde_wins']) : 0;
      $h_army_wins = isset($heroes[$z]['army_wins']) ? intval($heroes[$z]['army_wins']) : 0;

      mysqli_query($db,"UPDATE Users_stats SET xp='".$h_xp."', ji='".$h_ji."', achieved='".$h_achieved."', wins='".$h_wins."', duel_wins='".$h_duel_wins."', enemy_wins='".$h_enemy_wins."', ally_wins='".$h_ally_wins."', off_wins='".$h_off_wins."', npc_wins='".$h_npc_wins."', shadow_wins='".$h_shadow_wins."', military_wins='".$h_military_wins."', ruffian_wins='".$h_ruffian_wins."', channeler_wins='".$h_channeler_wins."', animal_wins='".$h_animal_wins."', exotic_wins='".$h_exotic_wins."', quests_done='".$h_quests_done."', play_quests_done='".$h_play_quests_done."', find_quests_done='".$h_find_quests_done."', npc_quests_done='".$h_npc_quests_done."', item_quests_done='".$h_item_quests_done."', horde_quests_done='".$h_horde_quests_done."', escort_quests_done='".$h_escort_quests_done."', my_quests_done='".$h_my_quests_done."', coin='".$h_coin."', bankcoin='".$h_bankcoin."', coin_donated='".$h_coin_donated."', duel_earn='".$h_duel_earn."', dice_earn='".$h_dice_earn."', item_earn='".$h_item_earn."', quest_earn='".$h_quest_earn."', prof_earn='".$h_prof_earn."', top_estate='".$h_top_estate."', tot_estate='".$h_tot_estate."', top_business='".$h_top_business."', tot_business='".$h_tot_business."', net_worth='".$h_net_worth."', align_high='".$h_align_high."', align_low='".$h_align_low."', win_tourney='".$h_win_tourney."', horde_wins='".$h_horde_wins."', army_wins='".$h_army_wins."' WHERE id='".($z+10000)."'");
	}
  }
}
mysqli_query($db,"UNLOCK TABLES;");

// Update Soc Stats
$msgs = mysqli_fetch_array(mysqli_query($db,"SELECT * FROM messages WHERE id='0'"));
if ($msgs['checktime'] < floor(time()/600) && !$lastBattleDone)
{
  for ($y = 0; $y < count($soc_rank_data); $y++)
  {

    $rank_by=$soc_rank_data[$y][0];
    $field = $rank_by;
    $orderBy = $rank_by." DESC";
    if (substr($rank_by, 0, 1) === "-") 
    { 
      $field = substr($rank_by, 1, strlen($rank_by));
      $orderBy =$field." ASC";
    }
    $x=0;
    $result = mysqli_query($db,"SELECT * FROM Soc WHERE 1 ORDER BY ".$orderBy.", id ASC LIMIT 0,10 ");
    $numsoc = mysqli_num_rows($result);
    while ( $listsoc = mysqli_fetch_array( $result ) )
    {
      $heroes[$x+1][$rank_by]=$listsoc[$field];
      $heroes[$x+11][$rank_by]=$listsoc['id'];
      $x++;
    }
  }
  // updated the database for the top 10 for each rank
  if ($numsoc > 0 )
  {
    for ($z=1; $z<=10; $z++)
    {
      // Check if values exist and convert to integers to avoid empty strings
      $score = isset($heroes[$z]['score']) ? intval($heroes[$z]['score']) : 0;
      $scoreId = isset($heroes[$z+10]['score']) ? intval($heroes[$z+10]['score']) : 0;
      $members = isset($heroes[$z]['members']) ? intval($heroes[$z]['members']) : 0;
      $membersId = isset($heroes[$z+10]['members']) ? intval($heroes[$z+10]['members']) : 0;
      $ruled = isset($heroes[$z]['ruled']) ? intval($heroes[$z]['ruled']) : 0;
      $ruledId = isset($heroes[$z+10]['ruled']) ? intval($heroes[$z+10]['ruled']) : 0;
      $bank = isset($heroes[$z]['bank']) ? intval($heroes[$z]['bank']) : 0;
      $bankId = isset($heroes[$z+10]['bank']) ? intval($heroes[$z+10]['bank']) : 0;
      $align = isset($heroes[$z]['align']) ? intval($heroes[$z]['align']) : 0;
      $alignId = isset($heroes[$z+10]['align']) ? intval($heroes[$z+10]['align']) : 0;
      $lowAlign = isset($heroes[$z]['-align']) ? intval(0-$heroes[$z]['-align']) : 0;
      $lowAlignId = isset($heroes[$z+10]['-align']) ? intval($heroes[$z+10]['-align']) : 0;
      
      mysqli_query($db,"UPDATE Soc_stats SET mostJiNum='".$score."', mostJiId='".$scoreId."', mostMembersNum='".$members."', mostMembersId='".$membersId."', mostRuledNum='".$ruled."', mostRuledId='".$ruledId."', mostCoinNum='".$bank."', mostCoinId='".$bankId."', highAlignNum='".$align."', highAlignId='".$alignId."', lowAlignNum='".$lowAlign."', lowAlignId='".$lowAlignId."' WHERE id='".($z+10000)."'");
    }
  }
}

?>