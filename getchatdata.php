<?php
// include class file
require_once ("mysql_class.php");
include_once ("admin/connect.php");

$id=mysqli_real_escape_string($db,$_GET['id']);
$msgs = mysqli_fetch_array(mysqli_query($db,"SELECT * FROM messages WHERE id='".$id."'"));
$msg = unserialize($msgs['message']);
// send messages to the client
for ($i = 0; $i < count($msg); $i++)
{
  $output = str_replace("-ap-","'",$msg[$i]);
  $output = str_replace("&amp;","&",$output);  
  $output = str_replace("&quot;","\"",$output);
  $output = str_replace("&gt;",">",$output);
  $output = str_replace("&lt;","<",$output); 
    $output = str_replace("&#39;","'",$output);   

  echo $output;
}
?>