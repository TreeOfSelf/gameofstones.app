<?php
include_once("connect.php");
include_once("skills.php");
include_once("charFuncs.php");
include_once("itemFuncs.php");

createChar("The", "Creator",  "tlc@semremedy.com", "", "Thakan&#39;dar", 999);
createChar("Dark", "One",  "tlc@semremedy.com", "", "Thakan&#39;dar", 999);
createChar("Green", "Man", "tlc@semremedy.com", "", "Thakan&#39;dar", 999);

createAccount("tlc@semremedy.com","0;B,mszM*<H2vxJNU^");

function createAccount($email,$password)
{
  global $db;
  $actualPass = sha1($password);
  $sql = "INSERT INTO Accounts (email, password) 
  VALUES ('$email','$actualPass')";
  $result = mysqli_query($db,$sql);
  echo mysqli_error($db);
}

function createChar($username, $lastname, $email, $avatar, $startat, $vit)
{
  global $db, $item_base;
  

  $born=time();
  $nation=0;
  $type=0;
  $sex=0;
  $goodevil=3;
  $item = 8;
  $lvl_up = 150;
  
  $tarr[0] = $type;
  $tarr[1] = $item +100;
  $starr = serialize($tarr);
  
  $jobs = array(1,0,0,0,0,0,0,0,0,0,0,0,0);
  $jobs[$nation_bonus[$nation][1]] = 1;
  $jobs[$nation_bonus[$nation][2]] = 1;
  $jobss = serialize($jobs);
  $num_start = 0;
  
  $donor = 1;
  $btoday = 170;
  
  $non = serialize(array());
  
  $sql = "INSERT INTO Users (name,       lastname,     avatar,   email,   born,   sex,   type,    nation,   jobs,    focus,gold,   level,vitality,points,propoints,stamina,stamaxa,lastcheck,   lastscript,lastbuy,newmsg,newlog,newachieve,society,nextbattle,battlestoday,bankgold,lastbank,location,  travelmode,travelmode_name,feedneed,travelmode2, travelto,  arrival,depart,traveltype,exp,exp_up,   exp_up_s, goodevil,   equip_pts,used_pts,donor, ip) 
                     VALUES ('$username','$lastname','$avatar','$email','$born','$sex','$starr','$nation','$jobss',$item,'1000', '1',  $vit,    '2',   '1',      '20',   '20',   '0'          ,'0',       '0',    '1',   '0',   '0',       '',     '0',       '$btoday',   '4000',  '0',     '$startat','0',       '',             '0',     '$num_start','$startat','0',    '0',   '0',       '0','$lvl_up','$lvl_up','$goodevil','100',    '90',    $donor,'$non')";
  $result = mysqli_query($db,$sql);

  echo mysqli_error($db);

  $char = mysqli_fetch_array(mysqli_query($db,"SELECT id,name FROM Users WHERE name = '$username' AND lastname = '$lastname' "));
  $id=$char['id'];
  include("setitems.php");

  for ($i=1; $i < 1000; $i++) $skills[$i]=0;
  $skills = getSkills($skills,$type);
  $friends=serialize(array());

  $about="";
  
//  echo "Inserting Data...";
  $sql2 = "INSERT INTO Users_data (id,   about,   skills,   active,find_battle,friends) 
                           VALUES ('$id','$about','$skills','$non','0',        '$friends')";
  $result2 = mysqli_query($db,$sql2);
  

//  echo $sql2;
//  echo $result2;
    
//  echo "Inserting Stats...";    
  $sql3 = "INSERT INTO Users_stats (id,   ji, wins,battles,duel_wins,tot_duels,enemy_wins,enemy_duels,off_wins,off_bats,npc_wins,tot_npcs,duel_earn,item_earn,dice_earn,prof_earn,quest_earn,quests_done,play_quests_done,find_quests_done,npc_quests_done,item_quests_done,shadow_wins,shadow_npcs,military_wins,military_npcs,ruffian_wins,ruffian_npcs,channeler_wins,channeler_npcs,animal_wins,animal_npcs,exotic_wins,exotic_npcs) 
                            VALUES ('$id','0','0', '0',    '0',      '0',      '0',       '0',        '0',     '0',     '0',     '0',     '0',      '0',      '0',      '0',      '0',       '0',        '0',             '0',             '0',            '0',             '0',        '0',        '0',          '0',          '0',         '0',         '0',           '0',           '0',        '0',        '0',        '0')";                              
  $result3 = mysqli_query($db,$sql3);
  
    echo mysqli_error($db);
//  echo $sql3;
//  echo $result3;
}