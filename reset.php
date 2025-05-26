 <?php
error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);
$skipVerify = 1;
include('header.php');
?>

 <div class="row"align='left' style="background-color:#00000096; border-radius:25px; padding:20px; margin:20px">
      <form role="form" action="verifyReset.php" method="post"> 
        <div class="form-group form-group-sm">
          <label for="email">Email:</label>
          <input type="text" class="form-control gos-form" id="email" name="email" maxlength="30">
        </div>
        <button type="submit" class="btn btn-block btn-danger">Reset Password</button>
      </form>     
      <br/><br/>
      <center>Don't have an account?</center>
      <a href='createAccount.php' class='btn btn-block btn-warning'>Create an Account</a>
    </div>
  </div>

<?php


include('footer.htm');
?>
