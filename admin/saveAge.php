<?php 
 //Link to download file...
 //Link to download file...
 $url = "https://".$_SERVER['SERVER_NAME']."/history/hornSnapshot.php";


 //Code to get the file...
 $data = file_get_contents($url);
 $date = date('Y-m-d');
 //save as?
 $filename = "../history/ages/".$date.".html";

 //save the file...
 $fh = fopen($filename,"w");
 fwrite($fh,$data);
 fclose($fh);

 //display link to the file you just saved...
 echo "<a href='".$filename."'>Click Here</a> to download the file...";
 ?>