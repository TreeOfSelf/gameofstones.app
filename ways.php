<?php

/* establish a connection with the database */
include_once("admin/connect.php");
include_once("admin/userdata.php");
include_once('admin/locFuncs.php');



$wdest=mysqli_real_escape_string($db,$_POST['wdest']);
$gdest=mysqli_real_escape_string($db,$_POST['godest']);

echo $wdest;

$foundPaths = [];
if ( ($wdest != "" && $wdest != -1) || $_GET['area'])
{
 
  $char_location_escaped = mysqli_real_escape_string($db, $char['location']);
  $loc = $char_location_escaped;
  $raw_end = $location_list[$wdest];
  
   if( !is_null($_GET['area'])){
    $raw_end = mysqli_real_escape_string($db, $_GET['area']);
  }
  $end = mysqli_real_escape_string($db, $raw_end);
  echo $end;
  
  $x=0;
  $lpaths = mysqli_query($db,"SELECT * FROM Routes WHERE type='1' AND start='".$loc."' AND end='".$end."'");
  while ($lpath = mysqli_fetch_array($lpaths))
  {
    $myPath = unserialize($lpath['path']);
    $foundPaths[$x][0] = "";
    $foundPaths[$x]['1'] = $lpath['id'];
    for ($i=0; $i < count($myPath); $i++)
    {
      if ($i != 0) { $foundPaths[$x][0] = $foundPaths[$x][0]." <font class='text-warning'>></font> "; }
      $foundPaths[$x][0] = $foundPaths[$x][0].$myPath[$i];
    } 
    $x++;
  }
  $routeMsg = "route";
  if ($x > 1) $routeMsg .= "s";
  $message="Found ".$x." ".$routeMsg." to ".$end;
}

if ($gdest != "" && $gdest != -1)
{
  $groute = mysqli_fetch_array(mysqli_query($db,"SELECT * FROM Routes WHERE id='".$gdest."'"));
  if ($groute)
  {
    $char['route'] = $gdest;
    $char['routepoint'] = 0;
    mysqli_query($db,"UPDATE Users SET route='".$char['route']."', routepoint='".$char['routepoint']."' WHERE id=".$char['id']);
    $message =  "Guidings marked to ".$groute['end'];
  }
  else { $message = "Something happened to destroy the path. Try again."; }
}

if ($message == "")
{
  $message = "Select a destination you wish to travel to";
}

// DISPLAY
include('header.php');
?>

  <div class='row'>
    <div class="col-sm-12">
      <div class='col-sm-3 hidden-xs'></div>
      <form class='form-horizontal' action="ways.php" method="post">
        <div class="form-group form-group-sm">
          <label for='nqtype' class='control-label col-sm-2'>Travel to: </label>
          <div class='col-sm-2'>
            <select name='wdest' id='wdest' size='1' class="form-control gos-form"/>
              <option value='-1'>-Select-</option>
              <?php
                for ($i=0; $i<count($location_list); $i++)
                {
                  echo "<option value='".$i."'>$location_list[$i]</option>";
                }
              ?>
            </select>
          </div>
          <div class='col-sm-2'>
            <input type="submit" name="submit" value="Get Directions" class="btn btn-sm btn-success"/>
          </div>
        </div>
      </form>
    </div>
  </div>
  <?php
    if ($foundPaths != "")
    {
  ?>
  <form name='goForm' action='ways.php' method='post'>
    <input type='hidden' name='godest' value='0' id='godest' />
  </form>
  <div class='row'>
    <div class="col-sm-12">
      <table class="table table-condensed table-striped table-clear table-responsive solid-back">
        <tr>
          <th width="40">&nbsp;</th>
          <th style='vertical-align: bottom;'>Route</th>
          <th style='vertical-align: bottom;'>Action</th>
        </tr>
        <?php
          for ($x=0; $x<count($foundPaths); $x++)
          {
            echo "<tr><td>".($x+1)."</td>";
            echo "<td align='center'>".$foundPaths[$x][0]."</td>";
            $go_link = "javascript:submitGoForm(".$foundPaths[$x][1].")";
            echo "<td><a class='btn btn-success btn-xs btn-block btn-wrap' href=\"$go_link\">Go</a></td>";
            echo "</tr>";
          }
        ?>
      </table>
    </div>
  </div>
  <?php
    }
  ?>
<script type='text/javascript'>
  function submitGoForm(toLoc)
  {
    document.goForm.godest.value = toLoc;
    document.goForm.submit();
  }
</script>
<?php
  include('footer.htm');
?>


