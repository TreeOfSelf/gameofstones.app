<?php
include("connect.php");

echo "Restarting last battle! </br>";
mysqli_query($db,"DELETE FROM Hordes WHERE type = '3'");
echo "Dropped mega hordes from DB </br>";
mysqli_query($db,"DELETE FROM Contests WHERE type = '99'");
echo "Deleted final battle contest </br>";
mysqli_query($db,"UPDATE Locations SET isDestroyed = '0'");
echo "Made all locations not destroyed </br>";
$emptyMsg = serialize(array());
$timeSet = intval(time()/3600-721+73);
mysqli_query($db,"UPDATE messages SET message='$emptyMsg', checktime='$timeSet' WHERE id='50000'");
echo "Cleared messages and reset starting time </br>";
?>