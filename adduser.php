<?php
$skipVerify = 1;

require_once("ayah.php");
$integration = new AYAH();

$score = $integration->scoreResult();

include_once("admin/connect.php");
include_once("admin/skills.php");
include_once("admin/charFuncs.php");
include_once("admin/itemFuncs.php");

//Make sure they have a valid login
$query = "SELECT * FROM Accounts WHERE email = '$email' AND password = '$password'";
$result = mysqli_query($db,$query);
if (mysqli_num_rows($result) <= 0) {
  $time = time();
  header("Location: $server_name/index2.php?time=$time");
  exit;
} 

//gather user variables from last page where inputed
$username=mysqli_real_escape_string($db,trim($_POST['userid']));
$lastname=mysqli_real_escape_string($db,trim($_POST['last']));

// Capitalize first letter of username and lastname
$username = ucfirst(strtolower($username)); // Make lowercase first to ensure only first char is upper
$lastname = ucfirst(strtolower($lastname));

$channeler=mysqli_real_escape_string($db,$_POST['channeler']);
$sex=mysqli_real_escape_string($db,$_POST['sex']);
$nation=mysqli_real_escape_string($db,$_POST['nation']);
$type=mysqli_real_escape_string($db,$_POST['type']);
$item=mysqli_real_escape_string($db,$_POST['item']);
$check_transfer=$_POST['transfer'];
$born=time();


$skipVerify = 1;
// clear stuff that could be transferred
$about="";

$avatar="";

// CREATE CHARACTER
$startat=$nat_start[$nation];
$num_start = 0;

// FIRST NOTE
$query = "SELECT * FROM Users WHERE name = '$username' AND lastname = '$lastname' ";
$result = mysqli_query($db,$query);
if (strlen($username) <= 2 || strlen($username) >= 20 || preg_match("/[^a-z]+/i",$username))
{
  include('header.php');
  echo "<br/><br/>";
  echo "<center>Invalid Character Name given: Must be 3-20 characters using letters only.</center>";
}
else if (strlen($lastname) <= 2 || strlen($lastname) >= 20 || preg_match("/[^a-z]+/i",$lastname))
{
  include('header.php');
  echo "<br/><br/>";
  echo "<center>Invalid House Name given: Must be 3-20 characters using letters only.</center>";
}
else if (mysqli_fetch_row($result)) {
  include('header.php');
  echo "<br/><br/>";
  echo "<center>The Character <b>$username $lastname</b> already exists. Please choose another name.</center>";
}
else if (!$_POST['nation'] || !$_POST['type'] || !$_POST['item'])
{
  include('header.php');
  echo "<br/><br/>";
  echo "<center>Invalid Nation, Class, or Weapon Focus selected.</center>";
}
elseif (strlen($lastname) > 2 && strlen($lastname) < 20 && strlen($username) > 2 && strlen($username) < 20 && !preg_match("/[^a-z]+/i",$lastname)  && !preg_match("/[^a-z]+/i",$username)  && $_POST['nation'] && $_POST['type'] && $_POST['item']) 
{

  $result = mysqli_query($db,"SELECT * FROM Users WHERE email='$email'");
  $alts = mysqli_num_rows($result);
  $maxnum=4;
  
  if ($alts >= $maxnum)
  {
    include('header.php');
    echo "<br/><br/>";
    echo "<center>You already have <b>$alts</b> characters. Why not play with them?</center>";
  }
  else
  {

    // NORMAL CHARACTER STUFF  
    $log = serialize(array());
    $goodevil = 0;
    $vit=50;
    if ((strtolower($username) == "the" && strtolower($lastname) == "creator") || (strtolower($username) == "dark" && strtolower($lastname) == "one"))
    {
      $nation=0;
      $type=0;
      $goodevil=3;
      $vit=999;
    }

    $notes = serialize($notes);
    $lvl_up = 150; // EXP TO LEVEL UP FOR THE FIRST TIME
    for ($i=1; $i < 1000; $i++) $skills[$i]=0;
    $skills = getSkills($skills,$type);
 
    $lastcheck = intval($born/900);
    $ipaddy[0] = $_SERVER['REMOTE_ADDR'];
    
    $tarr[0] = $type;
    $tarr[1] = $item +100;
    $starr = serialize($tarr);
    
    $jobs = array(1,0,0,0,0,0,0,0,0,0,0,0,0);
    $jobs[$nation_bonus[$nation][1]] = 1;
    $jobs[$nation_bonus[$nation][2]] = 1;
    $jobss = serialize($jobs);
    
    //$query = "SELECT * FROM donate WHERE email='$email'";
    //$result = mysqli_query($db,$query);
    //$donors = mysqli_fetch_array($result);

    // DONOR FIX
    //if (($donors['id'] && $donors['amount'] >= 5)) 
      //$donor = 1; 
    //else 
      //$donor = 0;
    
    // MAKE EVERYONE A DONOR  
    $donor = 1;
    
    if ($donor) $btoday = 70; else $btoday = 70;
// END DONOR FIX
     
   $creator = mysqli_fetch_array(mysqli_query($db,"SELECT * FROM Users WHERE name = 'The' AND lastname = 'Creator' "));
    if ($creator['id'])
    {
      $creationtime = intval($creator['born']/3600);
      $mytime = intval(time()/3600);
      $mydelay = $mytime-$creationtime;
      if ($mydelay > 20) $mydelay=20;
      $btoday -= ($mydelay*2);
    }

    $pchs = serialize(array());
    $itms = serialize(array());
    $inventory = array ( array ("",'','','','') );

    $user_ips= serialize($ipaddy);
    $sql = "INSERT INTO Users (name,        lastname,     avatar,   email,   born,   sex,   type,    nation,   jobs,    focus,gold,   level,vitality,points,propoints,stamina,stamaxa,lastcheck,   lastscript,lastbuy,newmsg,newlog,newachieve,society,nextbattle,battlestoday,bankgold,lastbank,location,  travelmode,travelmode_name,feedneed,travelmode2, travelto,  arrival,depart,traveltype,exp,exp_up,   exp_up_s, goodevil,   equip_pts,used_pts,donor, ip) 
                       VALUES ('$username','$lastname','$avatar','$email','$born','$sex','$starr','$nation','$jobss',$item,'001000', '1',  $vit,    '2',   '1',      '20',   '20',   '$lastcheck','0',       '0',    '1',   '0',   '0',       '',     '0',       '$btoday',   '4000',  '0',     '$startat','0',       '',             '0',     '$num_start','$startat','0',    '0',   '0',       '0','$lvl_up','$lvl_up','$goodevil','100',    '90',    '$donor','$user_ips')";
    

	  $result = mysqli_query($db, $sql);

    echo mysqli_error($db);
    $query = "SELECT * FROM Users WHERE name = '$username' AND lastname = '$lastname' ";
    $result = mysqli_query($db,$query);
    $char = mysqli_fetch_array($result);
    $id=$char['id'];
    
    // Fetch 'The Creator' user's ID safely
    $creator_data = mysqli_fetch_array(mysqli_query($db,"SELECT id FROM Users WHERE name = 'The' AND lastname = 'Creator' "));
    $cid = 0; // Default to 0, assuming '0' can represent a system/undefined sender
    if ($creator_data && isset($creator_data['id']) && is_numeric($creator_data['id'])) {
        $cid = (int)$creator_data['id'];
    }

    $notesub = "Welcome to GoS!";
    $note = "Check out the <a href=http://talij.com/goswiki/>GoS Wiki</a> for an overview of the gameplay or check out the forum if you have any questions.<br/><br/>";
    $note .= "If you're new to GoS, check out the <a href=http://talij.com/goswiki/index.php?title=Tutorial>Green Man's Tutorial</a> to learn how to play.<br/><br/>Enjoy the game!";
    $note_extra = "";
    $note = mysqli_real_escape_string($db, $note);
    $result = mysqli_query($db,"INSERT INTO Notes (from_id,to_id,del_from,del_to,type,root,sent,   cc,subject,   body,   special) 
                                      VALUES ('$cid', '$id','0',     '0',   '0', '0', '$born','','$notesub','$note','$note_extra')");

    include("admin/setitems.php");

    $friends=serialize(array());
    $sql2 = "INSERT INTO Users_data (id,   about,    skills,   active,find_battle,friends) 
                             VALUES ('$id','$about','$skills','$log','0',        '$friends')";
    $result2 = mysqli_query($db, $sql2);
    
    $sql3 = "INSERT INTO Users_stats (id,   ji, wins,battles,duel_wins,tot_duels,enemy_wins,enemy_duels,off_wins,off_bats,npc_wins,tot_npcs,duel_earn,item_earn,dice_earn,prof_earn,quest_earn,quests_done,play_quests_done,find_quests_done,npc_quests_done,item_quests_done,shadow_wins,shadow_npcs,military_wins,military_npcs,ruffian_wins,ruffian_npcs,channeler_wins,channeler_npcs,animal_wins,animal_npcs,exotic_wins,exotic_npcs) 
                              VALUES ('$id','0','0', '0',    '0',      '0',      '0',       '0',        '0',     '0',     '0',     '0',     '0',      '0',      '0',      '0',      '0',       '0',        '0',             '0',             '0',            '0',            '0',        '0',        '0',          '0',          '0',         '0',         '0',           '0',           '0',        '0',        '0',        '0')";                              
    $result3 = mysqli_query($db, $sql3);

    // REDIRECT TO LOGIN
    if ($id && $result2 && 'result3') 
    {
      setcookie("id", "$id", time()+99999999, "/");
      setcookie("name", "$username", time()+99999999, "/");
      setcookie("lastname", "$lastname", time()+99999999, "/");
      // IF 100th character, optimize database
      $result = mysqli_query($db, "SELECT name, id FROM Users WHERE name='$username' AND lastname='$lastname'");
      $new_id = mysqli_fetch_array($result);
      if ($new_id['id']/100 == intval($new_id['id']/100)) {mysqli_query($db,"OPTIMIZE TABLE Users"); mysqli_query($db,"OPTIMIZE TABLE Users_data");}
      // REDIRECT
      header("Location: $server_name/bio.php?time=$born");
      exit;
    }
    echo "Something really strange went wrong with this creation - please report it to tim.a.jensen@gmail.com";
    echo $id.$result2;
    exit;
  }
}
else 
{
  include('header.php');
  echo "<center><br/><br/><br/><b>This Character could not be created<br/><br/><br/><br/><table><tr><td class='littletext' align=left><b>1.</b> The first and last names must be between 3 and 10 characters in length <br/><br/><b>2.</b> The password must be between 5 and 10 characters in length<br/><br/><b>3.</b> Both parts of the name must consist only of letters (no spaces)<br/><br/><b>4.</b> The E-Mail address must not exceed 40 characters<br/><br/><b>5. <i>You must agree to the terms</i></b><br/><br/><b>6.</b> You must choose a nationality, class, and weapon focus.<br/><br/><b>7.</b> Complete the 'Are you a Human?' check.</td></tr></table></center>";
}
?>

<br/>

<?php

include('footer.htm');

?>

