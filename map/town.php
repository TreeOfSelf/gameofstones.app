<?php
	// CONNECT TO DATABASE
	include('places/info.php');
	$no_query=1;
	include('places/connect_info.php');
	$char = mysqli_fetch_array(mysqli_query($db,"SELECT * FROM Users LEFT JOIN Users_data ON Users.id=Users_data.id WHERE Users.id='$id'"));
	
	if ($char['arrival']>time()) {header("Location: $server_name/map/traveling.php"); exit;} // ERROR CHECK
	
	if ($char['arrival']<=time() && $char['arrival']!=0) // ARRIVE AT TRAVEL TO PLACE
	{
		$char['location']=$char['travelto'];
		include($_SERVER['DOCUMENT_ROOT']."/".$subfile."/map/mapdata/coordinates.inc");
		$char['ismarket'] = $location_array[$char['location']][3];
		mysqli_query($db,"UPDATE Users SET location='".$char['travelto']."', arrival='0', ismarket='".$location_array[$char['location']][3]."' WHERE id='$id'");
	}
	
	// SET VARIABLES / LOAD MAP
	$filename="mapdata/".str_replace(' ','_',strtolower($char['location'])).".map";
	include($filename);
	$mapdata=explode('#',$mapdata);
	$dimensions=explode('|',$mapdata[0]);
	
	$map_width = $dimensions[0];
	$map_height = $dimensions[1];

	// GET SURROUNDING AREAS
	for ($x=0; $x<7; $x++) $surrounding_area[$x]=str_replace(' ','_',$dimensions[$x+2]);
	
	// GENERATE MAP ARRAY
	for ($y=0; $y<$map_height; $y++)
	{
		$tmpRow=explode('|',$mapdata[$y+1]);
		for ($x=0; $x<$map_width; $x++)
		{
			$map[$x][$y]=explode(':',$tmpRow[$x]);
		}
	}
	
	// REMEMBER SECTION OF TOWN
	if ($_COOKIE['mapCo']) $mapCo=explode(':',$_COOKIE['mapCo']);
	if (intval($mapCo[0]+$mapCo[1]) || $_GET['update']) $refresh_map=1; else $refresh_map=0;
$mapCo=Array(0,0);
?>
<html>
<head>
	<SCRIPT LANGUAGE="JavaScript">
	
	var MapWidth=<?php echo intval($map_width); ?>;
	var MapHeight=<?php echo $map_height; ?>;
	var toHere='<?php echo $char['location']; ?>';
	var toNorth='<?php echo $surrounding_area[0]; ?>';
	var toSouth='<?php echo $surrounding_area[1]; ?>';
	var toEast='<?php echo $surrounding_area[2]; ?>';
	var toWest='<?php echo $surrounding_area[3]; ?>';
	var toDock1='<?php echo $surrounding_area[4]; ?>';
	var toDock2='<?php echo $surrounding_area[5]; ?>';
	var toDock3='<?php echo $surrounding_area[6]; ?>';
	parent.setInfo('<?php if (!$_GET['message']) echo str_replace('-ap-','&#39;',$char['location']); else echo $_GET['message']; ?>');
	parent.SetPlacePage('loc');
	parent.marketChange(<?php echo $char['ismarket']; ?>);
	
	parent.MapX=<?php echo intval($mapCo[0]); ?>;
	parent.MapY=<?php echo intval($mapCo[1]); ?>;
	parent.UpdateMap=<?php echo $refresh_map; ?>;
	
	function ShowText(sometext)
	{
		if (sometext) parent.document.getElementById('infoSet').innerHTML="<font class='medtext'><center>&#171; "+sometext.replace('_',' ').replace(' ','_').replace(' ','_')+" &#187;";
		else parent.document.getElementById('infoSet').innerHTML="";
	}
	
	function findPos(obj) {
		var curleft = curtop = 0;
		if (obj.offsetParent) {
			curleft = obj.offsetLeft
			curtop = obj.offsetTop
			while (obj = obj.offsetParent) {
				curleft += obj.offsetLeft
				curtop += obj.offsetTop
			}
		}
		return [curleft,curtop];
	}
	
	function showInfo(text,obj)
	{
		if (text) {
			var pos=findPos(obj);
			var tile = document.getElementById(obj.id.replace("l",""));
			// NORMAL OFFSETS
			if (tile.src.indexOf("UL")!=-1) pos[1]+=50;
			if (tile.src.indexOf("_UR")!=-1) {pos[0]+=-75; pos[1]+=50;}
			if (tile.src.indexOf("_LR")!=-1) pos[0]+=-75;
			// WEIRD DOCK OFFSETS
			if (tile.src.indexOf("_UFR")!=-1) {pos[0]+=-150; pos[1]+=50;}
			if (tile.src.indexOf("_LFR")!=-1)  {pos[0]+=-150; pos[1]+=0;}
			if (tile.src.indexOf("_FLFR")!=-1)  {pos[0]+=-150; pos[1]+=-50;}
			if (tile.src.indexOf("_FLR")!=-1)  {pos[0]+=-75; pos[1]+=-50;}
			if (tile.src.indexOf("_FLL")!=-1)  {pos[0]+=0; pos[1]+=-50;}
			if (tile.src.indexOf("_bottom_")!=-1)  {pos[0]+=0; pos[1]+=-100;}
			parent.showInfo(text.replace("_"," "),pos[0],pos[1],text);
		}
		else parent.hideMe2();
	}
	
	</SCRIPT>
	
</head>
<body bgcolor="black">
  <font size=20 color=white align=center>
<?php
  echo '<center>'.$char['location'].'</center>';
?>
  </font>
  <font color=white align=center>  
  <table align=center width=100%>
    <tr>
      <td>  
        <div align='center' onClick="parent.document.getElementById('InfoPage').src='map/places/bank.php?update=1&load='+(Math.random()*1000)">
          <font color=white align=center>Bank</font>
        </div>
      </td>
      <td>
        <div align='center' onClick="parent.document.getElementById('InfoPage').src='map/places/stable.php?update=1&load='+(Math.random()*1000)">
          <font color=white align=center>Stables</font>
        </div> 
      </td>
    </tr>
    <tr>
      <td>
        <div align='center' onClick="parent.document.getElementById('TownMap').src='dice.php?update=1&load='+(Math.random()*1000)">
          <font color=white align=center>Tavern</font>
        </div>
      </td>
      <td>
        <div align='center' onClick="parent.document.getElementById('InfoPage').src='map/places/blacksmith.php?update=1&load='+(Math.random()*1000)">
          <font color=white align=center>Blacksmith</font>
        </div> 
      </td>
    </tr>
  </table>
  </font>
</body>
<footer>
</footer>
</html>