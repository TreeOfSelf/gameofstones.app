<?php

function array_delel($array,$del)
{
  $arraycount1=count($array);
  $z = $del;
  while ($z < $arraycount1) {
    $array[$z] = $array[$z+1];
    $z++;
  }
  array_pop($array);
  return $array;
}

/* establish a connection with the database */
include_once("admin/connect.php");
include_once("admin/userdata.php");

$wikilink = "Game+Settings";

$avatar=mysqli_real_escape_string($db,$_POST['newav']);

$targetDirectory = 'avatar_uploads/'; // The folder where you want to save the uploaded images
$targetFile = $targetDirectory . basename($_FILES['newavupload']['name']);
$uploadOk = 1;


if (isset($_FILES['newavupload']) && is_uploaded_file($_FILES['newavupload']['tmp_name'])) {
  
  
  $baseURL = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]";

  $first = $char['name']; 
  $last = $char['lastname'];  

  $imageFileType = strtolower(pathinfo($_FILES['newavupload']['name'], PATHINFO_EXTENSION));

  // Generate the new file name
  $newFileName = $first . '_' . $last . '.' . $imageFileType;

  $targetDirectory = 'avatar_uploads/'; // The folder where you want to save the uploaded images
  $targetFile = $targetDirectory . $newFileName;
  $uploadOk = 1;

  // Check if the file is an actual image
  if (!in_array($imageFileType, array('jpg', 'jpeg', 'png', 'gif', 'webp', 'jfif'))) {
      echo "Sorry, only JPG, JPEG, PNG, and GIF files are allowed.";
  } else {
      if (move_uploaded_file($_FILES['newavupload']['tmp_name'], $targetFile)) {
          $avatar = $baseURL . '/' . $targetFile;
          echo "Uploaded successfully!";
      } else {
          echo "Sorry, there was an error uploading your file.";
      }
  }
}

$newtext=mysqli_real_escape_string($db,$_POST['aboutchar']);
$id=$char['id'];
$message="Edit character settings";

// Update avatar

if ($_POST['changer'])
{
  $message = "Character Info updated successfully";
  error_reporting(1);
  if ( $avatar && strlen($avatar) < 10000 
  /*&& (preg_match("/jpg\Z/i", $avatar) ||
  preg_match("/jpeg\Z/i", $avatar) ||
  preg_match("/bmp\Z/i", $avatar) ||
  preg_match("/webp\Z/i", $avatar) ||
   preg_match("/gif\Z/i", $avatar)
  || preg_match("/png\Z/i", $avatar)))*/)
  {
    $query = "UPDATE Users SET avatar='$avatar' WHERE id='$id'";
    $result = mysqli_query($db,$query);
    $char['avatar']=$avatar;
  }
  else 
  {
    if ($avatar)
    {
      $message = "Problem with Chosen Avatar";
    }
    else
    {
      $char['avatar']='';
      $query = "UPDATE Users SET avatar='' WHERE id=$id";
      $result = mysqli_query($db,$query);
    }
  }
}

// KILL CHARACTER
if ($_POST['killer'])
{
  $killpass = sha1($_POST['killpass']);
  if ($_POST['killmail'] == $email && $killpass == $password)
  {
    // TIE UP SOCIETY STUFF
    $soc_name = $char['society'];
    $query = "SELECT * FROM Soc WHERE name='$char[society]' ";
    $result = mysqli_query($db,$query);
    $society = mysqli_fetch_array($result);

    if ($society['id'])
    {
      // CHECK IF LEADER CHANGES
      if (strtolower($name) == strtolower($society['leader']) && strtolower($lastname) == strtolower($society['leaderlast']) )
      {
        $message = $user['name'];
        if ($society['subs']>0)
        {
          $subs = $society['subs'];
          $new_id = 9999999;
          $subleaders = unserialize($society['subleaders']);
          foreach ($subleaders as $c_n => $c_s)
          {
            if ($c_n < $new_id)
            {
              $new_id = $c_n;
            }
          }
          foreach ($subleaders as $c_n => $c_s)
          {
            if ($c_n == $new_id)
            {
              $queryb = "UPDATE Soc SET leader='$c_s[0]', leaderlast='$c_s[1]' WHERE name='$soc_name'";
              $result = mysqli_query($db,$queryb);
              --$subs;
              $subleaders[$new_id][0]=0;
              $subleaders=delete_blank($subleaders);
  
              if ($subs > 0)
              {
                $query = "UPDATE Soc SET subleaders='".serialize($subleaders)."', subs='$subs' WHERE name='".$char['society']."'";
              }
              else
              {
                $query = "UPDATE Soc SET subleaders='', subs='$subs' WHERE name='".$char['society']."'";
              }
              $result = mysqli_query($db,$query);
            }
          }
        }  
        else 
        {
          $user = mysqli_fetch_array(mysqli_query($db,"SELECT * FROM Users WHERE society='".$char['society']."' ORDER BY exp DESC LIMIT 1"));
          mysqli_query($db,"UPDATE Soc SET leader='".$user['name']."', leaderlast='".$user['lastname']."' WHERE name='".$char['society']."'");
        }
      }  
      // return all vault items
      $vid = 10000+$society['id'];
      $vresult = mysqli_query($db,"SELECT id, owner, society FROM Items WHERE owner='$char[id]' AND society > '0' AND society < '10000'");
      while ($sitem=mysqli_fetch_array($vresult))
      {
        $result = mysqli_query($db,"UPDATE Items SET owner='".$vid."', last_moved='".time()."', istatus='0' WHERE id='".$sitem['id']."'");
      }    

      // ADD TO NUMBER OF MEMBERS
      $memnumb = $society['members'] - 1;
      $query = "UPDATE Soc SET members='$memnumb' WHERE name='$soc_name' ";
      $result = mysqli_query($db,$query);

      if ($memnumb <= 0)
      {
        // IF THERE IS NO ONE LEFT IN THE CLAN THEN DELETE IT
        $stance = unserialize($society['stance']);
        foreach ($stance as $c_n => $c_s)
        {
          if ($c_s != 0)
          {
            $soc_name2 = str_replace("_"," ",$c_n);
            $society2 = mysqli_fetch_array(mysqli_query($db,"SELECT * FROM Soc WHERE name='$soc_name2' "));
            $stance2 = unserialize($society2['stance']);
            $stance2[str_replace(" ","_",$soc_name)] = 0;
            $changed_stance2 = serialize($stance2);
            mysqli_query($db,"UPDATE Soc SET stance='$changed_stance2' WHERE name='$society2[name]' ");
          }
        }

        $query = "DELETE FROM Soc WHERE name='$soc_name'";
        $result5 = mysqli_query($db,$query);
      }
    }
    
    // Delete any businesses
    $result4 = mysqli_query($db,"DELETE FROM Profs WHERE owner='$char[id]'");   
    
    // Delete any estates
    $result4 = mysqli_query($db,"DELETE FROM Estates WHERE owner='$char[id]'");  
       
    // Send a message to The Creator 
    $tc = mysqli_fetch_array(mysqli_query($db,"SELECT id, name, lastname FROM Users WHERE name='The' AND lastname='Creator' "));
    $cid = $tc['id'];
    $notesub = "OB: ".$char['name']." ".$char['lastname'];
    $note = "Born: ".$char['born']."<br/>";
    $note .= "ID: ".$char['id']."<br/>";
    $note .= "Clan: ".$char['society']."<br/>";
    $note .= "IPs:<br/>";
    $charip = unserialize($char['ip']); 
    for ($i = 0; $i < count($charip); $i++)
    {
      $note .= $charip[$i]."<br/>";
    }
    $note .= "Alts:<br/>";
    $alts = getAlts($charip);
    foreach ($alts as $aname => $anum)
    {
      $note .= $aname."<br/>";
    }
    
    $result = mysqli_query($db,"INSERT INTO Notes (from_id,to_id, del_from,del_to,type,root,sent,        cc,subject,   body,   special) 
                                      VALUES ('$cid', '$cid','0',     '0',   '0', '0', '".time()."','','$notesub','$note','')");
    mysqli_query($db,"UPDATE Users SET msgcheck='".time()."' WHERE id='$cid'");
    
    // Take Care of IP stuff 
    $ips = unserialize($char['ip']); 
    $fullname = $char['name']."_".$char['lastname'];
    for ($i = 0; $i < count($ips); $i++)
    {
      $result = mysqli_query($db,"SELECT * FROM IP_logs WHERE addy='$ips[$i]'");
      $ip_log = mysqli_fetch_array($result);
      $users= unserialize($ip_log['users']);
      for ($j=0; $j < count($users); $j++)
      {
        if ($users[$j] == $fullname)
        {
          $k=0;
          for ($k = $j; $k < count($users)-1; $k++)
          {
            $users[$k] = $users[$k+1];
          }
          $users=array_delel($users,$k);
        }
      }
      $ipcount = count($users);
      $ip_users2 = serialize($users);
      mysqli_query($db,"UPDATE IP_logs SET users='$ip_users2', num='$ipcount' WHERE addy='$ips[$i]'");
    }
    
    //Finish 'em off!
    $id = $char['id'];
    $query = "DELETE FROM Users_data WHERE id='$id'";
    $result5 = mysqli_query($db,$query);
    $query = "DELETE FROM Users WHERE id='$id'";
    $result5 = mysqli_query($db,$query);
    // Leave Users_Stats around just in case...
         
    $message = "Character deleted.";
    setcookie("id", "", time()-3600, "/");
    setcookie("name", "", time()-3600, "/");
    setcookie("lastname", "", time()-3600, "/");
    header("Location: $server_name/bio.php");
    exit;
  }
  else $message = "Invalid information given.";
}

// UPDATE PASSWORD

if ($_POST['password'] && $_POST['passworda'] && $_POST['passwordb'])
{
$char = mysqli_fetch_array(mysqli_query($db,"SELECT * FROM Users WHERE id='$id'"));

if ($_POST['passworda'] == $_POST['passwordb'] && strlen($_POST['passworda']) > 4 && strlen($_POST['passworda']) < 11 && sha1($_POST['password']) == $password)
{
$password = sha1($_POST['passworda']);
setcookie("password", "$password", time()+99999999, "/");
$query = "UPDATE Accounts SET password='$password' WHERE email='$email' ";
$result = mysqli_query($db,$query);
}
else $message = "Problem with the password";
}

// Update Character Info

if ($_POST['changer'])
{
$newtext = htmlspecialchars(stripslashes($newtext),ENT_QUOTES);
if (strlen($newtext) < 501)
{
  // if (preg_match('/^[-a-z0-9+.,!@*_&#:\/%;?\s]*$/i',$newtext))
  {
    $char['about']=$newtext;
    $query = "UPDATE Users_data SET about='$newtext' WHERE id='$id'";
    $result = mysqli_query($db,$query);
  }
  //else $message = "Some punctuation marks are not supported. Please remove them and try again.";
}
else $message="Info must be a max of 500 characters";
}

include('header.php');
?>
  <div class="row solid-back">
    <div class="col-sm-12">
      <div class='col-sm-8'>
        <div class='panel panel-info'>
          <div class='panel-heading'>
            <h3 class='panel-title'>
              Character Settings
            </h3>
          </div>
          <div class='panel-body abox'>
            <form class='form-horizontal' action="avatar.php" method="post" enctype="multipart/form-data">
              <div class="form-group form-group-sm">
                <label for='newav' class='control-label col-sm-4'>Offsite Avatar URL: </label>
                <div class='col-sm-8'>
                  <input type="text" class="form-control gos-form" name="newav" value="<?php echo $char['avatar']; ?>" id="newav" MAXLENTH="200" />
                  <i>No offensive or adult themed images<br/>
                  Leave input field blank for default avatar</i>
                </div>
                </br>
              </div>


              <div class="form-group form-group-sm">
                <label for='newav' class='control-label col-sm-4'>File Upload Avatar: </label>
                <div class='col-sm-8'>
                <input accept="image/png, image/gif, image/webp, image/jpeg, image/jfif" class="form-control gos-form" name="newavupload" id="file-upload" type="file"/>
                </div>
                </br>
              </div>


              <input type="hidden" name="changer" value="1" id="changer" />
              <div class="form-group form-group-sm">
                <label class='control-label col-sm-4'>Change Password: </label>
              </div>
              <div class="form-group form-group-sm">
                <label for='oldpass' class='control-label col-sm-4'>Old Password: </label>
                <div class='col-sm-8'>
                  <input id='oldpass' type="password" class="form-control gos-form" name="password" maxlength="20" id="password" />
                </div>
              </div>
              <div class="form-group form-group-sm">
                <label for='newpass' class='control-label col-sm-4'>New Password: </label>
                <div class='col-sm-8'>
                  <input id='newpass' type="password" class="form-control gos-form" name="passworda" maxlength="20" id="passworda" />
                </div>
              </div>
              <div class="form-group form-group-sm">
                <label for='conpass' class='control-label col-sm-4'>Confirm Password: </label>
                <div class='col-sm-8'>
                  <input id='conpass' type="password" class="form-control gos-form" name="passwordb" maxlength="20" id="passwordb" />
                </div>
              </div> 
              <div class="form-group">
                <label for='aboutchar' class='control-label col-sm-4'>Character Information: </label>
                <div class='col-sm-8'>
                  <textarea name="aboutchar" class="form-control gos-form" rows="4" wrap="soft"><?php echo $char['about']; ?></textarea>
                </div>
              </div>
              <input type="Submit" name="submit" value="Update Settings" class="btn btn-info"/>
            </form>
          </div>
        </div>
      </div>
      <div class='col-sm-4'>
        <div class='panel panel-danger'>
          <div class='panel-heading'>
            <h3 class='panel-title'>
              Kill Character
            </h3>
          </div>
          <div class='panel-body solid-back'> 
            <form action="avatar.php" name="killForm" method="post" enctype="multipart/form-data">
              <p class='text-danger h5'><i>Character and all of their data will be deleted. Confirm your password and email to delete. Once done, it cannot be undone!</i></p>
              <input type="hidden" name="killer" value="1" id="killer" />
              <div class="form-group form-group-sm">
                <label for='killpass'>Confirm Password: </label>
                <input type="password" class="form-control gos-form" name="killpass" id="killpass" />
              </div>
              <div class="form-group form-group-sm">
                <label for='killmail'>Confirm E-mail: </label>
                <input type="text" class="form-control gos-form" name="killmail" id="killmail" />
              </div>
              <a data-href="javascript:submitKill()" data-toggle="confirmation" data-placement="top" title="Warning! Once you do this, this character data will be lost forever! Are you sure?" class="btn btn-danger btn-sm btn-wrap">Kill Character</a>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
<script type="text/javascript">  
function submitKill()
{
  document.killForm.submit();
}
</script>
<?php
include('footer.htm');
?>