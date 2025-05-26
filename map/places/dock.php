<link REL="StyleSheet" TYPE="text/css" HREF="style.css">
<?php
	include('info.php');
	$no_query=1;
	include('connect_info.php');
	$char = mysqli_fetch_array(mysqli_query($db,"SELECT * FROM Users LEFT JOIN Users_data ON Users.id=Users_data.id WHERE Users.id='$id'"));
	include($_SERVER['DOCUMENT_ROOT']."/".$subfile.'/map/mapdata/coordinates.inc');
	
	include($_SERVER['DOCUMENT_ROOT']."/".$subfile."/admin/tree_skills.php");
	$resources=unserialize($char['resources']);
	$skill_tree=unserialize($char['skill_tree']);
	$over_limit=0;
	for ($x=0; $x<3; $x++) {
		for ($y=0; $y<3; $y++)
			if ($skill_tree[$skilltree[2][$y][$x]]*6<$resources[$x][$y]) $over_limit=1;
		if ($skill_tree[$skilltree[2][2][3]]*6<$resources[$x][3]) $over_limit=1;
	}
	
	$loc=$char['location'];

	function pathag($x,$y)
	{
		return intval(0.5+SQRT(pow($x,2)+pow($y,2)));
	}
	
	$_GET['d1']=str_replace('_',' ',$_GET['d1']);
	$_GET['d2']=str_replace('_',' ',$_GET['d2']);
	$_GET['d3']=str_replace('_',' ',$_GET['d3']);

	// cost to other ports
	$cost1=intval(pathag($location_array[$loc][0]-$location_array[$_GET['d1']][0],$location_array[$loc][1]-$location_array[$_GET['d1']][1])/3);
	$cost2=intval(pathag($location_array[$loc][0]-$location_array[$_GET['d2']][0],$location_array[$loc][1]-$location_array[$_GET['d2']][1])/3);
	$cost3=intval(pathag($location_array[$loc][0]-$location_array[$_GET['d3']][0],$location_array[$loc][1]-$location_array[$_GET['d3']][1])/3);
?>
<html>
	<head>
		<SCRIPT LANGUAGE="JAVASCRIPT">
			var Here='<?php echo $_GET['l']; ?>';
			var Loc = new Array();
			parent.sellRes=<?php echo intval($over_limit); ?>;
			Loc[0]= new Array(<?php echo $location_array[$char['location']][0].",".$location_array[$char['location']][1]; ?>);
			Loc[1]= new Array(<?php if ($_GET['d1']) echo $location_array[$_GET['d1']][0].",".$location_array[$_GET['d1']][1]; else echo "0"; ?>);
			Loc[2]= new Array(<?php if ($_GET['d2']) echo $location_array[$_GET['d2']][0].",".$location_array[$_GET['d2']][1]; else echo "0"; ?>);
			Loc[3]= new Array(<?php if ($_GET['d3']) echo $location_array[$_GET['d3']][0].",".$location_array[$_GET['d3']][1]; else echo "0"; ?>);
			
			function showArea(dir)
			{
				var speed = 35;
				if (dir==-1) {speed=0; dir=0;}
				parent.SetMapPos(Loc[dir][0],Loc[dir][1],speed);
			}
			
			function clickArrow(arrow,plac,need)
			{
				if (parent.sellRes) parent.popConfirm('You have more supplies than you are skilled to transport. Sell all extras and travel?','javascript:setTraveling('+plac+','+need+')');
				else parent.popConfirm('Travel to '+arrow.replace('-ap-',"&#39;").replace('-ap-',"&#39;").replace('-ap-',"&#39;")+' by boat?','javascript:setTraveling('+plac+','+need+')');
			}
		</SCRIPT>
	</head>
	<body bgcolor="black">
		<font class="littletext">
		<table border='0' cellpadding='0' cellspacing='0'><tr><td><img src='tools/anchor.gif'></td><td class="littletext">&nbsp;<b><?php echo str_replace('-ap-','&#39;',$char['location']); ?> Dock:</b></td></tr></table>
		<br>
		<font class='foottext_f'>Passage to:<br>
		<?php
			if ($_GET['d1']) {echo "<font class='littletext_f'><b><a name='".$_GET['d1']."' href=\"javascript:showArea(-1); clickArrow('".$_GET['d1']."',5,".($cost1*7+2).");\">".str_replace('-ap-','&#39;',$_GET['d1'])."</a></b> &nbsp; "; if ($char['gold']<$cost1) echo "<font color='ED3915'>"; else echo "<font class='littletext_f'>"; echo $cost1."g [<a href='javascript:void();' onMouseover='showArea(1);' onMouseout='showArea(0);'><b>?</b></a>]<br>\n";}
			if ($_GET['d2']) {echo "<font class='littletext_f'><b><a name='".$_GET['d2']."' href=\"javascript:showArea(-1); clickArrow('".$_GET['d2']."',6,".($cost2*7+2).");\">".str_replace('-ap-','&#39;',$_GET['d2'])."</a></b> &nbsp; "; if ($char['gold']<$cost2) echo "<font color='ED3915'>"; else echo "<font class='littletext_f'>"; echo $cost2."g [<a href='javascript:void();' onMouseover='showArea(2);' onMouseout='showArea(0);'><b>?</b></a>]<br>\n";}
			if ($_GET['d3']) {echo "<font class='littletext_f'><b><a name='".$_GET['d3']."' href=\"javascript:showArea(-1); clickArrow('".$_GET['d3']."',7,".($cost3*7+2).");\">".str_replace('-ap-','&#39;',$_GET['d3'])."</a></b> &nbsp; "; if ($char['gold']<$cost3) echo "<font color='ED3915'>"; else echo "<font class='littletext_f'>"; echo $cost3."g [<a href='javascript:void();' onMouseover='showArea(3);' onMouseout='showArea(0);'><b>?</b></a>]<br>\n";}
		?>
		<br>
		
	</body>
	<footer>
	</footer>
