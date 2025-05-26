<?php
  $loc_query=$char['location'];
  $bd = $town_bonuses['bD'];
  $max_deposit=($bd+80)/100;
  $min_out = 20-$bd;
  $ustats = mysqli_fetch_array(mysqli_query($db,"SELECT * FROM Users_stats WHERE id='$id'"));
  
  // do banking 
      $_POST['gold']=abs((int)$_POST['gold']);
      $_POST['silver']=abs((int)$_POST['silver']);
      $_POST['copper']=abs((int)$_POST['copper']);
      $money = $_POST['gold']*10000+$_POST['silver']*100+$_POST['copper'];
      $bankAction = $_POST['action'];
      include($_SERVER['DOCUMENT_ROOT']."/".$subfile."/map/mapdata/coordinates.inc");
      if (intval($money) && $location_array[$char['location']][2])
      {
        if ($bankAction != '')
        {
          if ($bankAction == 1) // deposit
          {
            if (($char['gold']+$char['bankgold'])*$max_deposit>=intval($money)+$char['bankgold'] && $char['gold']>=intval($money))
            {
              $char['gold']-=intval($money);
              $char['bankgold']+=intval($money);
              $char['bankgold']--;
              $char['lastbank']=time();
              $ustats['deposits']++;
              mysqli_query($db,"UPDATE Users SET gold='".$char['gold']."', bankgold='".$char['bankgold']."', lastbank='".$char['lastbank']."' WHERE id='".$char['id']."'");
              mysqli_query($db,"UPDATE Locations SET bank=bank+1 WHERE name='$loc_query'");
              mysqli_query($db,"UPDATE Users_stats SET deposits='".$ustats['deposits']."', coin='".$char['gold']."', bankcoin='".$char['bankgold']."' WHERE id='".$id."'");
              $message=displayGold(intval($money))." deposited";
            }
            else $message="You must keep at least $min_out% of your money out";
          }
          if ($bankAction == 2) // withdraw
          {
            if ($char['bankgold']>=intval($money))
            {
              $char['bankgold']-=intval($money);
              $char['gold']+=intval($money);
              if ($char['bankgold']>0) $char['bankgold']--;
              else $char['gold']--;
              $char['lastbank']=time();
              $ustats['withdrawls']++;
              mysqli_query($db,"UPDATE Users SET gold='".$char['gold']."', bankgold='".$char['bankgold']."', lastbank='".$char['lastbank']."' WHERE id='".$char['id']."'");
              mysqli_query($db,"UPDATE Locations SET bank=bank+1 WHERE name='$loc_query'");
              mysqli_query($db,"UPDATE Users_stats SET withdrawls='".$ustats['withdrawls']."', coin='".$char['gold']."', bankcoin='".$char['bankgold']."' WHERE id='".$id."'");
              $message=displayGold(intval($money))." withdrawn";
            }
            else $message="There is not that much money in your account";
          }
          if ($bankAction == 3) // donate
          {
            if ($char['bankgold']>=intval($money))
            {
              $soc_name = $char['society'];
              $query = "SELECT * FROM Soc WHERE name='$soc_name'";
              $result = mysqli_query($db,$query);
              $society = mysqli_fetch_array($result);
              $char['bankgold']-=intval($money);
              $society['bank']+=intval($money);
              $char['lastbank']=time();
              $ustats['coin_donated'] += $money;
              mysqli_query($db,"UPDATE Users SET bankgold='".$char['bankgold']."', lastbank='".$char['lastbank']."' WHERE id='".$char['id']."'");
              mysqli_query($db,"UPDATE Soc SET bank='$society[bank]' WHERE name='$char[society]' ");
              mysqli_query($db,"UPDATE Users_stats SET coin='".$char['gold']."', bankcoin='".$char['bankgold']."', coin_donated='".$ustats['coin_donated']."' WHERE id='".$id."'");
              $message=displayGold(intval($money))." donated to ".$soc_name;
            }
            else $message="There is not that much money in your account";
          }
          if (!($message) || $message == '') $message = "The banker gives you a blank stare...";
        }         
      }

?>