<link REL="StyleSheet" TYPE="text/css" HREF="style.css">
<?php
  $travel_mode=array(
    'canvas sack',
    'backpack',
    'small cart',
    'cart',
    'farm cart',
    'carriage',
    'wagon',
  );
  $travel_mode_cost=array(
    0,
    2000,
    10000,
    30000,
    50000,
    100000,
    200000,
  );
function displayGold ($money, $text=0)
{
  $crown = floor($money/10000);
  $mark = floor(($money-$crown*10000)/100);
  $penny = floor (($money-$crown*10000-$mark*100));
  if ($text==0)
    return "<img src='images/gold.gif' width='15' align='bottom' alt='g:'>".$crown."<img src='images/silver.gif' width='15' align='bottom' alt='s:'>".$mark."<img src='images/copper.gif' width='15' align='bottom' alt='c:'>".$penny;
  else
  {
    if ($crown != 1)$c = "crowns"; else $c = "crown";
    if ($mark != 1)$m = "marks"; else $m = "mark";
    if ($penny != 1)$p = "pennies"; else $p = "penny";        
    return $crown." gold ".$c.", ".$mark." silver ".$m.", and ".$penny." copper ".$p.".";  
  }
}  
      include($_SERVER['DOCUMENT_ROOT']."/".$subfile."/map/mapdata/coordinates.inc");      
?>
<html>
  <head>
  <SCRIPT LANGUAGE="JavaScript">
    <?php
      include('info.php');
      include('connect_info.php');
      if ($travel_mode_cost[$_GET['buy']] && $location_array[$char['location']][2])
      {
        if ($char['travelmode2']<=$char['travelmode'])
        {
          if ($travel_mode_cost[$_GET['buy']]<=$char['gold'])
          {
            $char['gold']-=$travel_mode_cost[$_GET['buy']];
            $char['travelmode2']=$_GET['buy'];
            $message="You have purchased a ".ucwords($travel_mode[$_GET['buy']]);
            mysqli_query($db,"UPDATE Users SET gold='".$char['gold']."', travelmode2='".$char['travelmode2']."' WHERE id='".$char['id']."'");
          }
          else $message="You do not have that much money";
        }
        else $message="You need a stronger animal to pull a ".$travel_mode[$_GET['buy']];
      }
      if ($message) echo "window.onLoad=parent.UpdateTop('$message',".$char[gold].");";
    ?>
  </SCRIPT>
  </head>
  <body bgcolor="black">
    <font class="littletext">
    <b>Blacksmith:</b>
    <br>
    <font class="foottext">
    <?php
      if ($char['travelmode2']==0) echo "Currently carrying a bag";
      else echo "Currently using a ".$travel_mode[$char['travelmode2']];
    ?>
    <br>
    <font class="foottext_f">
    [<i>holds <?php echo (10+4*$char['travelmode2']); ?> items</i>]
    <br>
    <br>
    <?php
      for ($x=$char['travelmode2']+1; $x<=$char['travelmode2']+2; $x++)
      {
        if ($travel_mode_cost[$x])
        {
          echo "<br><font class='littletext'>".ucwords($travel_mode[$x])."<font class='littletext_f'> - ".displayGold($travel_mode_cost[$x])."<font class='foottext'> [<a href='blacksmith.php?buy=$x&time=".time()."'>buy</a>]";
        }
      }
    ?>
    <font class="littletext">
    <br>
    <br>
    
  
  </body>
  <footer>
  </footer>
</html>  