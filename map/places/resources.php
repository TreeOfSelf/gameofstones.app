	<?php
		$event=rand(0,count($event_list)-1);
		
		// UPDATE WHEN STARTED TO PREVENT CHEATING
		include('info.php');
		$no_query=1;
		include('connect_info.php');
		$char = mysqli_fetch_array(mysqli_query($db,"SELECT * FROM Users LEFT JOIN Users_data ON Users.id=Users_data.id WHERE Users.id='$id'"));
		if ($char['num_res']>10) $num_to_action += intval(($char['num_res']-10)/2);
		
		if (($_GET['collect']-3.0)/67.0 == intval(($_GET['collect']-3.0)/67.0) && $char['r_numb'] != $_GET['collect']) {
			
			$resources=unserialize($char['resources']);
			// DETERMINE MAX GENERATEABLE
			include($_SERVER['DOCUMENT_ROOT']."/".$subfile."/admin/tree_skills.php");
			include($_SERVER['DOCUMENT_ROOT']."/".$subfile."/map/mapdata/coordinates.inc");
			if ($location_array[$char['location']][7] == intval($res_id) + 1) {
				$skill_tree=unserialize($char['skill_tree']);
				for($x=0; $x<3; $x++) $ability_list[$x]=$skill_tree[$skilltree[1][$x][$res_id]];
				$ability_list[3]=$skill_tree[$skilltree[1][2][3]];
						
				$find[0]=intval($ability_list[0]);
				$find[1]=intval($ability_list[1]);
				$find[2]=intval($ability_list[2]);
				$find[3]=intval($ability_list[3]);
				for($x=0; $x<4; $x++) $resources[$res_id][$x]+=$find[$x];
				if (time()-$char['r_start']<$time_to_action*$num_to_action) {echo "<font class='littletext'>You must wait a little while."; exit;}
				$char['num_res']++;
				mysqli_query($db,"UPDATE Users LEFT JOIN Users_data ON Users.id=Users_data.id SET Users.r_numb='".$_GET['collect']."', Users.num_res='".$char['num_res']."', Users.r_start='".time()."', Users_data.resources='".serialize($resources)."' WHERE Users.id='$id'");
				if (!intval($ability_list[0]+$ability_list[1]+$ability_list[2]+$ability_list[3])) $duh = "<br><br><font class='littletext'><b>You have no<br>".$title." skills!</b>";
				else $duh = "";
			}
			else $duh = "<br><br>Cheating is BAD";
		}
	?>
<link REL="StyleSheet" TYPE="text/css" HREF="style.css">
<html>
	<head>
	<SCRIPT LANGUAGE="JavaScript">
		
		parent.dontGo=0;
		var OrgTime=<?php echo $time_to_action*2; ?>;
		var TimeLeft=<?php echo $time_to_action*2; ?>;
		var startReq=<?php echo $num_to_action; ?>;
		var repReq=startReq;
		var CurEvent=<?php echo $event; ?>;
		var CounterHandle=0;
		var ToolNeeded=<?php echo $event_list[$event][1]; ?>;
		var ToolUsed=0;
		var Numbahs = new Array();
			Numbahs[0]="no";
			Numbahs[1]="one";
			Numbahs[2]="two";
			Numbahs[3]="three";
			Numbahs[4]="four";
			Numbahs[5]="five";
			Numbahs[6]="six";
			Numbahs[7]="seven";
			Numbahs[8]="eight";
			Numbahs[9]="nine";
			Numbahs[10]="ten";
		var ListEvents = new Array();
		var ListEventsTools = new Array();
		<?php
			for ($x=0; $x<count($event_list); $x++)
			{
				echo "ListEvents[".$x."]=\"".$event_list[$x][0]."\";\n";
				echo "ListEventsTools[".$x."]=".$event_list[$x][1].";\n";
			}
		?>
		var ListRes = new Array();
		<?php
			$y=0;
			for ($x=0; $x<count($resource_list); $x++)
			{
				if ($ability_list[$x]) {echo "ListRes[".$y."]= new Array(\"".$resource_list[$x]."\",".$ability_list[$x].",$x,".intval($find[$x]).");\n"; $y++;}
			}
			
			// ALLOW MAP TO GO OTHER PLACES
			if ($_POST['resources']) echo "parent.dontGo=0;";
		?>
		
		function get_random(min,max)
		{
			return Math.floor(Math.random()*(max-min))+min;
		}
		
		function startCounter()
		{
			CounterHandle = window.self.setInterval("myCounter()", 500);
		}
		
		function myCounter()
		{
			TimeLeft--;
			if (TimeLeft<0)
			{
				TimeLeft=0;
				clearInterval(CounterHandle);
				if (ToolUsed==ToolNeeded) repReq--;
				if (repReq>0) startOver();
				else changePage();
			}
			document.getElementById('moveBar').width=150*(1-(TimeLeft/OrgTime));
		}
		
		function useTool(tool)
		{
			ToolUsed=tool;
			var el = document.getElementById('tools');
			var elb = document.getElementById('bar');
			el.style.display = 'none';
			elb.style.display = 'block';
			startCounter();
			parent.dontGo="<?php echo $dontGo;?>";
		}
		
		function changePage()
		{
			location.href='<?php echo $page; ?>?collect=<?php echo (rand(900,20000) * 67 + 3); ?>';
		}
		
		function displayResults()
		{
			var el = document.getElementById('bar');
			var elb = document.getElementById('results');
			el.style.display = 'none';
			elb.style.display = 'block';
			document.getElementById('information').innerHTML="<font class='littletext_f'><i><?php echo $text_line; ?> "+Numbahs[resourcesFound]+" "+ListRes[r][0]+"</i>.";
			var text="<font class='foottext_f'><br><i><?php echo $total_text; ?></i><font class='foottext'>";
			for (var x=0; x<ListRes.length; x++) {
				if (ListRes[x][3]>0) {
					text+="<br>"+ListRes[x][3]+" "+ListRes[x][0];
				}
			}
			document.getElementById('content').innerHTML=text;
		}
		
		function startOver()
		{
			CurEvent=get_random(0,ListEvents.length);
			TimeLeft=OrgTime;
			ToolNeeded=ListEventsTools[CurEvent];
			document.getElementById('information').innerHTML="<font class='littletext_f'><i>"+ListEvents[CurEvent]+"</i>";
			document.getElementById('information').height=25;
			var el = document.getElementById('results');
			var el1 = document.getElementById('bar');
			var elb = document.getElementById('tools');
			el.style.display = 'none';
			el1.style.display = 'none';
			elb.style.display = 'block';
		}
	</SCRIPT>
	</head>
	<body>
		<font class="littletext">
			<?php
				echo "<table cellpadding='0' cellspacing='0' border='0'><tr><td><img src='tools/".$img.".gif'></td><td class='littletext'>&nbsp;<b>$title</b></td></tr></table>";
			?>
			<table border="0"><tr><td id="information" width="220" <?php if (!$_GET['collect']) echo "height='25'"; ?>><font class="littletext_f"><i><?php if (!$_GET['collect']) echo $event_list[$event][0]; ?></i></td></tr></table>
			
		<!-- SELECT TOOL -->
		<table border="0" cellpadding="0" cellspacing="0" id="tools" style="display: <?php if ($_GET['collect']) echo "none"; else echo "block";?>;">
			<tr>
				<td colspan="3">
					<font class='foottext_f'><br>Use which tool?
				</td>
			</tr>
			<tr>
			<?php
				for ($t=0; $t<count($tool_list); $t++) {
					echo "<td width='50'><a onClick='useTool($t);'><font class='foottext_f'><center><img border='0' src='tools/".strtolower(str_replace(" ","",$tool_list[$t])).".gif'><br><a href='javascript:useTool($t);'><font class='foottext_f'>".str_replace(" ","&nbsp;",$tool_list[$t])."</a></a></td>\n";	
				}
			?>
			</tr>
		</table>
		
		<!-- USE TOOL :: LOADING BAR -->
		<table id="bar" style="display: none;" width="150"><tr><td width="1" height="10" id="moveBar" bgcolor="#999999"></td><td bgcolor="#000000"></td></tr></table>
		
		<!-- RESULTS -->
		<table id="results" border="0" style="display: <?php if (!$_GET['collect']) echo "none"; else echo "block";?>;">
			<tr>
				<td id="content" class='littletext_f'>
				<?php
				echo "<i>$total_text</i><font class='foottext'>";
				for ($x=0; $x<4; $x++) if ($find[$x]) echo "<br>&nbsp;&nbsp;".intval($find[$x])." ".$res_name."s of ".$resource_list[$x];
				if (!($find[0]+$find[1]+$find[2]+$find[3])) echo "<br>&nbsp;&nbsp;Nothing".$duh;
				?>
				</td>
			<tr>
				<td></td>
			</tr>
			</tr>
			<tr>
				<td>
						<font class="foottext_f">
						<?php if (!$duh) { ?>
						[<a href="javascript:startOver();"><?php echo $again; ?></a>]
						<?php } ?>
				</td>
			</tr>
		</table>
	</body>
</html>
