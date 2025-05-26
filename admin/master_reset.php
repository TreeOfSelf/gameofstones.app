<html>
<head><meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>GoS Reset</title>

</head>
<body>
<?php 
include("connect.php");
// if check to prevent others from using this accidentally. Best to remove drop
// permissions from database user after a reset just to be safe
//if (strtolower($name) != "the" || strtolower($lastname) != "creator" )
//if (strtolower($name) != "the" || strtolower($lastname) != "creator" )
if(!$debug_mode && (strtolower($name) != "the" || strtolower($lastname) != "creator"))
{
  echo $name." ".$lastname;
  echo "  Only the Creator has such powers!";
} else {
echo "here";
$head = 1;
include("reset.php");
include("resetsoc.php");
include("resetquests.php");
include("resethordes.php");
include("resetestates.php");
include("resetips.php");
include("resetprofs.php");
include("resetcontests.php");
include("resetloc.php");
include("resetnotes.php");
  
if (mysqli_num_rows(mysqli_query($db,"SELECT id FROM Quests WHERE 1")) ==0)
{
  // Setup Quests
  include("create_tut_quests.php");
}
} 

// Close Database
mysqli_close($db);
?>

</body>
</html>