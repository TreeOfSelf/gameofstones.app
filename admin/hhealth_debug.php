<?php 
include("connect.php");
$row = mysqli_fetch_assoc(mysqli_query($db,'SELECT SUM(level) AS value_sum FROM Users WHERE nation != 0')); 
$totvit = $row['value_sum'];
echo $totvit;

?>