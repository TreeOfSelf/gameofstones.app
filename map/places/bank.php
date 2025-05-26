<?php
$bd = $town_bonuses[bD];
$max_deposit=($bd+80)/100;
      $tot_dep = floor(($char['gold']+$char['bankgold'])*$max_deposit)-$char['bankgold'];
      $gold_dep = floor($tot_dep/10000);
      $silver_dep = floor(($tot_dep - ($gold_dep*10000))/100);
      $copper_dep = floor(($tot_dep - ($gold_dep*10000)-$silver_dep*100)); 
      if ($tot_dep<0 || $clear) {$gold_dep = 0; $silver_dep = 0; $copper_dep = 0;}
?>
<link REL="StyleSheet" TYPE="text/css" HREF="style.css">
<html>
  <head>
  <SCRIPT LANGUAGE="JavaScript">
    var bankXMLHttpObj=getXMLHttpRequestObject();
    
    function doBanking (act)
    {
       var g = document.bankForm.gold.value;
       var s = document.bankForm.silver.value;
       var c = document.bankForm.copper.value;

       // open socket connection
       bankXMLHttpObj.open('POST','map/places/banker.php?',true);
       // set form http header
       bankXMLHttpObj.setRequestHeader('Content-Type','application/x-www-form-urlencoded');
       bankXMLHttpObj.send('gold='+g+'&silver='+s+'&copper='+c+'&action='+act);
       bankXMLHttpObj.onreadystatechange=bankStatusChecker;
    }
    
    function bankStatusChecker()
    {
      // check if request is completed
      if(bankXMLHttpObj.readyState==4)
      {
        if(bankXMLHttpObj.status==200)
        {        
          var response = bankXMLHttpObj.responseText.split('|');
          inPocket = response[0];
          inBank = response[1];
          newMessage = response[2];
          
          ng = 0;
          ns = 0;
          nc = 0;
          var deposit = Math.floor((parseInt(inPocket) + parseInt(inBank))*<?php echo $max_deposit;?>)-inBank;
          if (deposit > 0)
          {
            ng = Math.floor(deposit/10000);
            ns = Math.floor((deposit -(ng*10000))/100);
            nc = Math.floor((deposit -(ng*10000)-ns*100));           
          }
          var crown = 0;
          var mark = 0;
          var penny = 0;
          if (inBank > 0)
          {
            crown = Math.floor(inBank/10000);
            mark = Math.floor((inBank -(crown*10000))/100);
            penny = Math.floor((inBank -(crown*10000)-mark*100));            
          }
          
          var pcrown = 0;
          var pmark = 0;
          var ppenny = 0;
          if (inPocket > 0)
          {
            pcrown = Math.floor(inPocket/10000);
            pmark = Math.floor((inPocket -(pcrown*10000))/100);
            ppenny = Math.floor((inPocket -(pcrown*10000)-pmark*100));            
          }
          
          document.bankForm.gold.value= ng;
          document.bankForm.silver.value= ns;
          document.bankForm.copper.value= nc;
          document.getElementById('message').innerHTML= newMessage;
          document.getElementById('bankGold').innerHTML="<table class='blank'><tr><td><img src='images/till.gif'></td><td class='littletext'>&nbsp;<b>Bank:</b>"+
                                                        "<font class=littletext><img src='images/gold.gif' width='15' style='vertical-align:middle' alt='g:'>"+crown+
                                                        "<img src='images/silver.gif' width='15' style='vertical-align:middle' alt='s:'>"+mark+
                                                        "<img src='images/copper.gif' width='15' style='vertical-align:middle' alt='c:'>"+penny+"</font>"+
                                                        "</td></tr></table>"; 
          document.getElementById('pocket').innerHTML="<font class=littletext><img src='images/gold.gif' width='15' style='vertical-align:middle' alt='g:'>"+pcrown+
                                                        "<img src='images/silver.gif' width='15' style='vertical-align:middle' alt='s:'>"+pmark+
                                                        "<img src='images/copper.gif' width='15' style='vertical-align:middle' alt='c:'>"+ppenny+"</font>"; 
        }
        else{
            alert('Failed to get response :'+ bankXMLHttpObj.statusText);
        }
      }      
    }  
  </SCRIPT>  
  </head>
  <body bgcolor="black">
    <font class="littletext">
    <div id='bankGold'>
    <table class='blank'><tr><td><img src='images/till.gif'></td><td class='littletext'>&nbsp;<b>Bank:</b>
    <font class='littletext_f'>
    <?php
      echo displayGold($char['bankgold']);
    ?>
    </td></tr></table>
    </div>
    <font class="littletext"><br>
    <form name='bankForm'>
      <input type='hidden' name='cleared' value='0'>
      <img src='images/gold.gif' width='15' style='vertical-align:middle' alt='g:'>
      <input type="text" name="gold" value="<?php echo $gold_dep; ?>" class="form" size="5" maxlength="5">
      <img src='images/silver.gif' width='15' style='vertical-align:middle' alt='s:'>
      <input type="text" name="silver" value="<?php echo $silver_dep; ?>" class="form" size="2" maxlength="2">
      <img src='images/copper.gif' width='15' style='vertical-align:middle' alt='c:'>
      <input type="text" name="copper" value="<?php echo $copper_dep; ?>" class="form" size="2" maxlength="2">
      <input type="button" value="*" name="clear" class="form" onClick="clearBank();">
      <br>
      <br>
      <input type="button" value="Deposit" name="deposit" class="form" onClick="doBanking('1');">
      &nbsp;
      <input type="button" value="Withdraw" name="withdraw" class="form" onClick="doBanking('2');">
      <?php 
        if ($char['society'] != "")
        {
      ?>
      &nbsp;
      <input type="button" value="Donate" name="donate" class="form" onClick="doBanking('3');">
      <?php
        }
      ?>
    </form>
  
  </body>
  <footer>
  </footer>
</html>