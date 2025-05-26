 <?php
error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);
$skipVerify = 1;
include('header.php');
?>

 <div class="row"align='left' style="background-color:#00000096; border-radius:25px; padding:20px; margin:20px">
      <form role="form" action="verify.php" method="post">
        <div class="form-group form-group-sm">
          <label for="email">Email:</label>
          <input type="text" class="form-control gos-form" id="email" name="email" maxlength="40">
        </div>    
        <div class="form-group form-group-sm">
          <label for="password">Password:</label>
          <input type="password" class="form-control gos-form" id="pswd" name="pswd" maxlength="20">
        </div>
        <div class="form-group form-group-sm">
          <label for="mode">Mode:</label>
          <select class="form-control gos-form" id="mode" name="mode">
            <option value='0'>Normal</option>
            <option value='1'>Lite</option>
          </select>
        </div>
        <button type="submit" class="btn btn-block btn-success">Login</button>
		<a href='reset.php' class='btn btn-block btn-danger'>Forgot Password?</a>
      </form>     
      <br/><br/>
      <center>Don't have an account? </center>
      <a href='createAccount.php' class='btn btn-block btn-warning'>Create an Account</a>
    </div>
  </div>
<?php
include('footer.htm');
?>
