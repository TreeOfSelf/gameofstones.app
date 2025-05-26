<?php
  include_once("connect.php");
   include_once('displayFuncs.php');
  include_once('busiFuncs.php');
$numBroken = mysqli_num_rows(mysqli_query($db,"SELECT id FROM Items WHERE type=0 AND owner='99999'"));


function destroyCity($listloc)
{
  global $db;

  mysqli_query($db,"UPDATE Locations SET isDestroyed='1' WHERE id='" . $listloc['id'] . "'"); 
  
  echo "</br>\nDestroyed City: ".json_encode($listloc)." | ";
  
  
  // Destory all businesses in that city

  
  $destroyedBiz = mysqli_num_rows(mysqli_query($db,"SELECT id FROM Profs WHERE location='" . $listloc['name'] . "'"));


  mysqli_query($db,"DELETE FROM Profs WHERE location='" . $listloc['name'] . "'");

  echo "</br>\n Destroyed biz: ".$destroyedBiz." | ";
       
  
  // Update City Rumors
  $cityRumors = mysqli_fetch_array(mysqli_query($db,"SELECT * FROM messages WHERE id='50000'"));
  $rumorMessages = unserialize($cityRumors['message']);
  $numRumors = count($rumorMessages);
        
  $myMsg = "<" . $listloc['name'] . "_" . time() . ">` " . $listloc['name'] . " has been destroyed! " . $destroyedBiz . " businesses lost!|";
  $rumorMessages[$numRumors] = $myMsg;  
  $rumorMessages = pruneMsgs($rumorMessages, 50);
  $newRumors = serialize($rumorMessages);

   mysqli_query($db,"UPDATE messages SET message='$newRumors' WHERE id='50000'");
  
}

function destroyAttackedCity($myHorde)
{
  global  $db;
  


    mysqli_query($db,"UPDATE Locations SET isDestroyed='1' WHERE name='" . $myHorde['target'] . "'");

    echo "</br>\n Destroyed City: " . $myHorde['target'] . " | ";
  
          
  // Destory all businesses in that city
  $destroyedBiz = mysqli_num_rows(mysqli_query($db,"SELECT id FROM Profs WHERE location='" . $myHorde['target'] . "'"));

	mysqli_query($db,"DELETE FROM Profs WHERE location='" . $myHorde['target'] . "'");

	echo "</br>\n Destroyed biz: " . $destroyedBiz . " | ";
  
                
  // Destroy all estates in the wilderness
  $destroyedEstates = mysqli_num_rows(mysqli_query($db,"SELECT id FROM Estates WHERE location='" . $myHorde['location'] . "'"));

    mysqli_query($db,"DELETE FROM Estates WHERE location='" . $myHorde['location'] . "'");
  
 
    echo "</br>\n Destroyed estates: " . $destroyedEstates . " | ";
  
          
  // Update City Rumors
  $cityRumors = mysqli_fetch_array(mysqli_query($db,"SELECT * FROM messages WHERE id='50000'"));
  $rumorMessages = unserialize($cityRumors['message']);
    
  $numRumors = count($rumorMessages);      
  $myMsg = "<" . $myHorde['target'] . "_" . time() . ">` " . $myHorde['target'] . " has been destroyed! " . $destroyedBiz . " businesses lost!|";
  $rumorMessages[$numRumors] = $myMsg;

  $numRumors = count($rumorMessages);      
  $myMsg = "<" . $myHorde['location'] . "_" . time() . ">` " . $destroyedEstates . " Estates have been destroyed in " . $myHorde['location'] . "!|";
  $rumorMessages[$numRumors] = $myMsg;
            
  $rumorMessages = pruneMsgs($rumorMessages, 50);
  $newRumors = serialize($rumorMessages);

   mysqli_query($db,"UPDATE messages SET message='$newRumors' WHERE id='50000'");
  
}

function createMegaHorde($htown, $tnpc, $hhealth, $hstime, $hwild, $timesDefended)
{ 


  global $map_data, $horde_types,  $npc_list, $npc_count, $city_defenses, $db; 
  
  $htype = 3;
  $surrounding_area = $map_data[$htown['name']];

  // If we don't have a wilderness, find one.
  $w=0;
  while ($hwild == '' && $w < 4)
  {
    $hwild = $surrounding_area[$w];
    $inSameWild = mysqli_num_rows(mysqli_query($db,"SELECT id FROM Hordes WHERE type='3' AND location='" . $hwild . "' AND done<2"));
    if ($inSameWild)  $hwild = '';
    $w++;
  }
  
  // ensure our horde isn't a bubble of evil
  while ($tnpc == "Bubble of Evil")
  {
    $tnpc = $npc_list[rand(0,$npc_count-1)];
  }

  $hnpcs[0] = array($tnpc, $hhealth*(2-($timesDefended/2)));
  $hnpcs[1] = array($city_defenses[$htown['name']], $htown['army']);
  $shnpcs = serialize($hnpcs);
  $hetime = $hstime+30;
  $hntime = $hstime+30+24;
  echo "</br> CREATING HORDE </br>";
  echo "Start: ";
  echo $hetime;
  echo "</br>";
  echo "End: ";
  echo $hntime;
  echo "</br>";
  echo "Type: ";
  echo $tnpc;
  echo "</br>";
  $empty = json_encode(array());
  if ($hwild != "" && $htown['name'])
  {
    $sql = "INSERT INTO Hordes (type, location, target,         starts,    ends,      next,      done, npcs, users) 
                        VALUES ('3',  '" . $hwild . "', '" . $htown['name'] . "', '" . $hstime . "', '" . $hetime . "', '" . $hntime . "', 0,    '" . $shnpcs . "', '" . $empty . "')";

      $resultt = mysqli_query($db,$sql);
  
   
      echo "</br>\n New Horde of " . $hnpcs[0][0] . " targeting " . $htown['name'] . " from " . $hwild . "! ";
    
  
        
  // Update City Rumors
  $cityRumors = mysqli_fetch_array(mysqli_query($db,"SELECT * FROM messages WHERE id='50000'"));
  $rumorMessages = unserialize($cityRumors['message']);
  $numRumors = count($rumorMessages);
          
  $myMsg = "<" . $htown['name'] . "_" . time() . ">` A " . $horde_types[$hnpcs[0][0]] . " of " . $hnpcs[0][0] . "s is gathering in " . $hwild . "!|";
  $rumorMessages[$numRumors] = $myMsg;  
  $rumorMessages = pruneMsgs($rumorMessages, 50);
  $newRumors = serialize($rumorMessages);

   mysqli_query($db,"UPDATE messages SET message='$newRumors' WHERE id='50000'");

  }
  
}






if ($numBroken >= 7) // if all seals are broken
{
  echo "</br>\n Last battle running";




	$row = mysqli_fetch_assoc(mysqli_query($db,'SELECT SUM(vitality) AS value_sum FROM Users WHERE nation != 0')); 
	$totvit = $row['value_sum'];
	$resulth = mysqli_query($db,"SELECT id, level FROM Users WHERE nation != 0 ORDER BY level DESC, exp DESC LIMIT 1");
	$topchar = mysqli_fetch_array($resulth);
	$hhealth = $totvit*$topchar['level']/20;
	if ($hhealth < 1000) $hhealth = 1000;


  $cityRumors = mysqli_fetch_array(mysqli_query($db,"SELECT * FROM messages WHERE id='50000'"));
  

  $hoursFromBreak = intval(((time()/3600)-$cityRumors['checktime']));

  

  
  // NOTE: If these numbers change, update the Seals tab on rumors.php page.
  $p1Start = 720;
  $p2Start = 774;
  $p3Start = 828;
  $p1End = 750;
  $p2End = 804;
  $p3End = 858;
  $LBEnd = 888;

    echo ("\n</br>");
  echo ("Hours from break: " . $hoursFromBreak);
  echo ("\n</br>");
  echo ("P1 " . $p1Start . "/" . $p1End);
   echo ("\n</br>");
  echo ("P2 " . $p2Start . "/" . $p2End);
   echo ("\n</br>");
  echo ("P3 " . $p3Start . "/" . $p3End);
    echo ("\n</br>");

  // If 30 days have past since first seal break and no cities have been destroyed, kick off phase 1,
  $result = mysqli_query($db,"SELECT id FROM Locations WHERE isDestroyed='0' ORDER BY myOrder ASC");
  $citiesLeft = mysqli_num_rows($result);
  
  $isBattle = mysqli_num_rows(mysqli_query($db,"SELECT id FROM Contests WHERE type='99'"));
  

  if ($hoursFromBreak >= $p1Start)
  {
    // Start the Last Battle!
    if ($isBattle == 0) // if the battle hasn't been started yet and the time has come, make it.
    {
		echo ("\n</br> PHASE ONE STARTED \n</br>");
      $ttype = 99;
      $minp = 0;
      $maxp = 0;
      $pduels = 0;
      $tfee = 0;
      $pdistro = 99;

      $tstarts = $cityRumors['checktime'] + $p1Start;
      $tends = $cityRumors['checktime'] + $LBEnd;

      // calculate the points earned by each side so far.
      $lpoints = 0;
      $spoints = 0;
      
       // points for remaining or destroyed cities
      $reresult = mysqli_query($db,"SELECT id, ruler FROM Locations WHERE isDestroyed='0'");
      while ($remcity = mysqli_fetch_array($reresult))
      {
        // Check what the alignment of the city's ruler is.
        $soc = mysqli_fetch_array(mysqli_query($db,"SELECT id, name, members, align FROM `Soc` WHERE name='" . $remcity['ruler'] . "'"));
        $rulerAlign = getClanAlignment($soc['align'], $soc['members']);
      
        if ($rulerAlign > 0) $lpoints += 500; // 500 to Light for each remaining city ruled by a Light clan
        else if ($rulerAlign < 0) $spoints += 500; // 500 to Shadow for each remaining city ruled by a Shadow clan
      }
      #echo "l: ".$lpoints."; s: ".$spoints."|";
      
      // 1000 points for the side with most members (ties goes to Shadow)
      $lusers =  mysqli_num_rows(mysqli_query($db,"SELECT id FROM Users WHERE align > '300'"));
      $susers =  mysqli_num_rows(mysqli_query($db,"SELECT id FROM Users WHERE align < '-300'"));
      if ($lusers > $susers)  $lpoints += 1000;
      else $spoints += 1000;
      #echo "l: ".$lusers."; s: ".$susers."|";
      #echo "l: ".$lpoints."; s: ".$spoints."|";
    
      // 1000 points for highest level player
      $topExp = mysqli_fetch_array(mysqli_query($db,"SELECT id, align FROM Users WHERE nation!='0' ORDER BY exp DESC LIMIT 0,1 "));
      if ($topExp['align'] > 0) $lpoints += 1000;
      else $spoints += 1000;
      #echo "l: ".$lpoints."; s: ".$spoints."|";
      
      // 1000 points for most ji player
      $topJi = mysqli_fetch_array(mysqli_query($db,"SELECT * FROM Users LEFT JOIN Users_stats ON Users.id=Users_stats.id WHERE (Users.nation!='0') ORDER BY ji DESC, exp DESC LIMIT 0,1"));
      if ($topJi['align'] > 0) $lpoints += 1000;
      else $spoints += 1000;
      #echo "l: ".$lpoints."; s: ".$spoints."|";
            
      // 1000 points for most achieved player
      $topAchieve = mysqli_fetch_array(mysqli_query($db,"SELECT * FROM Users LEFT JOIN Users_stats ON Users.id=Users_stats.id WHERE (Users.nation!='0') ORDER BY achieved DESC, exp DESC LIMIT 0,1"));
      if ($topAchieve['align'] > 0) $lpoints += 1000;
      else $spoints += 1000;
      #echo "l: ".$lpoints."; s: ".$spoints."|";

      // 1000 points for most wins player
      $topWins = mysqli_fetch_array(mysqli_query($db,"SELECT * FROM Users LEFT JOIN Users_stats ON Users.id=Users_stats.id WHERE (Users.nation!='0') ORDER BY wins DESC, exp DESC LIMIT 0,1"));
      if ($topWins['align'] > 0) $lpoints += 1000;
      else $spoints += 1000;
      #echo "l: ".$lpoints."; s: ".$spoints."|";
 
      // 1000 points for highest net worth player
      $topWorth = mysqli_fetch_array(mysqli_query($db,"SELECT * FROM Users LEFT JOIN Users_stats ON Users.id=Users_stats.id WHERE (Users.nation!='0') ORDER BY net_worth DESC, exp DESC LIMIT 0,1"));
      if ($topWorth['align'] > 0) $lpoints += 1000;
      else $spoints += 1000;
      #echo "l: ".$lpoints."; s: ".$spoints."|"; 

      // 1000 points for most quests complete
      $topQuests = mysqli_fetch_array(mysqli_query($db,"SELECT * FROM Users LEFT JOIN Users_stats ON Users.id=Users_stats.id WHERE (Users.nation!='0') ORDER BY quests_done DESC, exp DESC LIMIT 0,1"));
      if ($topQuests['align'] > 0) $lpoints += 1000;
      else $spoints += 1000;
      #echo "l: ".$lpoints."; s: ".$spoints."|";
      
      // 1000 points for clan with the most Ji
      $topJiClan = mysqli_fetch_array(mysqli_query($db,"SELECT id, members, align FROM Soc WHERE 1 ORDER BY score DESC LIMIT 0,1 "));
      $socAlign = getClanAlignment($topJiClan['align'], $topJiClan['members']);
      if ($socAlign > 0) $lpoints += 1000;
      else if ($socAlign < 0) $spoints += 1000;
      #echo "l: ".$lpoints."; s: ".$spoints."|";
        
      // Create Last Battle
      $contestants = array(array());
      $contestants[-1][0] = "Shadow";
      $contestants[-1][1] = $spoints;
      $contestants[1][0] = "Light";
      $contestants[1][1] = $lpoints;       
      $sc = serialize($contestants);
    
      $rewards = serialize(array("0",'100'));
      $rules = serialize(array($minp*10,$maxp*10,$pduels,$tfee));


		$players = array();
		$players[1]=array();
		$players[1][0]=0;
		$players[1][1]=0;
		$players[1][2]=0;
		$players[1][3]=0;
		$players[-1]=array();
		$players[-1][0]=0;
		$players[-1][1]=0;
		$players[-1][2]=0;
		$players[-1][3]=0;


      $sql = "INSERT INTO Contests (type,    location,starts,    ends,    done,distro,    contestants,rules,   results   ,reward)
                            VALUES ('" . $ttype . "','World', '" . $tstarts . "','" . $tends . "','0', '" . $pdistro . "','" . $sc . "',      '" . $rules . "','" . $players . "','" . $rewards . "')";

        $resultt = mysqli_query($db,$sql);
 
        echo "</br>\nStarting Last Battle! Light: " . $lpoints . "; Shadow: " . $spoints . " | ";
      
    
      // Update City Rumors
      $cityRumors = mysqli_fetch_array(mysqli_query($db,"SELECT * FROM messages WHERE id='50000'"));

      $rumorMessages = unserialize($cityRumors['message']);
      $numRumors = count($rumorMessages);
        
      $myMsg = "<World_" . time() . ">` The Light has started the Last Battle against The Shadow!|";
      $rumorMessages[$numRumors] = $myMsg;
    
      $rumorMessages = pruneMsgs($rumorMessages, 50);
      $newRumors = serialize($rumorMessages);

      mysqli_query($db,"UPDATE messages SET message='$newRumors' WHERE id='50000'");  
      
    }
    
    // If all cities still stand, we need to destroy some and make some hordes


    if ($citiesLeft == 24)
    {
      include_once("locFuncs.php");

      $hstime = $cityRumors['checktime'] + $p1Start;
  
      // Destroy the city with the lowest population.
      $result = mysqli_query($db,"SELECT id, name, myOrder, army FROM Locations WHERE 1 ORDER BY pop ASC, myOrder ASC LIMIT 1"); 
      while ( $listloc = mysqli_fetch_array( $result ) )
      {
        echo "Destroy " . $listloc["name"] . "<br/>";
        // Destroy the city
        destroyCity($listloc);
        $citiesLeft--;
      }
    
      // Destroy the city with the lowest order
      $result = mysqli_query($db,"SELECT id, name, myOrder, army FROM Locations WHERE isDestroyed='0' ORDER BY myOrder ASC LIMIT 1"); 
      while ( $listloc = mysqli_fetch_array( $result ) )
      {
        echo "Destroy " . $listloc["name"] . "<br/>";
        // Destroy the city
        destroyCity($listloc);      
        $citiesLeft--;
      }
        
      // Target the 2 cities with the lowest order and the city with the highest order.
      $city = 1;
      $result = mysqli_query($db,"SELECT id, name, myOrder, army FROM Locations WHERE isDestroyed='0' ORDER BY myOrder ASC"); 
      while ( $listloc = mysqli_fetch_array( $result ) )
      {
        if ($city == 1 || $city == 2 || $city == $citiesLeft)
        {
          if ($city == 1) // Mega horde of Darkhounds to city with 2nd lowest order
          {
            //echo "Darkhounds to " . $listloc['name'] . "<br/>";
            $tnpc = "Darkhound";
          }
          else if ($city == 2) // Mega horde of Draghkar to city with 3rd lowest order
          {
            //echo "Draghkar to " . $listloc['name'] . "<br/>";
            $tnpc = "Draghkar";
          }
          else if ($city == $citiesLeft) // Mega horde of Trollocs to city with most order
          {
            //echo "Trollocs to " . $listloc['name'] . "<br/>";
            $tnpc = "Trolloc";
          }
  
          // Create a mega horde!
          $hwild = '';
		  echo ("</br> Initial horde creation for phase one");
          createMegaHorde($listloc, $tnpc, $hhealth, $hstime, $hwild, 0);
        }
        $city++;
      }
  
      // Find city with the lowest order that's ruled by the clan with highest alignment that rules a city.
      $lightClan = mysqli_fetch_array(mysqli_query($db,"SELECT id, name FROM Soc WHERE ruled > 0 ORDER BY align DESC LIMIT 1"));
      $result = mysqli_query($db,"SELECT * FROM Locations WHERE ruler='" . $lightClan['name'] . "' AND isDestroyed='0' ORDER BY myOrder ASC");
    
      $lcc=0;
      while ( ($listloc = mysqli_fetch_array( $result ) )&& $lcc == 0)
      {
        $result3 = mysqli_query($db,"SELECT id FROM Hordes WHERE type='3' AND done < '2' AND target = '" . $listloc['name'] . "'");
        $isTargeted = mysqli_num_rows($result3); 
  
        // If city not already targeted.
        if ($isTargeted == 0)
        {
          $tnpc = "Myrddraal";
          $hwild = '';
		  echo ("</br> Highest alignment horde");
          createMegaHorde($listloc, $tnpc, $hhealth, $hstime, $hwild, 0);
          $lcc=1;
        }
      }

      $minHordes = 6;
      
      // If less than minHordes active, create more.
      $result3 = mysqli_query($db,"SELECT id FROM Hordes WHERE type='3' AND done < '2'");
      $num_hordes = mysqli_num_rows($result3);
    
      $result2 = mysqli_query($db,"SELECT id, name, myOrder, army FROM Locations WHERE isDestroyed='0' ORDER BY RAND() ASC");
      while (( $listloc = mysqli_fetch_array( $result2 ) ) && ($num_hordes < $minHordes))
      {
        // Make some random mega hordes
        $result3 = mysqli_query($db,"SELECT id FROM Hordes WHERE type='3' AND done < '2' AND target = '" . $listloc['name'] . "'");
        $isTargeted = mysqli_num_rows($result3); 
        // If city not already targeted.
        if ($isTargeted == 0)
        {
          $htown = $listloc;
          $hwild='';
          $tnpc = "Bubble of Evil";
		  echo ("</br> Bubble of evil horde");
          createMegaHorde($htown, $tnpc, $hhealth, $hstime, $hwild, 0);        

          // increment the number of hordes
          $num_hordes++;
        } // if $isTargeted
      } // while cities || hordes < min
    } // if ($citiesleft == 24)
  } // if ($hoursFromBreak >= $p1Start)
  
  // check if we have any mega hordes done and handle effects
  $cur_hour = floor(time()/3600);
  $result3 = mysqli_query($db,"SELECT id, ends, done, target, location, army_done FROM Hordes WHERE type='3' AND done<'2' AND ends <= '" . $cur_hour . "'");
  $num_hordes = mysqli_num_rows($result3);

	echo "</br> \n NUM HORDES: ";
	echo $num_hordes;
	echo "   /    ";
	echo $cur_hour;

  if ($num_hordes)
  {
    $theLB = mysqli_fetch_array(mysqli_query($db,"SELECT id, results, contestants FROM Contests WHERE type='99'"));
    $lb_results = unserialize($theLB['results']);
    $lb_sides = unserialize($theLB['contestants']);
    while ($myHorde = mysqli_fetch_array( $result3 ) )
    {
      // If past time for horde to end, end the horde.
      if ($myHorde['ends']*3600<=time())
      {
        if ($myHorde['done'] == 1) 
        {
          $newdone=2;
          $lb_results[1][3] += 2500;
          $lb_sides[1][1] += 2500;
        }
        else 
        {
          $newdone=3;
          $lb_results[-1][3] += 1750;
          $lb_sides[-1][1] += 1750;
        }
        
        if ($myHorde['army_done'] == 1)
        {
          $lb_results[-1][3] += 1750;
          $lb_sides[-1][1] += 1750;
        }
        
 
          $result4= mysqli_query($db,"UPDATE Hordes SET done='" . $newdone . "' WHERE id = '" . $myHorde['id'] . "'");
        
 
        // Horde Attack!
        if ($newdone==3)
        {
          destroyAttackedCity($myHorde);
        }
      }
    }
    
    $lbCons = serialize($lb_sides);
    $lbRes = serialize($lb_results);
    mysqli_query($db,"UPDATE Contests SET contestants='" . $lbCons . "', results='" . $lbRes . "' WHERE id='" . $theLB['id'] . "'");
  }
 
  // Check if new mega hordes need to start
  $result3 = mysqli_query($db,"SELECT id, ends, done, target, location, army_done FROM Hordes WHERE type='3' AND done<'2'");
  $num_hordes = mysqli_num_rows($result3);
  
  echo ("</br> NUMBER OF HORDES: ");
  echo $num_hordes;
  echo ("</br>");
  
  echo (json_encode(($hoursFromBreak >= $p2Start && $hoursFromBreak < $p2End) || ($hoursFromBreak >= $p3Start && $hoursFromBreak < $p3End)));
  
  if ($num_hordes == 0 && (($hoursFromBreak >= $p2Start && $hoursFromBreak < $p2End) || ($hoursFromBreak >= $p3Start && $hoursFromBreak < $p3End)))
  {
    include_once("locFuncs.php");
    if ($hoursFromBreak >= $p2Start && $hoursFromBreak < $p2End)
    {
      // Kick off phase 2
	  	echo("</br> PHASE TWO STARTED </br>");
      $lastPhaseStart = $cityRumors['checktime'] + $p1Start;
      $myStart = $cityRumors['checktime'] + $p2Start;
	  echo "</br> START DETECTED: ";
	  echo $myStart;
      $minHordes = 8;
    } 
    else  // if ($hoursFromBreak >= 828 && $hoursFromBreak < 858)
    {
      // Kick off phase 3
	  	echo("</br> PHASE THREE STARTED </bs>");
      $lastPhaseStart = $cityRumors['checktime'] + $p2Start;
      $myStart = $cityRumors['checktime'] + $p3Start;
      $minHordes = 10;
    }   
    
    // Look a results of last phase to see what hordes need created.
    // Find all defeated hordes from last phase.
    $result3 = mysqli_query($db,"SELECT * FROM Hordes WHERE type='3' AND starts='" . $lastPhaseStart . "' AND done ='2'");
    $num_hordes = mysqli_num_rows($result3);
    if ($num_hordes)
    {
      $hstime = $myStart;
	  echo "</br> GOT HERE WITH TIME: ";
	  echo $hstime;
      while ($myHorde = mysqli_fetch_array( $result3 ) )
      {
        // Create a new horde targing the same city from the same wilderness.
        $htown = mysqli_fetch_array(mysqli_query($db,"SELECT id, name, myOrder, army FROM Locations WHERE name='" . $myHorde['target'] . "'"));
        // ensure our horde isn't a bubble of evil
        $tnpc = "Bubble of Evil";

        // find the number of mega hordes that have targeted this city already
        $timesDefended = mysqli_num_rows(mysqli_query($db,"SELECT id FROM Hordes WHERE type='3' AND target='" . $myHorde['target'] . "'"));
        
        createMegaHorde($htown, $tnpc, $hhealth, $hstime, $myHorde['location'], $timesDefended);        
      }
    }
    
    // Find all undefeated hordes from last phase.
    $result3 = mysqli_query($db,"SELECT * FROM Hordes WHERE type='3' AND starts='" . $lastPhaseStart . "' AND done ='3'");
    $num_hordes = mysqli_num_rows($result3);
    if ($num_hordes)
    {
      $hstime = $myStart;

      while ($myHorde = mysqli_fetch_array( $result3 ) )
      {
        // Create a new hordes targeting all 3 other cities around the wilderness from new wilderness areas.
        // If already a horde targeting that city, the city is already destroyed, or if all other wilderness
        //   areas around the city have hordes, skip it.
        $surrounding_city=$map_data[$myHorde['location']];
  
        for ($x=0; $x<4; $x++)
        {
          $loc = $surrounding_city[$x];  

          // If not the same city we just defeated.   
          if ($loc != $myHorde['target'])
          {         
            $htown = mysqli_fetch_array(mysqli_query($db,"SELECT id, name, myOrder, army,  FROM Locations WHERE name='" . $loc . "'"));
            if ($htown['isDestroyed'] == 0) // If not a destroyed city.
            {
              $inSameTown = mysqli_num_rows(mysqli_query($db,"SELECT id FROM Hordes WHERE type='3' AND target='" . $htown['name'] . "' AND done<2"));
              if (!$inSameTown)
              {
                $tnpc = "Bubble of Evil";
                $hwild='';
				echo ("</br> Bubble of evil");
                createMegaHorde($htown, $tnpc, $hhealth, $hstime, $hwild, 0);        
              } // if !inSameTown
            } // if !
          } // if !same city
        } // for surrounding cities
      } // while undefeated hordes      
    } // if undefeated hordes

    // If less than minHordes active, create more.
    $result3 = mysqli_query($db,"SELECT id FROM Hordes WHERE type='3' AND done < '2'");
    $num_hordes = mysqli_num_rows($result3);
  
    $result2 = mysqli_query($db,"SELECT id, name, myOrder, army FROM Locations WHERE isDestroyed='0' ORDER BY RAND() ASC");
    // Make sure we have $minHordes cities left...
    $num_cities = mysqli_num_rows($result2);   
    if ($minHordes > $num_cities) $minHordes = $num_cities;
	$hstime = $myStart;
    while (( $listloc = mysqli_fetch_array( $result2 ) ) && ($num_hordes < $minHordes))
    {
      // Make some random mega hordes
      $result3 = mysqli_query($db,"SELECT id FROM Hordes WHERE type='3' AND done < '2' AND target = '" . $listloc['name'] . "'");
      $isTargeted = mysqli_num_rows($result3); 
      // If city not already targeted.
      if ($isTargeted == 0)
      {
        $htown = $listloc; 
        $hwild='';
        $tnpc = "Bubble of Evil";
		echo ("</br> Another buble of evil");
        createMegaHorde($htown, $tnpc, $hhealth, $hstime, $hwild, 0);        

        // increment the number of hordes
        $num_hordes++;
      } // if $isTargeted
    } // while cities || hordes < min
  } // time to start new hordes

  // Handle hourly Horn bonuses.
  $curHorn = mysqli_fetch_array( mysqli_query($db,"SELECT id, owner FROM Items WHERE type='-1' ORDER BY last_moved" ));
  $theLB = mysqli_fetch_array(mysqli_query($db,"SELECT id, results, contestants FROM Contests WHERE type='99' AND done='0'"));
  if ($curHorn['owner'] && $theLB['id']) // Make sure we have a Horn and the LB is still going.
  {
    $lb_results = unserialize($theLB['results']);
	
	echo "</br>";
	echo JSON_encode($lb_results);
	
	
	if($lb_results==false){
		echo " </br> FALSE DETECTED FIXED";
		$lb_results = array();
		$lb_results[1]=array();
		$lb_results[1][0]=0;
		$lb_results[1][1]=0;
		$lb_results[1][2]=0;
		$lb_results[1][3]=0;
		$lb_results[-1]=array();
		$lb_results[-1][0]=0;
		$lb_results[-1][1]=0;
		$lb_results[-1][2]=0;
		$lb_results[-1][3]=0;
	}
	
    $lb_sides = unserialize($theLB['contestants']);
    // Sides earn 100 pts for holding the Horn each hour. 
    // 800 points are earned for holding the horn going into the battle 
    // and 2500 pts are earned for holding it at the end of the battle.
    $hornPts = 100;
    if ($hoursFromBreak == $p1Start) $hornPts = 800;
    else if ($hoursFromBreak == $LBEnd) $hornPts = 2500;
    
    $owner = mysqli_fetch_array(mysqli_query($db,"SELECT id, align FROM Users WHERE id='" . $curHorn['owner'] . "'"));
    if ($owner['align'] > 0)
    {
      $lb_results[1][3] += $hornPts;
      $lb_sides[1][1] += $hornPts;
    }
    else
    {
      $lb_results[-1][3] += $hornPts;
      $lb_sides[-1][1] += $hornPts;    
    }

    $lbCons = serialize($lb_sides);
    $lbRes = serialize($lb_results);
    mysqli_query($db,"UPDATE Contests SET contestants='" . $lbCons . "', results='" . $lbRes . "' WHERE id='" . $theLB['id'] . "'");
  }
    
  // End of Age
  // Handle end of Phase 4
  $endtime = intval(time()/3600);
  $result2 = mysqli_query($db,"SELECT id, contestants, rules, reward, type, distro FROM Contests WHERE type='99' AND done='0' AND ends<='" . $endtime . "'");
  while ($contest = mysqli_fetch_array( $result2 ) )
  {
    $contestants = unserialize($contest['contestants']);
    $rules = unserialize($contest['rules']);

    foreach ($contestants as $cid => $cdata)
    {
      if ($cdata[0]) 
      { 
        $ranks[$cdata[0]]=$cdata[1];
        $rid[$cdata[0]]=$cid;
      }
    }
    arsort($ranks);
    $place=1;
    $pdone=0;
    $numc= count($contestants);
    $first='';
    foreach ($ranks as $rname => $rscore) 
    {
      if (!$pdone)
      {
        if ($place==1)
        {
          $cname = explode('_', $rname);
          $first = $rname;
          $pdone=1;                
        }
        mysqli_query($db,"UPDATE Contests SET done=1, winner='" . $first . "' WHERE id='" . $contest['id'] . "'"); 
        
        // Update City Rumors
        $cityRumors = mysqli_fetch_array(mysqli_query($db,"SELECT * FROM messages WHERE id='50000'"));
        $rumorMessages = unserialize($cityRumors['message']);
        $numRumors = count($rumorMessages);

        if ($first == "Shadow")
        {
          $lbMsg = "The Last Battle is over. The Shadow has fallen over the pattern. Hail the Great Lord!.";
        }
        else
        {
          $lbMsg = "The Last Battle is over. The Light has prevailed over the Shadow. The Wheel turns.";
        }        
        $myMsg = "<World_" . time() . ">` " . $lbMsg . "|";
        $rumorMessages[$numRumors] = $myMsg;  
        $rumorMessages = pruneMsgs($rumorMessages, 50);
        $newRumors = serialize($rumorMessages);
        mysqli_query($db,"UPDATE messages SET message='$newRumors' WHERE id='50000'");
		
		echo $lbMsg;
		echo "</br>";
		
		
		echo $lbMsg;
		echo "</br>";

		
		echo $lbMsg;
		echo "</br>";
		include_once("saveAge.php");
      }
    }
  }
  

}else{
	echo "</br>\n last battle script tried running without enough seals broken";
}
?>