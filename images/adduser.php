<?php
include("admin/connect.php");
include("admin/skills.php");

//gather user variables from last page where inputed
$username=trim($_POST[userid]);
$lastname=trim($_POST[last]);
$actualpass=$_POST[password];
$actualpass2=$_POST[pass2];
$channeler=$_POST[channeler];
$password=sha1($actualpass);
$email=$_POST[email];
$sex=$_POST[sex];
$nation=$_POST[nation];
$type=$_POST[type];
$item=$_POST[item];
$check_transfer=$_POST[transfer];
$born=time();

// clear stuff that could be transferred
$about="";

$avatar="";

// CREATE CHARACTER

$rand = rand(1,18);

$query = "SELECT * FROM Locations WHERE id='$rand'";
$result = mysqli_query($db,$query);
$location = mysqli_fetch_array($result);

$startat="$location[name]";

// FIRST NOTE
$query = "SELECT * FROM Users WHERE name = '$username' AND lastname = '$lastname' ";
$resultb = mysqli_query($db,$query);
if (mysql_fetch_row($resultb)) {
  include('headerno.htm');
  echo "<text class=littletext><br><center><br>";
  echo "<center>The Character <b>$username $lastname</b> already exists. Please choose another name.";

}
elseif (strlen($lastname) > 2 && strlen($lastname) < 11 && strlen($username) > 2 && strlen($username) < 11 && strlen($actualpass) > 4 && strlen($actualpass) < 11 && !eregi("[^a-z]+",$lastname)  && !eregi("[^a-z]+",$username) && strlen($email) <= 40 && $actualpass == $actualpass2 && $_POST[noalt] && $_POST[nocrap] && $_POST[nation] && $_POST[type] && $_POST[item]) 
{
  $ips[0]=$_SERVER['REMOTE_ADDR'];
  $alts = ''; 
  $ip_log = '';
  $users='';
  for ($i = 0; $i < count($ips); $i++)  
  {
    $result = mysqli_query($db,"SELECT * FROM IP_logs WHERE addy='$ips[$i]'"); 
    $ip_log = mysqli_fetch_array($result); 
    $users= unserialize($ip_log[users]); 
    for ($j=0; $j < count($users); $j++)  
    {  $alts[$users[$j]] = 1; } 
  }
  $maxnum=10;
  if ($ip_log[num]) {$maxnum = $ip_log[maxnum];}
  if (count($alts) >= $maxnum)
  {
    $altnum = count($alts);
    include('headerno.htm');
    echo "<text class=littletext><br><center><br>";
    echo "<center>You already have <b>$altnum</b> characters. Why not play with them?.";
  }
  else
  {
    // CREATE CHANNELER
    $num_start = 0;
    $notes = array (
      array ( "<b>Welcome to GoS!</b>", "<br>Check out the <a href=http://goshelpsite.wikispaces.com/>GoS Wiki</a> for an overview of the gameplay or check out the forum if you have any questions.<br><br>Enjoy the game!", "The_Creator", 0,"$born"),
    );
  
    // NORMAL CHARACTER STUFF  
    $log = serialize(array());
    $goodevil = 0;
    $vit=24;
    if ((strtolower($username) == "the" && strtolower($lastname) == "creator") || (strtolower($username) == "dark" && strtolower($lastname) == "one"))
    {
      $nation=0;
      $type=0;
      $goodevil=3;
      $vit=999;
    }
    if ($item == 6) $item = 8;
    include("admin/setitems.php");
    $notes = serialize($notes);
    $lvl_up = 150; // EXP TO LEVEL UP FOR THE FIRST TIME
    for ($i=1; $i < 1000; $i++) $skills[$i]=0;
    $skills = getSkills($skills,$type);
 
    $lastcheck = intval($born/3600);
    $ipaddy[0] = $_SERVER['REMOTE_ADDR'];
    
    $tarr[0] = $type;
    $starr = serialize($tarr);
    
    $queryd = "SELECT * FROM donate WHERE email='$email'";
    $resultd = mysqli_query($db,$queryd);
    $donors = mysqli_fetch_array($resultd);
    if ($donors[id] && $donors[amount] >= 5) 
      $donor = 1; 
    else 
      $donor = 0;
    
    if ($donor) $btoday = 150; else $btoday = 50;

    $user_ips= serialize($ipaddy);
    $sql = "INSERT INTO Users (name,       lastname,   password,   avatar,   email,   born,   sex,   type,    nation,   focus,gold, level,vitality,points,stamina,stamaxa,lastcheck,   lastscript,lastbuy,society,nextbattle,battlestoday,bankgold,lastbank,location,  travelmode,travelmode_name,feedneed,travelmode2, travelto,  arrival,depart,traveltype,exp,exp_up,   exp_up_s, goodevil,   equip_pts,used_pts,donor, ip) 
                       VALUES ('$username','$lastname','$password','$avatar','$email','$born','$sex','$starr','$nation',$item,'20', '1',  $vit,    '3',   '10',   '10',   '$lastcheck','0',       '0',    '',     '0',       '$btoday',   '80',    '0',     '$startat','0',       '',             '0',     '$num_start','$startat','0',    '0',   '0',       '0','$lvl_up','$lvl_up','$goodevil','48',     48,      $donor,'$user_ips')";
    $result = mysqli_query($db,$sql);
    $query = "SELECT * FROM Users WHERE name = '$username' AND lastname = '$lastname' ";
    $resultb = mysqli_query($db,$query);
    $char = mysqli_fetch_array($resultb);
    $id=$char['id'];
    $friends=serialize(array());
    $sql2 = "INSERT INTO Users_data (id,   itmlist,pouch  ,stomach, msgs,   about,   log,   skills,   active,find_battle,friends) 
                             VALUES ('$id','$itms','$pchs','$log', '$notes','$about','$log','$skills','$log','0',        '$friends')";
    $result2 = mysqli_query($db,$sql2);
    
    $sql3 = "INSERT INTO Users_stats (id,   wins,battles,duel_wins,tot_duels,enemy_wins,enemy_duels,off_wins,off_bats,npc_wins,tot_npcs,duel_earn,item_earn,dice_earn,quests_done,quest_types,shadow_wins,shadow_npcs,military_wins,military_npcs,ruffian_wins,ruffian_npcs,channeler_wins,channeler_npcs,animal_wins,animal_npcs,exotic_wins,exotic_npcs) 
                              VALUES ('$id','0', '0',    '0',      '0',      '0',       '0',        '0',     '0',     '0',     '0',     '0',      '0',      '0',      '0',        '$log',     '0',        '0',        '0',          '0',          '0',         '0',         '0',           '0',           '0',        '0',        '0',        '0')";
                              
    $result3 = mysqli_query($db,$sql3);

    // REDIRECT TO LOGIN
    if ($id && $result2 && result3) 
    {
      setcookie("id", "$id", time()+99999999, "/");
      setcookie("name", "$username", time()+99999999, "/");
      setcookie("lastname", "$lastname", time()+99999999, "/");
      setcookie("password", "$password", time()+99999999, "/");
      // IF 100th character, optimize database
      $result = mysqli_query($db,"SELECT name, id FROM Users WHERE name='$username' AND lastname='$lastname'");
      $new_id = mysqli_fetch_array($result);
      if ($new_id[id]/100 == intval($new_id[id]/100)) {mysqli_query($db,"OPTIMIZE TABLE Users"); mysqli_query($db,"OPTIMIZE TABLE Users_data");}
      // REDIRECT
      header("Location: $server_name/bio.php?time=$born");
      exit;
    }
    echo "Something really strange went wrong with this creation - please report it to tim.a.jensen@gmail.com";
    echo $id.$result2;
    exit;
  }
}
else {
  include('headerno.htm');
  echo "<text class=littletext><br><center><br><br><b><center>This Character could not be created<br><br><br><br><center><table><tr><td class='littletext'><p align=left><b>1.</b> The first and last names must be between 3 and 10 characters in length <br><br><b>2.</b> The password must be between 5 and 10 characters in length<br><br><b>3.</b> Both parts of the name must consist only of letters (no spaces)<br><br><b>4.</b> The E-Mail address must not exceed 40 characters<br><br><b>5. <i>You must agree to the terms</i></b><br><br><b>6.</b>You must choose a nationality, class, and weapon focus.</td></tr></table>";
}
?>

<br>

<?php
include('footer.htm');
?>