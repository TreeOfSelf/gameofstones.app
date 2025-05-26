<?php if (!$head) { ?>
<html>
<head>
<title>Admin Recreate Society Table</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>
<body>
<?php } ?>
<u>Resets the table "Soc"</u><br><br>
<?php
// Connect
include_once("connect.php");

if (strtolower($name) != "the" && strtolower($lastname) != "creator" && $head != 1  && !$debug_mode)
{
  echo "Only the Creator has such powers!";
}
else
{
// Drop Old Table
$query  = 'DROP TABLE IF EXISTS Soc';
$result = mysqli_query($db,$query);
echo "<b>Results</b><br><br>Drop Old Table: $result";

// Create New Table
$query = 'CREATE TABLE IF NOT EXISTS `Soc` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(30) DEFAULT NULL,
  `leader` varchar(30) DEFAULT NULL,
  `leaderlast` varchar(30) DEFAULT NULL,
  `subleaders` text,
  `subs` int(11) DEFAULT NULL,
  `bank` bigint(20) unsigned NOT NULL,
  `align` int(11) DEFAULT NULL,
  `declared` int(11) DEFAULT NULL,
  `invite` int(11) DEFAULT NULL,
  `allow` int(11) DEFAULT NULL,
  `members` int(11) DEFAULT NULL,
  `score` int(11) DEFAULT NULL,
  `leadertitle` varchar(12) DEFAULT NULL,
  `subtitle` varchar(12) DEFAULT NULL,
  `inactivity` int(11) DEFAULT NULL,
  `flag` varchar(30) DEFAULT NULL,
  `sigil` varchar(30) DEFAULT NULL,
  `lastupkeep` int(11) DEFAULT NULL,
  `ruled` int(11) DEFAULT NULL,
  `last_war` int(11) DEFAULT NULL,
  `about` text,
  `private_info` text,
  `stance` text,
  `support` text,
  `blocked` text,
  `area_score` text,
  `area_rep` text,
  `wars` text,
  `upgrades` text,
  `goods` text,
  `offices` text,
  `ranks` text,
  PRIMARY KEY (`id`),
  KEY `name` (`name`(3))
) ENGINE=MyISAM  DEFAULT CHARSET=latin1';

$result = mysqli_query($db,$query);
echo "<br>Create New Table: $result";

// Table structure for table `Soc_stats`

// Drop Old Table
$query  = 'DROP TABLE IF EXISTS Soc_stats';
$result = mysqli_query($db,$query);
echo "<b>Results</b><br><br>Drop Old Table: $result";

// Create New Table
$query = "CREATE TABLE IF NOT EXISTS `Soc_stats` (
  `id` int(11) NOT NULL,
  `mostJiId` int(11) NOT NULL DEFAULT '0',
  `mostJiNum` int(11) NOT NULL DEFAULT '0',
  `mostMembersId` int(11) NOT NULL DEFAULT '0',
  `mostMembersNum` int(11) NOT NULL DEFAULT '0',
  `mostRuledId` int(11) NOT NULL DEFAULT '0',
  `mostRuledNum` int(11) NOT NULL DEFAULT '0',
  `mostCoinId` int(11) NOT NULL DEFAULT '0',
  `mostCoinNum` int(11) NOT NULL DEFAULT '0',
  `highAlignId` int(11) NOT NULL DEFAULT '0',
  `highAlignNum` int(11) NOT NULL DEFAULT '0',
  `lowAlignId` int(11) NOT NULL DEFAULT '0',
  `lowAlignNum` int(11) NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=latin1";

$result = mysqli_query($db,$query);
echo "<br>Create New Table: $result";

if (mysqli_num_rows(mysqli_query($db,"SELECT id FROM Soc_stats WHERE 1")) ==0)
{
  for ($i=10001; $i<=10010; $i++)
  {
    mysqli_query($db,"INSERT INTO `Soc_stats` (id) VALUES ('$i')");
	echo mysqli_error($db);
  }
}
// Drop Old Table
$query  = 'DROP TABLE IF EXISTS messages';
$result = mysqli_query($db,$query);
echo "<b>Results</b><br><br>Drop Old Table: $result";

$query = 'CREATE TABLE IF NOT EXISTS `messages` (
  `id` int(4) unsigned NOT NULL,
  `checktime` int(11) NOT NULL,
  `message` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1';
$result4 = mysqli_query($db,$query);

// CLEAR MESSAGES
if (mysqli_num_rows(mysqli_query($db,"SELECT id FROM messages WHERE 1")) ==0)
{
// Set up global chat messages
mysqli_query($db,"INSERT INTO messages (message, checktime, id) 
                           VALUES ('a:0:{}','0',      '0')");
// Set up city rumors messages
mysqli_query($db,"INSERT INTO messages (message, checktime, id) 
                           VALUES ('a:0:{}','0',      '50000')");
}
}
?>

<br><br>
<?php if (!$head) { ?>
</body>
</html>
<?php } ?>