<link REL="StyleSheet" TYPE="text/css" HREF="style.css">
<?php
  $no_query=1;
  
  // SET VARIABLES / LOAD MAP
  $filename="map/mapdata/".str_replace(' ','_',strtolower($char['location'])).".map";
  include($filename);
  $mapdata=explode('#',$mapdata);
  $dimensions=explode('|',$mapdata[0]);
  
  $map_width = $dimensions[0];
  $map_height = $dimensions[1];
  $loc_name = $char['location'];

  // GET SURROUNDING AREAS
  for ($x=0; $x<7; $x++) $surrounding_area[$x]=str_replace('_',' ',$dimensions[$x+2]);
  
  // GENERATE MAP ARRAY
  for ($y=0; $y<$map_height; $y++)
  {
    $tmpRow=explode('|',$mapdata[$y+1]);
    for ($x=0; $x<$map_width; $x++)
    {
      $map[$x][$y]=explode(':',$tmpRow[$x]);
    }
  }
?>

<?php if ($char['arrival']<=time()) { ?>
    <table border="0" cellpadding="0" cellspacing="0" height="20">
      <tr>
        <td id="textblock" class='foottext_f'>
          <b>Travel from <?php echo str_replace('-ap-','&#39;',$char['location']); ?>:</b>
        </td>
      </tr>
    </table>

    <!-- COMPASS -->
    <center>
    <table>
      <?php if ($surrounding_area[0]) { echo $surrounding_area[0]?>
      <tr>
        <td height="25" align='right'>
          <font class=littletext valign='middle'><?php echo str_replace('_',' ',str_replace('-ap-','&#39;',$surrounding_area[0])); ?></font></td><td>
          <img src="map/places/imgs/e.gif" border="0" alt="N" onMouseover="overArrow(this);" onMouseout="leaveArrow(this);" onClick="clickArrow(this);" id="n" name="1">
        </td>
      </tr>
      <?php } ?>
      <?php if ($surrounding_area[3]) { ?>
      <tr>
        <td height="25" align='right'>
          <font class=littletext><?php echo str_replace('_',' ',str_replace('-ap-','&#39;',$surrounding_area[3])); ?></td><td>
          <img src="map/places/imgs/e.gif" border="0" alt="W" onMouseover="overArrow(this);" onMouseout="leaveArrow(this);" onClick="clickArrow(this);" id="w" name="4">
        </td>
      </tr>
      <?php } ?>
      <?php if ($surrounding_area[2]) { ?>
      <tr>
        <td height="25" align='right'>
          <font class=littletext><?php echo str_replace('_',' ',str_replace('-ap-','&#39;',$surrounding_area[2])); ?></td><td>
          <img src="map/places/imgs/e.gif" border="0" alt="E" onMouseover="overArrow(this);" onMouseout="leaveArrow(this);" onClick="clickArrow(this);" id="e" name="3">
        </td>
      </tr>
      <?php } ?>
      <?php if ($surrounding_area[1]) { ?>
      <tr>
        <td height="25" align='right'>
          <font class=littletext><?php echo str_replace('_',' ',str_replace('-ap-','&#39;',$surrounding_area[1])); ?></td><td>
          <img src="map/places/imgs/e.gif" border="0" alt="S" onMouseover="overArrow(this);" onMouseout="leaveArrow(this);" onClick="clickArrow(this);" id="s" name="2">
        </td>
      </tr>
      <?php } ?>      
    </table>
<?php } ?>