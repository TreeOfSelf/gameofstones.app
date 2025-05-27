<?php

  include_once("admin/mapData.php");
  include_once("admin/displayFuncs.php");
  include_once("admin/userdata.php");

  if (!$skipVerify) {

    //Verify we have a correct email/pass combo 
    $headerQuery = "SELECT * FROM Accounts WHERE email = '$email' AND password = '$password'";
    $headerResult = mysqli_query($db,$headerQuery);
    if (mysqli_num_rows($headerResult) <= 0) {
      if (!headers_sent()) {
        $time = time();
        header("Location: $server_name/index2.php?time=$time"); exit;
      }
    } 

    //Verify we have some sort of name, lastname
    // TODO: check if this is valid for our email?
    if((!($name && $lastname))){
      if (!headers_sent()) {
      header("Location: $server_name/verify.php?enabled=1",false); exit;
      }
    }

    $headerQuery = "SELECT * FROM Users WHERE email = '$email' AND name = '$name' AND lastname = '$lastname'";
    $headerResult = mysqli_query($db,$headerQuery);
    if (mysqli_num_rows($headerResult) <= 0) {
      if (!headers_sent()) {
        $time = time();
        header("Location: $server_name/verify.php?enabled=1",false); exit;
      }
    } 

  
  }


?>
<!DOCTYPE html>
<html lang="en">
<head>
    <!-- Global site tag (gtag.js) - Google Analytics -->
<script async src="https://www.googletagmanager.com/gtag/js?id=G-ZNZPD7T4G5"></script>
<script>

  function updateCookie(key, value) {
    var expirationDate = new Date();
    expirationDate.setFullYear(expirationDate.getFullYear() + 1);
    var expires = "expires=" + expirationDate.toUTCString();
    document.cookie = key + "=" + value + "; " + expires;
  }
  
  function revealEmail(event,element) {
    event.preventDefault();
    if(element.style.filter != "blur(0px)"){
      element.style.filter = "blur(0px)";
    }else{
      element.style.filter = "blur(4px)";
    }
  }

  function characterSelect(event, element){
    event.preventDefault();

    let firstName = element.getAttribute('firstName');
    let lastName = element.getAttribute('lastName');
    let charId = element.getAttribute('charId');

    updateCookie("name",firstName);
    updateCookie("lastname",lastName);
    updateCookie("id",charId);

    console.log(document.cookie);

    location.reload();

  }

  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'G-ZNZPD7T4G5');
</script>
    
    <meta charset="windows-1252">
<META name="description" content="A Game of Stones is a free MMORPG based in the Wheel of Time universe. Includes: custom character development, unique weapons, clan warfare, and player versus player combat."/>
<META name="keywords" content="wheel of time, wot, mmorpg, wotmud, wot mud, wheeloftime, randland, free, rpg, sword, battle, gos, game of stones, online game, browser based, browser-based"/>
<meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate" />
<meta http-equiv="Pragma" content="no-cache" />
<meta http-equiv="Expires" content="0" />
<link rel="icon" href="https://gameofstones.app/images/icon.ico" type="image/icon" />
  <?php 
    if (!$skipVerify)
    {

      $myresult = mysqli_query($db,"SELECT * FROM Users LEFT JOIN Users_data ON Users.id=Users_data.id WHERE Users.id='$id'");
      $char = mysqli_fetch_array($myresult);
      $percent_up = 0; // Default to 0
      if ($char['exp_up_s'] != 0) { // Check if the divisor is not zero
          $percent_up = 100 - intval(100 * ($char['exp_up'] - $char['exp']) / $char['exp_up_s']);
      }
      if ($percent_up > 99) $percent_up = 99;
      $mylistsize = mysqli_num_rows(mysqli_query($db,"SELECT * FROM Items WHERE owner='$id' AND type<15"));
      $myclistsize = mysqli_num_rows(mysqli_query($db,"SELECT * FROM Items WHERE owner='$id' AND type>=19 AND istatus=0"));

      if ($char['travelmode']) {
        if ($travel_mode[$char['travelmode']][1]<=$char['feedneed']) $feedneed = 0;
        else $feedneed = ($travel_mode[$char['travelmode']][1]-$char['feedneed']);
      }
      else $feedneed = 0;
    }
    $n_title = $_COOKIE['name'];
    $nl_title = $_COOKIE['lastname'];
    if ($char['id']) $title = "$n_title $nl_title";
    if (!$title) $title = "A Game of Stones"; 
    else $title = "GoS: ".$title;
    echo "<title>$title</title>";
  ?>
  
  <meta name="viewport" content="width=device-width, initial-scale=1">

<?php
  if ($mode == 1)
  {
?>
  <link rel="stylesheet" href="./gosBootstrap/css/goslitetheme.min.css">
  <link href="//netdna.bootstrapcdn.com/bootstrap/3.0.0/css/bootstrap-glyphicons.css" rel="stylesheet">
  <link rel="stylesheet" href="./gos_style.css">
  <link rel="stylesheet" href="./gos_lite.css">
<?php 
  } 
  else 
  {
?>
  <link rel="stylesheet" href="./themes/gosdefault.min.css">
  <link href="//netdna.bootstrapcdn.com/bootstrap/3.0.0/css/bootstrap-glyphicons.css" rel="stylesheet">
  <link rel="stylesheet" href="./gos_style.css">
  <link rel="stylesheet" href="./gos_default.css">
<?php 
  }
?>

</head>

<?php 
  if (!$wikilink) $wikilink = "";
  if (!$message && !$char['id']) $message = ""; 
?>
 <nav class="navbar navbar-default navbar-fixed-top">
  <div class="container-fluid">
    <div class="navbar-header">
      <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#myNavbar">
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
      </button>
      <a class="navbar-brand" href="index.php"><img src='https://gameofstones.app/images/goslogotop1.png' class='' height='25'/></a>
    </div>
<?php
  //If we don't have an account logged in
  if (!$email)
  {
?>     
    <div class="collapse navbar-collapse" id="myNavbar">
      <ul class="nav navbar-nav navbar-right">
        <li><a href="create.php">Join</a></li>
        <li><a href="http://gosos.proboards.com/index.cgi" target="_blank">About</a></li>
        <li><a href="<?php echo 'http://talij.com/goswiki/index.php?title='.$wikilink;?>" target="_blank">How To Play</a></li>
        <li><a href="http://gosos.proboards.com/index.cgi" target="_blank">Forum</a></li>
        <li><a onClick="donateForm.submit()">Donate</a></li>
      <form name="donateForm" action="https://www.paypal.com/cgi-bin/webscr" method="post">
        <input type="hidden" name="cmd" value="_donations"/>
        <input type="hidden" name="business" value="timothycermak@semremedy.com"/>
        <input type="hidden" name="item_name" value="GoS: Project Coramoor"/>
        <input type="hidden" name="item_number" value="101"/>
        <input type="hidden" name="no_shipping" value="0"/>
        <input type="hidden" name="no_note" value="1"/>
        <input type="hidden" name="currency_code" value="USD"/>
        <input type="hidden" name="tax" value="0"/>
        <input type="hidden" name="lc" value="US"/>
        <input type="hidden" name="bn" value="PP-DonationsBF"/>
      </form>        
      </ul>      
<?php
  }
  //If we have an account logged in
  else
  {
    $newskills = $char['newskills'];
    $newprof = $char['newprof'];
    $newpoints = $newskills + $newprof;
  
    $newmsg = $char['newmsg'];
    $newlog = $char['newlog'];
    $newachieve = $char['newachieve'];
    $newnotes = $newmsg + $newlog + $newachieve;


    $selectionName = "";
    $selectionsArray = array();
    for ($i = 0; $i < 4; $i++) {
      $selectionArray[$i] = "<li><a href='create.php'>Create new character!</a></li>";
  }

    if (is_null($char)) {
      $selectionName = "No Characters!";
    }else{
      $selectionName = $char['name'] . " " . $char['lastname'];
    }

    $headerResult = mysqli_query($db,"SELECT * From Users WHERE email='$email'");
    if (mysqli_num_rows($headerResult) > 0) {
        // Loop through each row in the result set
        $selectionIndex = 0;
        while ($row = mysqli_fetch_assoc($headerResult)) {
            // Access individual columns of the current row using keys
            $thisId = $row['id'];
            $firstName = $row['name'];
            $lastName = $row['lastname'];
            $fullName = $firstName . " " . $lastName;
            
            if ($thisId != $id) {
              $selectionArray[$selectionIndex] = '<li><a newChar=false charId="'.$thisId.'" firstName="'.$firstName.'" lastName="'.$lastName.'" onclick="characterSelect(event, this)" href="#">'.$fullName.'</a></li>';
            } else {
              $selectionArray[$selectionIndex] = '<li><a style="background-color:rgb(50,90,50);" doNothing=true newChar=false charId="'.$thisId.'" firstName="'.$firstName.'" lastName="'.$lastName.'" onclick="characterSelect(event, this)" href="#">'.$fullName.'</a></li>';
            }
            $selectionIndex += 1;
        }
    }



?>
    <div class="collapse navbar-collapse" id="myNavbar">
      <ul class="nav navbar-nav">


      <?php
      //If we have a char logged in
      if ($char['id']) {
      ?>

        <li class="dropdown hidden-sm">
          <a class="dropdown-toggle" data-toggle="dropdown" href="#"><span><img src='https://gameofstones.app/images/icons/playericon.png'></span><?php echo $selectionName; ?> <?php if ($newpoints > 0 ) echo "<span class='badge'>$newpoints</span>";?>
          <span class="caret"></span></a>
          <ul class="dropdown-menu">
            <li><a href="bio.php">Profile</a></li>
            <li><a href="items.php">Inventory</a></li>
            <li><a href="stats.php">Skills <?php if ($newskills > 0 ) echo "<span class='badge'>$newskills</span>";?></a></li>
            <li><a href="professions.php">Professions <?php if ($newprof > 0 ) echo "<span class='badge'>$newprof</span>";?></a></li>
            <li><a href="myquests.php">Quests</a></li>
            <li><a href="avatar.php">Settings</a></li>
          </ul>
        </li>
        <li class="dropdown visible-sm">
          <a class="dropdown-toggle" data-toggle="dropdown" href="#">Player <?php if ($newpoints > 0 ) echo "<span class='badge'>$newpoints</span>";?>
          <span class="caret"></span></a>
          <ul class="dropdown-menu">
            <li><a href="bio.php">Profile</a></li>
            <li><a href="items.php">Inventory</a></li>
            <li><a href="stats.php">Skills <?php if ($newskills > 0 ) echo "<span class='badge'>$newskills</span>";?></a></li>
            <li><a href="professions.php">Professions <?php if ($newprof > 0 ) echo "<span class='badge'>$newprof</span>";?></a></li>
            <li><a href="myquests.php">Quests</a></li>
          </ul>
        </li>       
        <li class="dropdown">
          <a class="dropdown-toggle" data-toggle="dropdown" href="#">
              <span><img src='https://gameofstones.app/images/icons/worldicon.png'></span>
              <span style="vertical-align:middle; font-size:12px;">World</span>
          <span class="caret"></span></a>
          <ul class="dropdown-menu">
            <li><a href="world.php"><?php echo $char['location'];?></a></li>
            <li><a href="look.php?first=1">Nearby Players</a></li>
            <li><a href="look.php?&world=1&first=1">All Players</a></li>
            <li><a href="viewtowns.php">All Cities</a></li>
            <li><a href="horn.php">Heroes</a></li>
            <li><a href="halloffame.php">Hall of Fame</a></li>
            <li><a href="ways.php">The Ways</a></li>
            <li><a href="map.php">Map</a></li>                     
          </ul>
        </li> 
        <?php

          $hunt =0;
          $eLinks = array();
          $eLinkNum = 0;
          $myquests = $char['quests'] ? unserialize($char['quests']) : [];
          if ($myquests)
          {
            foreach ($myquests as $c_n => $c_s)
            {
              if ($c_s[0] == 1)
              {
                $hquest = mysqli_fetch_array(mysqli_query($db,"SELECT * FROM Quests WHERE id='$c_n'"));
                if ($hquest['expire'] > (time()/3600) || $hquest['expire'] == -1)
                { 
                  if ($hquest['type'] == $quest_type_num["NPC"])
                  {
                    $hgoals = unserialize($hquest['goals']);
                    if (strtolower($hgoals[2]) == strtolower($char['location']) && $myquests[$c_n][1]< $hgoals[0])
                    {
                      $hunt =1;
                    }
                  }
                  else if ($hquest['type'] == $quest_type_num["Horde"])
                  {
                    $hgoals = unserialize($hquest['goals']);
                    if (strtolower($hgoals[2]) == strtolower($char['location']) && $myquests[$c_n][1] > 0)
                    {
                       $hunt = 1;
                    }
                  }
                  else if ($hquest['type'] == $quest_type_num["Escort"])
                  {
                    $hgoals = unserialize($hquest['goals']);
                    $route = mysqli_fetch_array(mysqli_query($db,"SELECT * FROM Routes WHERE id='".$hgoals[1]."'"));
                    $rpath = unserialize($route['path']);
					
                    if (strtolower($rpath[$myquests[$c_n][1]]) == strtolower($char['location']) && $myquests[$c_n][1]< $hgoals[0])
                    {
                      if (!in_array($char['location'], $townnames) && $myquests[$c_n][2] < 1)
                      {
						
                        $eLinks[$eLinkNum++] = "<li><a href='npc.php?escort=".$c_n."'>Defend ".$hquest['offerer']."</a></li>";
                      }
                      else 
                      {
					  //ESCORT SHIT HERE
                        $eLinks[$eLinkNum++] = "<li><a onClick='submitEscortTravelForm($hquest[id]);'>Escort ".$hquest['offerer']."</a></li>";
                      }
                    }
                  }
                }
              }
            }
          }

          if (in_array($char['location'], $townnames))
          {          
            // CHECK FOR LOCAL BUSINESSES
            $myLocBiz[999] = '';

            $tempResult = mysqli_query($db,"SELECT id, type FROM Profs WHERE owner='".$char['id']."' AND location='".$char['location']."'");
            while ($bq = mysqli_fetch_array( $tempResult ) )
            {
              $myLocBiz[$bq['type']]=1;
            }
              
            $locCheck = mysqli_fetch_array(mysqli_query($db,"SELECT id, name FROM Locations WHERE name='".$char['location']."'"));
            $clead = isClanLeader($char,$char['society'],1, $locCheck['id']);
        ?>  
        <li class="dropdown">
          <a class="dropdown-toggle" data-toggle="dropdown" href="#">
          <span><img src='https://gameofstones.app/images/icons/townicon.png'></span>
          <span style="vertical-align:middle; font-size:12px;">Local</span>
          <span class="caret"></span></a>
          <ul class="dropdown-menu">
            <li><a href="shop.php">Shop</a></li>
            <li><a href="blacksmith.php">Blacksmith</a></li>
            <li><a href="market.php">Market</a></li>
            <li><a href="business.php?shop=4">Outfitter</a></li>
            <li><a href="business.php?shop=1">Inn</a></li>
            <li><a href="business.php?shop=2">Wise Woman</a></li>
            <li><a href="business.php?shop=3">Tavern</a></li>
            <li><a href="quests.php">Quests</a></li>
            <li><a href="rumors.php">Rumor</a></li>
            <li><a href="townhall.php">City Hall</a></li>
            <?php 
              if (count($myLocBiz) >1)
              {
            ?>
            <li><a href="mybusinesses.php">Businesses</a></li>
            <?php 
              }
              if ($clead)
              {
            ?>
            <li><a href="clanoffice.php">Clan Office</a></li>                       
            <?php
              }
            ?>
          </ul>
        </li>
        <?php
          }
          else
          {
            $escaped_location = mysqli_real_escape_string($db, $char['location']);
            $isHorde=mysqli_fetch_array( mysqli_query($db,"SELECT * FROM Hordes WHERE done='0' AND location='".$escaped_location."'") );
        ?>
        <li class="dropdown">
          <a class="dropdown-toggle" data-toggle="dropdown" href="#">
              <span><img src='https://gameofstones.app/images/icons/townicon.png'></span>
         <span style="vertical-align:middle; font-size:12px;">Local</span>
          <span class="caret"></span></a>
          <ul class="dropdown-menu">
            <li><a href="npc.php">Explore</a></li>
            <?php
              if ($hunt)
              { 
            ?>
            <li><a href="npc.php?hunt=1">Hunt</a></li>
            <?php
              }  
              if ($isHorde['id'])
              {
            ?>
            <li><a href="npc.php?horde=<?php echo $isHorde['id'];?>">Horde</a></li>
            <?php
                if ($isHorde['army_done'] == 0)
                {
            ?>
            <li><a href="npc.php?horde=<?php echo $isHorde['id'];?>&army=1'">Army</a></li>
            <?php
                }
              }
            ?>
            <li><a href="estate.php">Estates</a></li>
          </ul>
        </li>        
        <?php
          } 
        ?>             
        <li class="dropdown">
          <a class="dropdown-toggle" data-toggle="dropdown" href="#">
              <span><img src='https://gameofstones.app/images/icons/messagesicon.png'></span>
              <span style="vertical-align:middle; font-size:12px;">Messages</span> <?php if ($newnotes > 0 ) echo "<span class='badge'>$newnotes</span>";?>
          <span class="caret"></span></a>
          <ul class="dropdown-menu">
            <li><a href="messages.php">Message Center <?php if ($newmsg > 0 ) echo "<span class='badge'>$newmsg</span>";?></a></li>
            <li><a href="battlelogs.php">Battle Logs <?php if ($newlog > 0 ) echo "<span class='badge'>$newlog</span>";?></a></li>
            <li><a href="achievements.php">Achievements <?php if ($newachieve > 0 ) echo "<span class='badge'>$newachieve</span>";?></a></li>
            <li><a href="telaranrhiod.php" target="_blank">Tel'aran'rhiod</a></li>
          </ul>
        </li>      
        <li class="dropdown">
          <a class="dropdown-toggle" data-toggle="dropdown" href="#">
               <span><img src='https://gameofstones.app/images/icons/clanicon.png'></span>
               <span style="vertical-align:middle">Clans</span>
          <span class="caret"></span></a>
          <ul class="dropdown-menu">
          <?php
            if ($char['society'])
            {
          ?>
            <li><a href="clan.php"><?php echo $char['society'];?></a></li>
            <li><a href="vault.php">Clan Vault</a></li>
            <li><a href="look.php?first=1&clan=<?php echo $char['society'];?>">Clan Members</a></li>
          <?php
            }
          ?>    
            <li><a href="viewclans.php">All Clans</a></li>                  
          </ul>
        </li>             
        <li class="dropdown">
          <a class="dropdown-toggle" data-toggle="dropdown" href="#">
          <span><img src='https://gameofstones.app/images/icons/travelicon.png'</span>
          <span style="vertical-align:middle; font-size:12px;">Travel</span>
          <span class="caret"></span></a>
          <ul class="dropdown-menu">
          <?php
            include_once("admin/mapData.php");
            $surrounding_area = $map_data[$char['location']];
            
            for ($l = 0; $l < 4; $l++)
            {
              echo "<li><a onClick='submitTravelForm($l);'>$surrounding_area[$l]</a></li>";
            }
            for ($e = 0; $e < $eLinkNum; $e++)
            {

			  echo $eLinks[$e];
			  
            }
            if ($char['route'])
            {
              $route = mysqli_fetch_array(mysqli_query($db,"SELECT * FROM Routes WHERE id='".$char['route']."'"));
              $rpath = unserialize($route['path']);

              if (strtolower($rpath[$char['routepoint']]) == strtolower($char['location']))
              {
                echo "<li><a onClick='submitWaysTravelForm(".$route['id'].");'>Guiding to ".$route['end']."</a></li>";
              }
            }
          ?>        
          </ul>
        </li>



      <?php
      //END if we have a char logged in
          }
      ?>

      <li class="dropdown hidden-sm">
          <a class="dropdown-toggle" data-toggle="dropdown" href="#">Select Character
          <span class="caret"></span></a>
          <ul class="dropdown-menu">
            <?php 
                echo $selectionArray[0];
                echo $selectionArray[1];
                echo $selectionArray[2];
                echo $selectionArray[3];
            ?>
             <!-- <li><a href="#" style=""><?php //echo $email; ?></a></li> -->
          </ul>
        </li>

        <form name="travelForm" action="world.php" method="post">
          <?php $clean_loc = $char['location'];?>
          <input type="hidden" name="fromLoc" value="<?php echo $clean_loc;?>"/>
          <input type="hidden" name="toLoc" value="-1"/>
          <input type="hidden" name="escortId" value="-1"/>
          <input type="hidden" name="waysId" value="-1"/>
        </form>
                  
      </ul>
      <ul class="nav navbar-nav navbar-right">

      <li class="dropdown">
          <a class="dropdown-toggle" data-toggle="dropdown" href="#">
              <span><img src='https://gameofstones.app/images/icons/linkicon.png'></span>
              <span style="vertical-align:middle; font-size:12px;">Links</span>
          <span class="caret"></span></a>
          <ul class="dropdown-menu">
            <li class='hidden-sm hidden-md'><a href="about.php">About</a></li>  
            <li class='hidden-sm hidden-md'><a href="<?php echo 'http://talij.com/goswiki/index.php?title='.$wikilink;?>" target="_blank">How To Play</a></li>    
            <li class='hidden-sm hidden-md'><a href="http://gosos.proboards.com/index.cgi" target="_blank">Forum</a></li>
            <li class='hidden-sm hidden-md'><a href="https://discord.gg/ZVXMK45" target="_blank">Discord</a></li>
          </ul>
        </li>  


     
        <li class="dropdown visible-sm visible-md">
          <a class="dropdown-toggle" data-toggle="dropdown" href="#">Info
          <span class="caret"></span></a>
          <ul class="dropdown-menu">
            <li><a href="http://gosos.proboards.com/index.cgi" target="_blank">About</a></li>
            <li><a href="<?php echo 'http://talij.com/goswiki/index.php?title='.$wikilink;?>" target="_blank">Wiki</a></li>
            <li><a href="http://gosos.proboards.com/index.cgi" target="_blank">Forum</a></li>     
            <li><a href="https://discord.gg/nUJDSf" target="_blank">Discord</a></li>
          </ul>
        </li>        
        <li><a href="logout.php">Logout</a></li>
      </ul>
<?php
  }
?>
    </div>
  </div>
</nav>
<div class="container" align='center'>
  <div class="row" align='left'>

     <?php
       if ($char['id'])
       {
     ?>  
     <div class="col-md-6 col-md-push-6">
       <div class="">
         <table class="table table-responsive table-condensed infobar" width="100%" cellpadding="0" cellspacing="0" border="0">
           <tr>
             <td><img src="https://gameofstones.app/images/health.gif" title='Stamina' style="vertical-align:middle"/><?php echo $char['stamina']."/".$char['stamaxa'];?></td>
             <td><img src="https://gameofstones.app/images/battle.gif" title='Turns' style="vertical-align:middle"/><?php echo ($battlelimit - $char['battlestoday']);?></td>
             <?php 
    $xp = 0;
    $imgSrc = "./images/lvlup.gif";
    $textHere = "'% Towards Level Up'";
    // If they have a clan
    if ($char['society'] != "") {
      $societyValue = $char['society'];
      $escaped_societyValue = mysqli_real_escape_string($db, $societyValue);
      $headerQuery = "SELECT * FROM Users WHERE society = '$escaped_societyValue'";
      $societyMembers = mysqli_query($db, $headerQuery);
  
      if ($societyMembers) {
          $topLevel = 0; // Initialize the top level variable
          $topExpUp = 0;
          while ($member = mysqli_fetch_assoc($societyMembers)) {
              // Access the level of each society member
              $level = $member['level'];
  
              // Check if the current member's level is higher than the current top level
              if ($level > $topLevel) {
                  $topLevel = $level; // Update the top level
                  $topExpUp = $member['exp_up_s'];
              }
          }
  
          $levelCap = floor($topLevel*0.75);
  
          //If we are less than the level cap
          if($char['level'] < $levelCap){

            mysqli_query($db,"LOCK TABLES Soc WRITE, Users WRITE;");
            $headerQuery = "SELECT * FROM Soc WHERE name = '$escaped_societyValue'";
            $societyRows = mysqli_query($db, $headerQuery);
            $society = mysqli_fetch_assoc($societyRows);
            mysqli_query($db,"UNLOCK TABLES;");

            $upgrades = unserialize($society['upgrades']);
            $trainingGroundsLevel = $upgrades[8] / 10;
  
            if($trainingGroundsLevel > 0){
              
    
              //$distanceToCap = $levelCap - $char['level'];
              $distanceToCap = $topExpUp /*- $char['exp_up_s']*/;
              
              // Define a scaling factor (you can adjust this based on your preference)
              $scalingFactor = 0.25; // Adjust as needed
          
              // Calculate the XP based on the scaling factor and remaining distance
              $xp = $scalingFactor * $distanceToCap * $trainingGroundsLevel;
  
    
            }
          }

      } 
    }

    if($xp!=0){
      $imgSrc = "./images/star.png";
      $textHere = "'% Towards Level Up (+".floor($xp)." Bonus XP From Training)'";
    }


             ?>
             <td><img src=<?php echo $imgSrc?> title=<?php echo $textHere ?> style="vertical-align:middle"/><?php echo number_format($percent_up)."%";?></td>

             
            
              <td><img src="https://gameofstones.app/images/armor.gif" title='Equipment Storage' style="vertical-align:middle"/><?php echo $mylistsize."/".$inv_max;?></td>
             <td><img src="https://gameofstones.app/images/bag.gif" title='Consumables Storage' style="vertical-align:middle"/><?php echo $myclistsize."/".$pouch_max;?></td>
             <td><img src='https://gameofstones.app/images/horse.gif' title='Horse Stamina' style="vertical-align:middle"/><?php echo $feedneed;?> </td>
             <td><div id='pocket'><?php echo displayGold($char['gold']);?></div></td>
           </tr>
         </table>
       </div>
     </div>
     <?php
       }
     ?>
     <div class="col-md-6 col-md-pull-6">
       <b><?php if ($message) echo $message; ?></b>
     </div>
  </div>
  

