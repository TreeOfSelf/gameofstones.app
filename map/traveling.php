<?php
  // CONNECT TO DATABASE
  include_once('mapdata/coordinates.inc');
  include_once('places/info.php');
  $no_query=1;
  include_once('places/connect_info.php');
  include_once('../admin/busiFuncs.php');
  include_once('../admin/locFuncs.php');
  
  $char = mysqli_fetch_array(mysqli_query($db,"SELECT * FROM Users LEFT JOIN Users_data ON Users.id=Users_data.id WHERE Users.id='$id'"));
  $error=0;
  
  $surrounding_area=$map_data[$char['location']];
 
  $dest = (str_replace("&#039;","'",$_POST['goto']));
  $dir = $_POST['dir'];

  // Prevent 'Skimming'
  if ($dest != $surrounding_area[$dir-1]) $dest = $surrounding_area[$dir-1];

  // START TRAVELING  
  if ($dir != '')
  {
    $char['travelto']=$dest;
    if ($char['arrival']<=time() && $location_array[$char['travelto']][0])
    {
        // SET VARIABLES / LOAD MAP / GET TRAVEL TIME      
        $result3 = mysqli_query($db,"SELECT * FROM Hordes WHERE done='0' AND location='$char[location]'");
        $numhorde = mysqli_num_rows($result3);
        // SET TRAVELING
        if ($travel_mode[$char['travelmode']][1]<=$char['feedneed']) $char['travelmode']=0; // WALK IF HORSE IS TOO HUNGRY
        $char['depart']=time();
        $char['arrival']=time()+1;
        $char['traveltype']=0;
        $newstamina = $char['stamina'];
        if ($char['travelmode']) $char['feedneed']++;
        else $newstamina--;
        if ($numhorde) $newstamina = $newstamina-2;
        if ($newstamina < 0) $newstamina = 0;
        mysqli_query($db,"UPDATE Users SET stamina='".$newstamina."', travelto='".$char['travelto']."', depart='".$char['depart']."', arrival='".$char['arrival']."', feedneed='".$char['feedneed']."', gold='".$char['gold']."', traveltype='".$char['traveltype']."' WHERE id='$id'");
    }
    else {$error=1;}
  }
  elseif ($char['arrival']<=time()) $error=1;
  
  if ($error && $location_array[$char['travelto']][2]) {header("Location: $server_name/town.php?update=1&message=$message"); exit;} // ERROR CHECK
  elseif ($error) {echo $dest.":".$surrounding_area[$dir-1]; exit;} // ERROR CHECK
  
  $loc=$char['travelto'];
  $locfrom=$char['location'];
  $speed=intval(0.3+SQRT(pow($location_array[$loc][0]-$location_array[$locfrom][0],2)+pow($location_array[$loc][1]-$location_array[$locfrom][1],2))/($char['arrival']-time()));
  if ($speed<1) $speed=1;
  echo ($char['arrival']-time())."|".($char['arrival']-$char['depart'])."|";
  echo "<center><font class='medtext'>Traveling from<br><b>".str_replace('-ap-','&#39;',$char['location'])."</b> to <b>".str_replace('-ap-','&#39;',$char['travelto'])."</b>".
                   "<br><br><table width='220' height='197'><tr><td width='100%'><img src='../images/travel.gif'></td><td></td></tr><table><br><br>".
                   "<table border='0' cellpadding='0' cellspacing='0'><tr><td id='travelInfo'><font class='littletext'>".intval(100*(time()-$char['depart'])/($char['arrival']-$char['depart']))." percent completed</td></tr></table>".
                   "<br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br>";
?>