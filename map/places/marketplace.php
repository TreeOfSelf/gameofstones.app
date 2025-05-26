<link REL="StyleSheet" TYPE="text/css" HREF="style.css">
<?php
	include('info.php');
?>
<html>
	<head>
	</head>
	<body bgcolor="black">
		<font class="foottext">
		<form method="post" action="<?php echo $server_name; ?>/market.php" target="_parent" id="theForm">
			Name: 
			<input type="text" name="itemname" id="itemname" class="form" maxlength="20">
			<br>
			<br>
			Type:&nbsp;&nbsp;&nbsp;
			<select name="type" size="1" class="form">
				<option selected value="">All Items</option>
				<option value="0">Armor</option>
				<option value="1">Sword</option>
				<option value="2">Axe</option>
				<option value="3">Spear</option>
				<option value="4">Bows</option>
				<option value="5">Bludgeon</option>
				<option value="6">Knives</option>
				<option value="7">Shield</option>
				<option value="12">Talisman</option>
			</select>
			<br>
			<br>
			Cost:&nbsp;&nbsp;&nbsp;
			<input type="text" name="costmin" size="5" id="costmin" value="1" class="form" maxlength="9">
			<font class="foottext_f"> to 
			<input type="text" name="costmax" size="5" id="costmax" value="99999" class="form" maxlength="10">
			<br>
			<!-- <input type="Submit" name="submit" value="Search Marketplace" class="form" style="visibility: hidden;"> -->
		</form>
		<br>
		<table border='0' cellpadding='0' cellspacing='0'><tr><td><img src='tools/scale.gif'></td><td class="foottext">&nbsp;&nbsp;[<a href="javascript:void();" onClick="document.getElementById('theForm').submit();">Search Market</a> | <a href="/charshop.php?time=<?php echo time(); ?>" target="_parent">View your Shop</a>]</td></tr></table>
	</body>
	<footer>
	</footer>
</html>