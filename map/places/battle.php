<link REL="StyleSheet" TYPE="text/css" HREF="style.css">
<?php
	include('info.php');
	$no_query=1;
	include('connect_info.php');
	$char = mysqli_fetch_array(mysqli_query($db,"SELECT * FROM Users LEFT JOIN Users_data ON Users.id=Users_data.id WHERE Users.id='$id'"));
	
	// CHECK RESOURCES
	include($_SERVER['DOCUMENT_ROOT']."/".$subfile."/admin/tree_skills.php");
	$resources=unserialize($char['resources']);
	$skill_tree=unserialize($char['skill_tree']);
	$over_limit=0;
	for ($x=0; $x<3; $x++) {
		for ($y=0; $y<3; $y++)
			if ($skill_tree[$skilltree[2][$y][$x]]*6<$resources[$x][$y]) $over_limit=1;
		if ($skill_tree[$skilltree[2][2][3]]*6<$resources[$x][3]) $over_limit=1;
	}
?>
<html>
	<head>
	</head>
	<body bgcolor="black">
		<table border="0" cellpadding="0" cellspacing="0" height="20">
			<tr>
				<td>
					<center><img src='tools/wins.gif' alt='Held by:'>
				</td>
				<td class='littletext' valign='middle'>
					<p align='left'>
					&nbsp;
					<b>
					<?php
					echo $char['location'];
					?>
					</b>
				</td>
			</tr>
			<tr>
				<td class='foottext_f'>
					[<a href="javascript:parent.SetPlacePage('set_travel');">Travel</a>]
				</td>
				<td>
				</td>
			</tr>
			<tr>
				<td>
					<!--<img src='tools/wins.gif' alt='Held by:'>-->
				</td>
				<td class='foottext_f' valign='middle'>
					<br>
					<p align='left'>
					&nbsp;Under control of<br>
					<font class='foottext'>
					<?php
					$loc_stat = mysqli_fetch_array(mysqli_query($db,"SELECT own, war FROM Trade WHERE location='".$char['location']."'"));
					$soc = $loc_stat['own'];
					$war = $loc_stat['war'];
					if ($soc && $soc != $char['society']) echo "<a target='_parent' href='/".$subfile."/joinclan.php?time=".time()."&name=$soc'><font class='littletext'><b>$soc</b></a>";
					elseif ($soc == $char['society']) echo "<a target='_parent' href='/".$subfile."/clan.php?time=".time()."'><font class='littletext'><b>$soc</b></a>";
					else echo "<font class='littletext'><b>No Organization</b>";
					?>
					<br><br>
					<?php
					if ($char['society'] && !$war) {
					?>
						<font class='foottext_f'>
						&nbsp;[<?php echo "<a target='_parent' href='/".$subfile."/clan.php?time=".time()."&war=".$char['location']."'>"; ?>Incite War</a>]
					<?php
					}
					elseif ($war)
					{
					?>
						<font class='foottext_f'>
						&nbsp;At war with<br>
						<?php
						echo "<a target='_parent' href='/".$subfile."/joinclan.php?name=$war'><font class='littletext'><b>$war</b></a>";
					}
					?>
				</td>
			</tr>
		</table>
	</body>
	<footer>
	</footer>
</html>
