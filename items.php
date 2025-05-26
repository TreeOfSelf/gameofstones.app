<?php
// establish a connection with the database
include_once("admin/connect.php");
include_once("admin/userdata.php");
include_once("admin/charFuncs.php");
include_once("admin/duelFuncs.php");
include_once("admin/locFuncs.php");
include_once("map/mapdata/coordinates.inc");
// Find all equipped items
include_once("admin/equipped.php");

$wikilink = "Inventory";

$sortBy = mysqli_real_escape_string($db, $_POST['sort']);
$sortCBy = mysqli_real_escape_string($db, $_POST['sort2']);
if ($sortBy != "") {
  mysqli_query($db, "UPDATE Users SET sort_inv='" . $sortBy . "' WHERE id=$id");
  $char['sort_inv'] = $sortBy;
}
if ($sortCBy != "") {
  mysqli_query($db, "UPDATE Users SET sort_consume='" . $sortCBy . "' WHERE id=$id");
  $char['sort_consume'] = $sortCBy;
}

$invSort = sortItems($char['sort_inv']);
$consumeSort = sortItems($char['sort_consume']);

$id = $char['id'];
$itmlist = [];
$listsize = 0;
$wEq = 0;
$a = -1;
$b = -1;
$c = -1;
$d = -1;
$e = -1;
$currw = [];
$iresult = mysqli_query($db, "SELECT * FROM Items WHERE owner='$id' AND type<15 " . $invSort);
while ($qitem = mysqli_fetch_array($iresult)) {

  $itmlist[$listsize++] = $qitem;
  if ($qitem['istatus'] == 1) $a = $listsize - 1;
  else if ($qitem['istatus'] == 2) $b = $listsize - 1;
  else if ($qitem['istatus'] == 3) $c = $listsize - 1;
  else if ($qitem['istatus'] == 4) $d = $listsize - 1;
  else if ($qitem['istatus'] == 5) $e = $listsize - 1;
}

$currw = mysqli_fetch_array(mysqli_query($db, "SELECT * FROM Items WHERE owner='$char[id]' AND type='8'"));
if ($currw['istatus'] > 0) $wEq = 1;


$clistsize = 0;
$pouch = [];
$stomach = [];
$fullness = 0;
$presult = mysqli_query($db, "SELECT * FROM Items WHERE owner='$id' AND type>=19 " . $consumeSort);
while ($qitem = mysqli_fetch_array($presult)) {
  if ($qitem['istatus'] == 0) $pouch[$clistsize++] = $qitem;
  else $stomach[$fullness++] = $qitem;
}

$types = unserialize($char['type']);

$message = mysqli_real_escape_string($db, $_GET['message']);
$action = mysqli_real_escape_string($db, $_POST['action']);
$consume = mysqli_real_escape_string($db, $_POST['consume']);
$inv = mysqli_real_escape_string($db, $_POST['inv']);
$tab = mysqli_real_escape_string($db, $_REQUEST['tab']);
$newWeave = mysqli_real_escape_string($db, $_POST['newweave']);

$myWeaves = [];
$tskills = '';

$doequip = mysqli_real_escape_string($db, $_REQUEST['doequip']);

$bonuses1 = $stomach_bonuses1 . " " . $stamina_effect1 . " " . $clan_bonus1 . " " . $skill_bonuses1 . " " . $clan_building_bonuses;
$skills = $bonuses1;

// if a channeler, get my weaves
if (isChanneler($types)) {
  $myWeaves = getWeaves($item_list, $item_base, $skills);
}

if ($doequip) {

  $e1 = mysqli_real_escape_string($db, $_REQUEST['equip1']);
  $e2 = mysqli_real_escape_string($db, $_REQUEST['equip2']);
  $e3 = mysqli_real_escape_string($db, $_REQUEST['equip3']);
  $e4 = mysqli_real_escape_string($db, $_REQUEST['equip4']);
  $e5 = mysqli_real_escape_string($db, $_REQUEST['equip5']);
  $e1n = mysqli_real_escape_string($db, $_REQUEST['doequipid1']);
  $e2n = mysqli_real_escape_string($db, $_REQUEST['doequipid2']);
  $e3n = mysqli_real_escape_string($db, $_REQUEST['doequipid3']);
  $e4n = mysqli_real_escape_string($db, $_REQUEST['doequipid4']);
  $e5n = mysqli_real_escape_string($db, $_REQUEST['doequipid5']);



  $good = 1;

  $aid = $itmlist[$a]['id'];
  $bid = $itmlist[$b]['id'];
  $cid = $itmlist[$c]['id'];
  $did = $itmlist[$d]['id'];
  $eid = $itmlist[$e]['id'];
  $e1id = $itmlist[$e1]['id'];
  $e2id = $itmlist[$e2]['id'];
  $e3id = $itmlist[$e3]['id'];
  $e4id = $itmlist[$e4]['id'];
  $e5id = $itmlist[$e5]['id'];

  if ($e1id != "" && $e1id != $e1n) $good = 0;
  if ($e2id != "" && $e2id != $e2n) $good = 0;
  if ($e3id != "" && $e3id != $e3n) $good = 0;
  if ($e4id != "" && $e4id != $e4n) $good = 0;
  if ($e5id != "" && $e5id != $e5n) $good = 0;


  //echo $e1id."=".$e1n."|".$e2id."=".$e2n."|".$e3id."=".$e3n."|".$e4id."=".$e4n."|".$e5id."=".$e5n;

  if ($good) {
    if ($a >= 0)  mysqli_query($db, "UPDATE Items SET istatus='0' WHERE id='" . $aid . "'");
    if ($b >= 0)  mysqli_query($db, "UPDATE Items SET istatus='0' WHERE id='" . $bid . "'");
    if ($c >= 0)  mysqli_query($db, "UPDATE Items SET istatus='0' WHERE id='" . $cid . "'");
    if ($d >= 0)  mysqli_query($db, "UPDATE Items SET istatus='0' WHERE id='" . $did . "'");
    if ($e >= 0)  mysqli_query($db, "UPDATE Items SET istatus='0' WHERE id='" . $eid . "'");
    if ($e1 >= 0) mysqli_query($db, "UPDATE Items SET istatus='1' WHERE id='" . $e1id . "'");
    if ($e2 >= 0) mysqli_query($db, "UPDATE Items SET istatus='2' WHERE id='" . $e2id . "'");
    if ($e3 >= 0) mysqli_query($db, "UPDATE Items SET istatus='3' WHERE id='" . $e3id . "'");
    if ($e4 >= 0) mysqli_query($db, "UPDATE Items SET istatus='4' WHERE id='" . $e4id . "'");
    if ($e5 >= 0) mysqli_query($db, "UPDATE Items SET istatus='5' WHERE id='" . $e5id . "'");

    $listsize = 0;
    $iresult = mysqli_query($db, "SELECT * FROM Items WHERE owner='$id' AND type<14 " . $invSort);


    while ($qitem = mysqli_fetch_array($iresult)) {
      $itmlist[$listsize++] = $qitem;

      if ($qitem['istatus'] == 1) $a = $listsize - 1;
      else if ($qitem['istatus'] == 2) $b = $listsize - 1;
      else if ($qitem['istatus'] == 3) $c = $listsize - 1;
      else if ($qitem['istatus'] == 4) $d = $listsize - 1;
      else if ($qitem['istatus'] == 5) $e = $listsize - 1;
    }
    $message = "Equipment changed!";
  } else // bad
  {
    $message = "Something odd happened while equipping. Please try again.";
  }
}

// change weaves
if ($newWeave >= 0 && $newWeave != '') {
  // check that weave was found
  if ($currw) {
    $nextw = $currw;
    $nextw['base'] = $myWeaves[$newWeave][0];
    if ($wEq) {
      $currwPts = lvl_req(getIstats($currw, $skills), getTypeMod($skills, $currw['type']));
      $nextwPts = lvl_req(getIstats($nextw, $skills), getTypeMod($skills, $nextw['type']));
      $ptsDiff = $nextwPts - $currwPts;
      $ptsLeft = $char['equip_pts'] - $char['used_pts'];
      if ($ptsLeft <= $ptsDiff) $nextw['istatus'] = -2;
    }
    mysqli_query($db, "UPDATE Items SET istatus='" . $nextw['istatus'] . "', base='" . $nextw['base'] . "' WHERE id=$currw[id]");
    $message = "You are now ready to use " . ucwords($myWeaves[$newWeave][0]);
    $currw = $nextw;
  } else {
    $message = "Something odd happened. Please try again...";
  }
}
$invWeave = $currw['base'];

$good = 1;
$oldlist = $itmlist;
$oldpouch = $pouch;

$ustats = mysqli_fetch_array(mysqli_query($db, "SELECT * FROM Users_stats WHERE id='$id'"));

// EQUIPMENT ACTIONS

// SELL ITEMS
if ($action == 1) // SELL
{
  // var_dump($_POST); die(); // DEBUG: Check submitted data
  if ($location_array[$char['location']][2]) {
    $x = 0;
    $value = 0;
    $q = 0;
    while ($x < 100) {
      $tmpItm = isset($_POST["$x"]) ? mysqli_real_escape_string($db, $_POST["$x"]) : null; // Use string key
      if ($tmpItm) {
        $sitem = mysqli_fetch_array(mysqli_query($db, "SELECT * FROM Items WHERE id='$tmpItm'"));
        // --- DEBUG START ---
        // echo "Checking item ID: $tmpItm<br>";
        // var_dump($sitem);
        // if (!$sitem) { echo "Item not found in DB!<br>"; continue; } // Skip if item doesn't exist
        // echo "Society Check: " . ($sitem['society'] == 0 ? 'OK' : 'FAIL (' . $sitem['society'] . ')') . "<br>";
        // echo "Type Check: " . ($sitem['type'] > 0 ? 'OK' : 'FAIL (' . $sitem['type'] . ')') . "<br>";
        // echo "Status Check: " . ($sitem['istatus'] == 0 ? 'OK' : 'FAIL (' . $sitem['istatus'] . ')') . "<br>";
        // --- DEBUG END ---
        if (($sitem['society'] == 0 || $sitem['society'] == "") && $sitem['type'] > 0) { // Allow 0 or empty string for society
          if ($sitem['istatus'] == 0) {
            if ($sitem['type'] > 13) $value += intval(0.5 * $item_base[$sitem['name']][2]);
            else $value += intval(0.5 * item_val(iparse($sitem, $item_base, $item_ix, $ter_bonuses)));

            if ($sitem['type'] == 14) {
              $value += intval(0.5 * $item_base[$sitem['base']][2]);
            }

            $result5 = mysqli_query($db, "DELETE FROM Items WHERE id='$sitem[id]'");
            // echo "Item $tmpItm processed for selling. Incrementing q.<br>"; // DEBUG
            $q++;
          }
        }
      }
      $x++;
    }
    // echo "Loop finished. Final q value: $q<br>"; // DEBUG
    // die("Debug finished."); // DEBUG

    $listsize = $listsize - $q;
    if ($good && $q > 0) {
      $s = "";
      if ($q > 1) $s = "s";
      $chargold = $value + $char['gold'];
      $ustats['item_earn'] += $value;
      $result = mysqli_query($db, "UPDATE Users_stats SET item_earn='" . $ustats['item_earn'] . "', items_sold= items_sold + " . $q . " WHERE id='" . $id . "'");
      $result = mysqli_query($db, "UPDATE Users SET gold='" . $chargold . "' WHERE id='$id'");
      $message = "Item$s sold for " . displayGold($value);
    } else {
      $message = "You didn't select anything to sell!";
    }
  } else {
    $message = "You can't do that from here! ";
  }
} elseif ($action == 2) // CACHE / UNCACHE
{
  $itm = 0;
  $processed_count = 0; // Count items selected & found
  $updated_count = 0; // Count items actually updated
  // echo "--- Cache/Uncache Start ---<br>"; // DEBUG
  while ($itm < 100) { // Loop through potential POST keys 0-99
    $tmpItm = isset($_POST["$itm"]) ? mysqli_real_escape_string($db, $_POST["$itm"]) : null;
    if ($tmpItm) {
      // echo "Processing POST key: $itm, Item ID: $tmpItm<br>"; // DEBUG
      // Fetch only necessary fields + check owner
      $sitem = mysqli_fetch_array(mysqli_query($db, "SELECT id, istatus FROM Items WHERE id='$tmpItm' AND owner='$id'"));
      if ($sitem) { // Check if item exists and belongs to user
        // echo "Item found. Current status: " . $sitem['istatus'] . "<br>"; // DEBUG
        $processed_count++; // Item selected and found
        // $sql = ""; // DEBUG
        // $affected_rows = -1; // DEBUG
        if ($sitem['istatus'] == 0) { // If currently uncached, cache it
          // echo "Attempting to Cache (set status to -2)...<br>"; // DEBUG
          $sql = "UPDATE Items SET istatus='-2' WHERE id='$tmpItm'"; // DEBUG: Assign query
          $result = mysqli_query($db, $sql);
          $affected_rows = mysqli_affected_rows($db); // DEBUG: Store result
          // echo "Query: $sql <br> Query Success: " . ($result ? 'Yes' : 'No') . "<br> Rows Affected: $affected_rows<br>"; // DEBUG
          if ($result && $affected_rows > 0) $updated_count++; // Increment only if DB row changed
        } elseif ($sitem['istatus'] == -2) { // If currently cached, uncache it
          // echo "Attempting to Uncache (set status to 0)...<br>"; // DEBUG
          $sql = "UPDATE Items SET istatus='0' WHERE id='$tmpItm'"; // DEBUG: Assign query
          $result = mysqli_query($db, $sql);
          $affected_rows = mysqli_affected_rows($db); // DEBUG: Store result
          // echo "Query: $sql <br> Query Success: " . ($result ? 'Yes' : 'No') . "<br> Rows Affected: $affected_rows<br>"; // DEBUG
          if ($result && $affected_rows > 0) $updated_count++; // Increment only if DB row changed
        } else { // DEBUG
            // echo "Item status (".$sitem['istatus'].") is neither 0 nor -2. Doing nothing.<br>"; // DEBUG
        } // DEBUG
        // If status is neither 0 nor -2 (e.g., equipped), do nothing.
      } else { // DEBUG
          // echo "Item ID $tmpItm not found or doesn't belong to owner $id.<br>"; // DEBUG
      } // DEBUG
    }
    $itm++;
  }
  // echo "--- Cache/Uncache End ---<br>Processed: $processed_count, Updated: $updated_count<br>"; // DEBUG
  // Feedback logic based on counts
  if ($updated_count > 0) {
      $message = "Cache Updated!";
  } elseif ($processed_count > 0) {
       $message = "Selected items already in the desired cache state."; // More specific message
  } else {
      $message = "No valid items were selected for caching/uncaching."; // Changed message slightly
  }
} elseif ($action == 3) // DONATE
{
  $soc_name = $char['society'];
  $society = mysqli_fetch_array(mysqli_query($db, "SELECT * FROM Soc WHERE name='$soc_name'"));
  $upgrades = unserialize($society['upgrades']);
  $maxvault = 50 + 10 * $upgrades[0];
  $vaultsize = mysqli_num_rows(mysqli_query($db, "SELECT * FROM Items WHERE society='$society[id]'"));

  $z = 0;
  $x = 0;
  $q = 0;
  $dlist = [];

  //while ($x < $listsize)
  while ($x < 100) {
    $tmpItm = isset($_POST["$x"]) ? mysqli_real_escape_string($db, $_POST["$x"]) : null; // Use string key
    if ($tmpItm) {
      $sitem = mysqli_fetch_array(mysqli_query($db, "SELECT * FROM Items WHERE id='$tmpItm'"));
      if (($sitem['society'] == 0 || $sitem['society'] == "") && $sitem['type'] > 0) { // Allow 0 or empty string for society
        if ($sitem['istatus'] == 0) {
          $dlist[$q] = $sitem;
          $q++;
        }
      }
    }
    $x++;
  }
  if ($vaultsize + $q > $maxvault) {
    $good = 0;
    $message = "There is not enough room in the vault!";
  }
  if ($good && $q > 0) {
    $nowner = 10000 + $society['id'];
    for ($x = 0; $x < $q; $x++) {
      $result = mysqli_query($db, "UPDATE Items SET society='" . $society['id'] . "', owner='$nowner', last_moved='" . time() . "' WHERE id='" . $dlist[$x]['id'] . "'");
      $ustats['items_donated']++;
    }
    $s = "";
    if ($q > 1) $s = "s";
    $result = mysqli_query($db, "UPDATE Users_stats SET items_donated='" . $ustats['items_donated'] . "' WHERE id='" . $id . "'");
    $message = "Item$s donated to clan vault";
  } else if ($good && $q == 0) { // Add else if for no items selected/valid
      $message = "No valid items selected for donation.";
  }
} elseif ($action == 4) // DROP
{
  if (!$location_array[$char['location']][2]) {
    $z = 0;
    $x = 0;
    $q = 0;
    while ($x < $listsize) {
      $tmpItm = isset($_POST["$x"]) ? mysqli_real_escape_string($db, $_POST["$x"]) : null; // Use string key
      if ($tmpItm) {
        $sitem = mysqli_fetch_array(mysqli_query($db, "SELECT * FROM Items WHERE id='$tmpItm'"));
        if (($sitem['society'] == 0 || $sitem['society'] == "") && $sitem['type'] > 0) { // Allow 0 or empty string for society
          if ($sitem['istatus'] == 0) {
            $result5 = mysqli_query($db, "DELETE FROM Items WHERE id='$sitem[id]'");
            $q++;
            $ustats['items_dropped']++;
          }
        }
      }
      $x++;
    }
    $listsize = $listsize - $q;
    if ($good && $q > 0) {
      $s = "";
      if ($q > 1) $s = "s";
      $ustats['item_earn'] += $value;
      $result = mysqli_query($db, "UPDATE Users_stats SET items_dropped='" . $ustats['items_dropped'] . "' WHERE id='" . $id . "'");
      $message = "Item$s dropped.";
    } else {
      $message = "You didn't select anything to drop!";
    }
  } else {
    $message = "You can't do that from here! ";
  }
} elseif ($action == 5) // ESTATE
{
  $myEstate = mysqli_fetch_array(mysqli_query($db, "SELECT * FROM Estates WHERE owner='$id' AND location='$char[location]'"));
  $upgrades = unserialize($myEstate['upgrades']);
  $maxestinv = 3 + 3 * $upgrades[2];
  $eid = 20000 + $myEstate['id'];
  $estinvsize = mysqli_num_rows(mysqli_query($db, "SELECT id FROM Items WHERE owner='$eid'"));


  $z = 0;
  $x = 0;
  $q = 0;
  $dlist = [];
  //  while ($x < $listsize)
  while ($x < 100) {
    $tmpItm = isset($_POST["$x"]) ? mysqli_real_escape_string($db, $_POST["$x"]) : null; // Use string key
    if ($tmpItm) {
      $sitem = mysqli_fetch_array(mysqli_query($db, "SELECT * FROM Items WHERE id='$tmpItm'"));
      if (($sitem['society'] == 0 || $sitem['society'] == "") && $sitem['type'] > 0) { // Allow 0 or empty string for society
        if ($sitem['istatus'] == 0) {

          if ($estinvsize + $q < $maxestinv) {
            $dlist[$q++] = $sitem;
          } else {
            $message = "There is not enough room at your estate!";
            $good = 0;
          }
        }
      }
    }
    $x++;
  }
  if ($good && $q > 0) {
    $nowner = 20000 + $myEstate['id'];
    for ($x = 0; $x < $q; $x++) {
      $result = mysqli_query($db, "UPDATE Items SET owner='$nowner', last_moved='" . time() . "' WHERE id='" . $dlist[$x]['id'] . "'");
    }
    $s = "";
    if ($q > 1) $s = "s";
    $message = "Item$s sent to Estate";
  }
}
// CONSUMABLE ACTIONS
// SELL ITEMS
if ($consume == 1) // SELL
{
  // var_dump($_POST); die(); // DEBUG: Check submitted data
  if ($location_array[$char['location']][2]) {
    $x = 0;
    $value = 0;
    $q = 0;
    while ($x < 100)
    //while ($x < $clistsize)
    {
      $tmpItm = isset($_POST["$x"]) ? mysqli_real_escape_string($db, $_POST["$x"]) : null; // Use string key
      if ($tmpItm) {
        $sitem = mysqli_fetch_array(mysqli_query($db, "SELECT * FROM Items WHERE id='$tmpItm'"));
        if ($sitem['istatus'] == 0) {
          if ($sitem['type'] != 14) {
            $value += intval(0.5 * $item_base[$pouch[$x]['base']][2]);
          } else {
            $value = intval(0.5 * $item_base[$sitem['base']][2]);
          }
          $result5 = mysqli_query($db, "DELETE FROM Items WHERE id='$sitem[id]'");
          $q++;
        }
      }
      $x++;
    }
    $clistsize = $clistsize - $q;

    if ($good && $q > 0) {
      $s = "";
      if ($q > 1) $s = "s";
      $chargold = $value + $char['gold'];
      $querya = "UPDATE Users SET gold='" . $chargold . "' WHERE id='$id'";
      $result = mysqli_query($db, $querya);
      $message = "Item$s sold for " . displayGold($value);
    } else {
      $message = "You didn't select anything!";
    }
  } else {
    $message = "You can't do that from here! ";
  }
} elseif ($consume == 2) // DROP
{
  if (!$location_array[$char['location']][2]) {
    $x = 0;
    $q = 0;
    while ($x < $clistsize) {
      $tmpItm = isset($_POST["$x"]) ? mysqli_real_escape_string($db, $_POST["$x"]) : null; // Use string key
      if ($tmpItm) {
        $sitem = mysqli_fetch_array(mysqli_query($db, "SELECT * FROM Items WHERE id='$tmpItm'"));
        if ($sitem['istatus'] == 0) {
          $result5 = mysqli_query($db, "DELETE FROM Items WHERE id='$sitem[id]'");
          $q++;
        }
      }
      $x++;
    }
    $clistsize = $clistsize - $q;

    if ($good && $q > 0) {
      $s = "";
      if ($q > 1) $s = "s";
      $message = "Item$s dropped.";
    } else {
      $message = "You didn't select anything to drop!";
    }
  } else {
    $message = "You can't do that from here! ";
  }
} else if ($consume == 3) // consume
{
  $z = 0;
  $x = 0;
  $q = 0;
  $eatlist = [];

  $newstam = $char['stamina'];
  $resourceUsed = 0;

  while ($x < 100) {
    $tmpItm = isset($_POST["$x"]) ? mysqli_real_escape_string($db, $_POST["$x"]) : null; // Use string key
    if ($tmpItm) {
      $sitem = mysqli_fetch_array(mysqli_query($db, "SELECT * FROM Items WHERE id='$tmpItm'"));
      //FOOD ITEM
      if ($sitem['type'] != 14) {
        if ($fullness + $q < 2) {
          $eatlist[$q++] = $sitem;
        } else {
          $message = $message . "</br>You are too full to consume more right now! ";
          $good = 0;
        }
      } else {

        $resourceUsed = 1;

        //Get locations current resources
        $query = "SELECT * FROM Locations WHERE name='$char[location]'";
        $result = mysqli_query($db, $query);
        $location = mysqli_fetch_array($result);

        if ($location) {


          //'cheap','average','fine','quality'

          $amount = 20;

          if ($sitem['prefix'] == "average") {
            $amount = 40;
          }

          if ($sitem['prefix'] == "fine") {
            $amount = 80;
          }

          if ($sitem['prefix'] == "quality") {
            $amount = 130;
          }

          $amount += rand(0, intval($amount / 2));

          $location[$sitem['base']] += $amount;

          $query = "UPDATE Locations SET fish='$location[fish]', 
            fish='$location[fish]', 
            grain='$location[grain]', 
            lumber='$location[lumber]', 
            luxury='$location[luxury]', 
            stone='$location[stone]',
            livestock='$location[livestock]' 
            WHERE name='$char[location]'";
          $result = mysqli_query($db, $query);
          $message = $message . "</br> Added " . $amount . " " . $sitem['base'] . " to " . $location['name'];

          mysqli_query($db, "DELETE FROM Items WHERE id='$sitem[id]'");
        } else {
          $message = $message . "</br> You can only provide this to a city.";
        }
      }
    }
    $x++;
  }

  if ($good && $q > 0) {
    for ($x = 0; $x < $q; $x++) {
      $stats = cparse($eatlist[$x]['stats']);
      if ($eatlist[$x]['type'] == 20) $pro_turnup = $pro_stats['hU'];
      elseif ($eatlist[$x]['type'] == 21) $pro_turnup = $pro_stats['dU'];
      elseif ($eatlist[$x]['type'] == 19) {
        $newstam += $stats['M'] + $pro_stats['fS'];
        if ($newstam > $char['stamaxa']) $newstam = $char['stamaxa'];
        $pro_turnup = $pro_stats['fU'];
      }
      $digest = $stats['dC'];
      $eatlist[$x]['istatus'] = $digest + $pro_turnup;
      $result = mysqli_query($db, "UPDATE Items SET istatus='" . $eatlist[$x]['istatus'] . "' WHERE id='" . $eatlist[$x]['id'] . "'");
      $ustats['items_consumed']++;
    }

    $s = "";
    if ($q > 1) $s = "s";
    $result = mysqli_query($db, "UPDATE Users_stats SET items_consumed='" . $ustats['items_consumed'] . "' WHERE id='" . $id . "'");
    $resulta = mysqli_query($db, "UPDATE Users SET stamina='$newstam' WHERE id='$id'");
    $message = $message . "</br>Item$s consumed. Mmm! Tasty!";
  } elseif ($good && $q == 0 && $resourceUsed == 0) {
    $message = "You didn't select anything to consume!";
  }
}

$itmlist = [];
$listsize = 0;
$iresult = mysqli_query($db, "SELECT * FROM Items WHERE owner='$id' AND type<15 " . $invSort);
while ($qitem = mysqli_fetch_array($iresult)) {
  $itmlist[$listsize++] = $qitem;
}

$clistsize = 0;
$pouch = [];
$stomach = [];
$fullness = 0;
$presult = mysqli_query($db, "SELECT * FROM Items WHERE owner='$id' AND type>=14  " . $consumeSort);
while ($qitem = mysqli_fetch_array($presult)) {
  if ($qitem['istatus'] == 0) $pouch[$clistsize++] = $qitem;
  else $stomach[$fullness++] = $qitem;
}

$a = [];
$b = [];
$c = [];
$d = [];
$e = [];
$a = mysqli_fetch_array(mysqli_query($db, "SELECT * FROM Items WHERE owner='$char[id]' AND istatus='1' AND type<14"));
$b = mysqli_fetch_array(mysqli_query($db, "SELECT * FROM Items WHERE owner='$char[id]' AND istatus='2' AND type<14"));
$c = mysqli_fetch_array(mysqli_query($db, "SELECT * FROM Items WHERE owner='$char[id]' AND istatus='3' AND type<14"));
$d = mysqli_fetch_array(mysqli_query($db, "SELECT * FROM Items WHERE owner='$char[id]' AND istatus='4' AND type<14"));
$e = mysqli_fetch_array(mysqli_query($db, "SELECT * FROM Items WHERE owner='$char[id]' AND istatus='5' AND type<14"));

for ($i = 0; $i < $listsize; ++$i) {
  $invinfo[$i] = "<div class='panel panel-success' style='width: 150px;'><div class='panel-heading'><h3 class='panel-title'>" . ucwords($itmlist[$i]['prefix'] . " " . $itmlist[$i]['base']) . " " . str_replace("Of", "of", ucwords($itmlist[$i]['suffix'])) . "</h3></div><div class='panel-body abox' align='center'><img class='img-responsive hidden-xs img-optional-nodisplay' border='0' bordercolor='black' src='items/" . str_replace(' ', '', $itmlist[$i]['base']) . ".gif'/>";
  if ($itmlist[$i]['type'] == 8) {
    $invinfo[$i] .= itm_info(cparse(weaveStats($itmlist[$i]['base'], $skills)));
  } else {
    $invinfo[$i] .= itm_info(cparse(iparse($itmlist[$i], $item_base, $item_ix, $ter_bonuses)));
  }
  $invinfo[$i] .= "</div></div>";
}
for ($i = 0; $i < $clistsize; ++$i) {
  $pic_name = "";
  $name_words = explode(" ", $pouch[$i]['base']);

  $start = 1;
  if (count($name_words) == 1) {
    $start = 0;
  }

  for ($x = $start; $x < count($name_words); $x++) {
    $pic_name .= $name_words[$x];
  }
  $coninfo[$i] = "<div class='panel panel-warning' style='width: 150px;'><div class='panel-heading'><h3 class='panel-title'>" . ucwords($pouch[$i]['base']) . "</h3></div><div class='panel-body abox' align='center'><img class='img-responsive hidden-xs img-optional-nodisplay' border='0' bordercolor='black' src='items/" . str_replace(' ', '', $pic_name) . ".gif'/>";
  $coninfo[$i] .= itm_info(cparse(iparse($pouch[$i], $item_base, $item_ix, $ter_bonuses)));
  $coninfo[$i] .= "</div></div>";
}

for ($i = 0; $i < $fullness; ++$i) {
  $pic_name = "";
  $name_words = explode(" ", $stomach[$i]['base']);
  for ($x = 1; $x < count($name_words); $x++) {
    $pic_name .= $name_words[$x];
  }


  $stominfo[$i] = "<div class='panel panel-info' style='width: 150px;'><div class='panel-heading'><h3 class='panel-title'>" . ucwords($stomach[$i]['base']) . "</h3></div><div class='panel-body abox' align='center'><img class='img-responsive hidden-xs img-optional-nodisplay' border='0' bordercolor='black' src='items/" . str_replace(' ', '', $pic_name) . ".gif'/>";
  $stominfo[$i] .= itm_info(cparse(iparse($stomach[$i], $item_base, $item_ix, $ter_bonuses)));
  $stominfo[$i] .= "</div></div>";
}

if (!$tab) $tab = 1;
include('header.php');
?>
<div class="row solid-back">
  <div class="col-sm-12">
    <div id="content">
      <ul id="invtabs" class="nav nav-tabs" data-tabs="tabs">
        <li <?php if ($tab == 1) echo "class='active'"; ?>><a href="#equip_tab" data-toggle="tab">Equipment</a></li>
        <li <?php if ($tab == 2) echo "class='active'"; ?>><a href="#inv_tab" data-toggle="tab">Inventory</a></li>
        <li <?php if ($tab == 3) echo "class='active'"; ?>><a href="#cons_tab" data-toggle="tab">Consumables</a></li>
        <?php
        if (isChanneler($types)) {
        ?>
          <li <?php if ($tab == 4) echo "class='active'"; ?>><a href="#weaves_tab" data-toggle="tab">Weaves</a></li>
        <?php
        }
        ?>
      </ul>

      <div class="tab-content">
        <div class="tab-pane <?php if ($tab == 1) echo 'active'; ?>" id="equip_tab">
          <?php
          $y = 0;
          $a = [];
          $b = [];
          $c = [];
          $d = [];
          $e = [];
          $a = mysqli_fetch_array(mysqli_query($db, "SELECT * FROM Items WHERE owner='$char[id]' AND istatus='1' AND type<14"));
          $b = mysqli_fetch_array(mysqli_query($db, "SELECT * FROM Items WHERE owner='$char[id]' AND istatus='2' AND type<14"));
          $c = mysqli_fetch_array(mysqli_query($db, "SELECT * FROM Items WHERE owner='$char[id]' AND istatus='3' AND type<14"));
          $d = mysqli_fetch_array(mysqli_query($db, "SELECT * FROM Items WHERE owner='$char[id]' AND istatus='4' AND type<14"));
          $e = mysqli_fetch_array(mysqli_query($db, "SELECT * FROM Items WHERE owner='$char[id]' AND istatus='5' AND type<14"));
          $estats = getstats($a, $b, $c, $d, $e, $skills);
          $tskills = $skills . " " . $estats[0];
          $pts_tot = 0;
          if (!empty($a)) $pts_tot += (lvl_req($estats[1], getTypeMod($tskills, $a['type'])));
          if (!empty($b)) $pts_tot += (lvl_req($estats[2], getTypeMod($tskills, $b['type'])));
          if (!empty($c)) $pts_tot += (lvl_req($estats[3], getTypeMod($tskills, $c['type'])));
          if (!empty($d)) $pts_tot += (lvl_req($estats[4], getTypeMod($tskills, $d['type'])));
          if (!empty($e)) $pts_tot += (lvl_req($estats[5], getTypeMod($tskills, $e['type'])));

          if ($pts_tot != $char['used_pts']) {
            $result = mysqli_query($db, "UPDATE Users SET used_pts='$pts_tot' WHERE id=$id");
          }
          ?>
          <!-- MAIN PAGE -->
          <form name="equipForm" action="items.php" method="post">
            <div class="row">
              <br />
              <div class="col-sm-6 col-md-8">
                <div class='row'>
                  <?php
                  // DRAW EQUIPPED ITEMS
                  $time = time();
                  $item_one = 0;
                  $last_blit = 0;
                  for ($placeequip = 1; $placeequip < 6; $placeequip++) {
                    $itemtoblit = 0;
                    $blited = 0;
                    $itemtoblit = mysqli_fetch_array(mysqli_query($db, "SELECT * FROM Items WHERE owner='$char[id]' AND istatus='$placeequip' AND type<14"));
                    echo "<div class='col-xs-5ths'>";
                    if ($itemtoblit['id']) {
                      $blited = 1;
                      $weapon = $estats[$placeequip];
                      $itmStats = itm_info(cparse($weapon));
                      $pointcost = (lvl_req($weapon, getTypeMod($tskills, $itemtoblit['type'])));
                  ?>
                      <button type="button" class="btn btn-primary btn-xs btn-wrap" data-html="true" data-toggle="popover" data-placement="top" data-content="<?php echo $itmStats; ?>"><?php echo iname($itemtoblit); ?></button><br />
                      <?php
                      echo "<font class='small'>";
                      if ($itemtoblit['type'] < 12 && $itemtoblit['type'] != 8) echo $itemtoblit['cond'] . "% | ";
                      echo $pointcost . "pts</font>";
                      ?>
                      <img class="img-responsive hidden-xs img-optional-nodisplay" border="0" bordercolor="black" src="items/<?php echo str_replace(" ", "", $itemtoblit['base']); ?>.gif" valign='bottom' />
                      <br />
                    <?php
                    }

                    // Draw nothing equipped images
                    if ($blited == 0) {
                    ?>
                      <button type="button" class="btn btn-default btn-sm">Empty</button><br />
                  <?php
                      if ($placeequip == 1) {
                        echo "<img class='img-responsive hidden-xs img-optional-nodisplay' src='items/hand1.gif' border=0/>";
                      } elseif ($placeequip == 5) {
                        echo "<img class='img-responsive hidden-xs img-optional-nodisplay' src='items/hand2.gif' border=0/>";
                      } elseif ($blited == 0) {
                        echo "<img class='img-responsive hidden-xs img-optional-nodisplay' src='items/torso" . $char['sex'] . ".gif' border=0/>";
                      }
                    }
                    echo "</div>";
                  }
                  $style = "style='border-width: 0px; border-bottom: 1px solid #333333'";
                  ?>
                </div>
                <div class='row'>
                  <div class='col-xs-9 col-sm-10'>
                    <?php
                    for ($i = 1; $i <= 5; ++$i) {
                      echo "<div class='row'>";
                      echo "<div class='col-xs-2'>";
                      if ($i == 1 || $i == 5) echo "Hand: ";
                      else echo "Body: ";
                      echo "</div>";
                      echo "<div class='col-xs-10'><select class='form-control gos-form input-sm' name='equip$i' id='equip$i' style='width: 95%' onchange=\"javascript:updateEquipLists(0);\"><option value='-1'>Unequipped</option></select>";
                      echo "<input type='hidden' name='equip" . $i . "name' id='equip" . $i . "name' value=''/></div>";
                      echo "</div>";
                    }
                    ?>
                  </div>
                  <div class='col-xs-3 col-sm-2'><input style="white-space: normal;" type='button' value='Change Gear' class='btn btn-sm btn-success' onclick='javascript:doEquip()' /></div>
                </div>
                <input type='hidden' name='doequip' id='doequip' value='0' />
                <input type='hidden' name='doequipid1' id='doequipid1' value='0' />
                <input type='hidden' name='doequipid2' id='doequipid2' value='0' />
                <input type='hidden' name='doequipid3' id='doequipid3' value='0' />
                <input type='hidden' name='doequipid4' id='doequipid4' value='0' />
                <input type='hidden' name='doequipid5' id='doequipid5' value='0' />
                <br />
              </div>
              <div class="col-sm-3 col-md-2 col-xs-6">
                <div class="panel panel-info">
                  <div class="panel-heading">
                    <h3 class="panel-title">Current Stats</h3>
                  </div>
                  <div class="panel-body abox" id='currstats'>
                    <?php echo "<center>My Stats!</center>"; ?>
                  </div>
                </div>
              </div>
              <div class="col-sm-3 col-md-2 col-xs-6">
                <div class="panel panel-success">
                  <div class="panel-heading">
                    <h3 class="panel-title">New Stats</h3>
                  </div>
                  <div class="panel-body abox" id='newstats'>
                    <?php echo "<center>My Stats!</center>"; ?>
                  </div>
                </div>
              </div>
            </div>
          </form>
          <br /><br />
        </div> <!-- close equip_tab content -->
        <div class="tab-pane <?php if ($tab == 2) echo 'active'; ?>" id="inv_tab">
          <div class="row">
            <div class="col-sm-12">
              <div class='row'>
                <form name="sortForm" action="items.php" method="post">
                  <input type="hidden" name="sort" id="sort" value="" />
                  <input type="hidden" name="tab" value="1" />
                </form>
                <form id="itemForm" name="itemForm" action="items.php" method="post">
                  <table class="table table-condensed table-responsive table-hover table-clear small">
                    <tr>
                      <th width="15"><input type="checkbox" name="toggler" onClick="javascript:checkedAll()" /></td>
                      <th width="275"><a class="btn btn-success btn-md btn-block" href="javascript:resortForm('0');">Item</a></th>
                      <th width="75"><a class="btn btn-success btn-md btn-block" href="javascript:resortForm('1');">Type</a></th>
                      <th width="50"><a class="btn btn-success btn-md btn-block" href="javascript:resortForm('7');">Status</a></th>
                      <th width="110"><a class="btn btn-success btn-md btn-block" href="javascript:resortForm('5');">Worth</a></th>
                      <th width="60"><a class="btn btn-success btn-md btn-block" href="javascript:resortForm('2');">Points</a></th>
                    </tr>
                    <?php
                    $draw_some = 0;
                    for ($itemtoblit = 0; $itemtoblit < $listsize; $itemtoblit++) {
                      // DRAW ITEM IN LIST
                    ?>
                      <tr>
                        <?php
                        if ($itmlist[$itemtoblit]['society'] == 0 || $itmlist[$itemtoblit]['society'] == "") {
                        ?>
                          <td width="15"><input type="checkbox" name="<?php echo $itemtoblit; ?>" value="<?php echo $itmlist[$itemtoblit]['id']; ?>" /></td>
                        <?php
                        } else {
                          echo "<td width='15'>&nbsp;</td>";
                        }
                        if ($itmlist[$itemtoblit]['type'] == 8) {
                          $weapon = weaveStats($itmlist[$itemtoblit]['base'], $tskills);
                        } else {
                          $weapon = itp($itmlist[$itemtoblit]['stats'], $itmlist[$itemtoblit]['type'], $itmlist[$itemtoblit]['cond']);
                        }
                        $itmStats = itm_info(cparse($weapon));

                        $iclass = "btn-primary";
                        if (lvl_req($weapon, getTypeMod($tskills, $itmlist[$itemtoblit]['type'])) > $char['equip_pts'] / 2) $iclass = "btn-danger";
                        if ($itmlist[$itemtoblit]['istatus'] == -2) $iclass = "btn-warning"; // Style for Cached items
                        if ($itmlist[$itemtoblit]['type'] == 8 && !isChanneler($types)) $iclass = "btn-danger";
                        if ($itmlist[$itemtoblit]['type'] == 1 && $char['nation'] == 1) $iclass = "btn-danger";
                        if ($itmlist[$itemtoblit]['istatus'] > 0) $iclass = "btn-info";
                        ?>
                        <td width="275" class="popcenter">
                          <button type="button" class="btn <?php echo $iclass; ?> btn-sm btn-block btn-wrap link-popover" data-toggle="popover" data-html="true" data-placement="bottom" data-content="<?php echo $invinfo[$itemtoblit]; ?>"><?php echo iname($itmlist[$itemtoblit]); ?></button>
                        </td>

                        <!-- DISPLAY ITEM TYPE -->
                        <td width="75" align='center'>
                          <?php
                          echo ucwords($item_type[$itmlist[$itemtoblit]['type']]);
                          ?>
                        </td>

                        <!-- DISPLAY ITEM CONDITION -->
                        <td width="50" align='center'>
                          <?php
                          if ($itmlist[$itemtoblit]['type'] < 12 && $itmlist[$itemtoblit]['type'] != 8) echo $itmlist[$itemtoblit]['cond'] . "%";
                          else echo "-";
                          ?>
                        </td>
                        <!-- STATUS -->
                        <td width="110" align='center'>
                          <?php
                          $points = lvl_req($weapon, getTypeMod($tskills, $itmlist[$itemtoblit]['type']));

                          if ($itmlist[$itemtoblit]['type'] == 8 || $itmlist[$itemtoblit]['type'] == 14) $worth = $item_base[$itmlist[$itemtoblit]['base']][2];
                          else $worth = item_val($weapon);
                          echo displayGold($worth);
                          ?>
                        </td>
                        <td width="60" align='center'>
                          <?php
                          echo $points . "pts";
                          ?>
                        </td>
                      </tr>
                    <?php
                      $draw_some = 1;
                    }
                    if ($draw_some == 0)
                      echo "<tr><td colspan='8'><center><font class='littletext_f'><br/><b>You are not carrying any items</b><br/><br/></font></center></td></tr>";
                    ?>
                  </table>
                  <?php
                  if ($location_array[$char['location']][2]) {
                  ?>
                    <a data-href="javascript:submitFormItem(1);" data-toggle="confirmation" data-placement="top" title="Sell selected items?" class="btn btn-success btn-sm btn-wrap">Sell Selected</a>
                  <?php
                  } else {
                  ?>
                    <a data-href="javascript:submitFormItem(4);" data-toggle="confirmation" data-placement="top" title="Drop selected items?" class="btn btn-danger btn-sm btn-wrap">Drop Selected</a>
                  <?php
                  }
                  ?>
                  <input type="hidden" name="action" value="0" />
                  <input type="hidden" name="tab" value="1" />
                  <a data-href="javascript:submitFormItem(2);" data-toggle="confirmation" data-placement="top" title="Cache/Uncache selected items?" class="btn btn-warning btn-sm btn-wrap">Cache/Uncache</a>
                  <?php
                  if ($char['society'] != "") {
                  ?>
                    <a data-href="javascript:submitFormItem(3);" data-toggle="confirmation" data-placement="top" title="Donate selected items to your clan's vault?" class="btn btn-primary btn-sm btn-wrap">Donate to Vault</a>
                  <?php
                  }
                  $myEstate = mysqli_fetch_array(mysqli_query($db, "SELECT * FROM Estates WHERE owner='$id' AND location='$char[location]'"));
                  if ($myEstate['id']) {
                    $upgrades = unserialize($myEstate['upgrades']);
                    $maxestinv = 3 + 3 * $upgrades[2];
                    $eid = 20000 + $myEstate['id'];
                    $estinvsize = mysqli_num_rows(mysqli_query($db, "SELECT * FROM Items WHERE owner='$eid' AND type<=14"));
                  ?>
                    <a data-href="javascript:submitFormItem(5);" data-toggle="confirmation" data-placement="top" title="Send selected items to your local Estate?" class="btn btn-info btn-sm btn-wrap">Send to Estate (<?php echo $estinvsize . "/" . $maxestinv; ?>)</a>
                  <?php
                  }
                  ?>
                </form>
              </div>
            </div>
          </div>
        </div> <!-- close inv_tab content -->
        <div class="tab-pane <?php if ($tab == 3) echo 'active'; ?>" id="cons_tab">
          <div class="row">
            <div class="col-sm-3 col-sm-push-9">
              <div class="panel panel-info">
                <div class="panel-heading">
                  <h3 class="panel-title">Items in your System</h3>
                </div>
                <div class="panel-body abox">
                  <?php
                  $draw_some = 0;
                  for ($itemtoblit = 0; $itemtoblit < $fullness; $itemtoblit++) {
                    // DRAW ITEM IN LIST
                    $iclass = "btn-info";
                  ?>
                    <div class='row'>
                      <div class='col-xs-12'>
                        <button type="button" class="btn <?php echo $iclass; ?> btn-xs btn-block btn-wrap link-popover" data-html="true" data-toggle="popover" data-placement="bottom" data-content="<?php echo $stominfo[$itemtoblit]; ?>"><?php echo iname($stomach[$itemtoblit]); ?></button>
                        Turn remaining: <?php echo $stomach[$itemtoblit]['istatus']; ?>
                      </div>
                    </div>
                  <?php
                    $draw_some = 1;
                  }
                  if ($draw_some == 0)
                    echo "<div class='col-xs-12'><center><b>You have nothing in your system</b><br/><br/></center></div>";
                  ?>
                </div>
              </div>
            </div>
            <div class="col-sm-9 col-sm-pull-3">
              <div class='row'>
                <form name="sortForm2" action="items.php" method="post">
                  <input type="hidden" name="sort" id="sort" value="" />
                  <input type="hidden" name="tab" value="1" />
                </form>
                <form id="consumeForm" name="consumeForm" action="items.php" method="post">
                  <table class="table table-condensed table-responsive table-hover table-clear small">
                    <tr>
                      <th width="15"><input type="checkbox" name="toggler" onClick="javascript:checkedAll()" /></th>
                      <th><a class="btn btn-warning btn-md btn-block" href="javascript:resortForm('0');">Item</a></th>
                      <th><a class="btn btn-warning btn-md btn-block" href="javascript:resortForm('1');">Type</a></th>
                      <th><a class="btn btn-warning btn-md btn-block" href="javascript:resortForm('5');">Worth</a></th>
                    </tr>
                    <?php
                    $draw_some = 0;
                    for ($itemtoblit = 0; $itemtoblit < $clistsize; $itemtoblit++) {
                      if ($pouch[$itemtoblit]['istatus'] == 0 || $pouch[$itemtoblit]['istatus'] == -2) {
                        $consStats = itm_info(cparse($pouch[$itemtoblit]['stats']));
                        // DRAW ITEM IN LIST
                    ?>
                        <tr>
                          <td width="15"><input type="checkbox" name="<?php echo $itemtoblit; ?>" value="<?php echo $pouch[$itemtoblit]['id']; ?>" /></td>
                          <?php
                          $iclass = "btn-primary";
                          ?>
                          <td width="275" class="popcenter">
                            <button type="button" class="btn <?php echo $iclass; ?> btn-sm btn-block btn-wrap link-popover" data-toggle="popover" data-html="true" data-placement="bottom" data-content="<?php echo $coninfo[$itemtoblit]; ?>"><?php echo iname($pouch[$itemtoblit]); ?></button>
                          </td>

                          <!-- DISPLAY ITEM TYPE -->
                          <td width="75" align='center'>
                            <?php
                            echo ucwords($item_type[$pouch[$itemtoblit]['type']]);
                            ?>
                          </td>
                          <!-- STATUS -->
                          <td width="110" align='center'>
                            <?php
                            $worth = $item_base[$pouch[$itemtoblit]['base']][2];

                            echo displayGold($worth);
                            ?>
                          </td>
                        </tr>
                    <?php
                        $draw_some = 1;
                      }
                    }
                    if ($draw_some == 0)
                      echo "<tr><td colspan='8' align='center'><br/><b>You are not carrying any consumables items</b><br/><br/></td></tr>";
                    ?>
                  </table>


                  <?php
                  if ($location_array[$char['location']][2]) {
                  ?>
                    <a data-href="javascript:submitConsumeForm(1);" data-toggle="confirmation" data-placement="top" title="Sell selected items?" class="btn btn-success btn-sm btn-wrap">Sell Selected</a>
                  <?php
                  } else {
                  ?>
                    <a data-href="javascript:submitConsumeForm(2);" data-toggle="confirmation" data-placement="top" title="Drop selected items?" class="btn btn-danger btn-sm btn-wrap">Drop Selected</a>
                  <?php
                  }
                  ?>
                  <input type="hidden" name="consume" value="0" />
                  <input type="hidden" name="tab" value="3" />
                  <a data-href="javascript:submitConsumeForm(3);" data-toggle="confirmation" data-placement="top" title="Use selected items?" class="btn btn-info btn-sm btn-wrap">Use Selected</a>
                </form>
              </div>
            </div>
          </div>
        </div> <!-- close cons_tab content -->
        <?php
        if (isChanneler($types)) {
          $myWeaves = getWeaves($item_list, $item_base, $tskills);
        ?>

          <div class="tab-pane <?php if ($tab == 4) echo 'active'; ?>" id="weaves_tab">
            <div class="row">
              <div class='col-sm-3 col-sm-push-9'>
                <div id='weavestats' width='95%'>
                  <?php echo "<div class='panel panel-danger'><div class='panel-heading'><h3 class='panel-title'>Weave Stats</h3></div><div class='panel-body abox'>Click on a Weave to display current stats!</div></div>"; ?>
                </div>
                <a data-href="javascript:submitWeaveForm('<?php echo $wEq; ?>');" data-toggle="confirmation" data-placement="top" title="Switch to use selected weave?" class="btn btn-info btn-sm btn-wrap">Switch Weave</a>
              </div>
              <div class="col-sm-9 col-sm-pull-3">
                <div class='row'>
                  <form name="weaveForm" action="items.php" method="post">
                    <input type="hidden" name="tab" value="3" />
                    <input type="hidden" name="newweave" value="-1" />
                  </form>
                  <table class="table table-condensed table-responsive table-hover table-clear small">
                    <tr>
                      <th><a class="btn btn-default btn-md btn-block" href="#">Weave</a></th>
                      <th><a class="btn btn-default btn-md btn-block" href="#">Pts</a></th>
                      <th><a class="btn btn-danger btn-md btn-block" href="#">Fire</a></th>
                      <th><a class="btn btn-primary btn-md btn-block" href="#">Water</a></th>
                      <th><a class="btn btn-info btn-md btn-block" href="#">Spirit</a></th>
                      <th><a class="btn btn-warning btn-md btn-block" href="#">Air</a></th>
                      <th><a class="btn btn-success btn-md btn-block" href="#">Earth</a></th>
                    </tr>
                    <?php
                    for ($w = 0; $w < count($myWeaves); $w++) {
                      $ptscost = lvl_req($myWeaves[$w][1], getTypeMod($tskills, 8));
                      $iclass = 'primary';
                      if ($ptscost > $char['equip_pts'] / 2) $iclass = "danger";
                      if ($invWeave == $myWeaves[$w][0]) $iclass = 'info';
                      echo "<tr>";
                      echo "<td width=115 align='center'><a class='btn btn-block btn-sm btn-" . $iclass . "' href=\"javascript:weaveSwapinfo('" . $w . "')\">" . ucwords($myWeaves[$w][0]) . "</a></td>";
                      echo "<td width=40 align='center'>" . $ptscost . "</font></center></td>";
                      echo "<td width=95 class='text-danger' align='center'>" . itm_info(cparse($myWeaves[$w][2], 0)) . "</td>";
                      echo "<td width=95 class='text-primary' align='center'>" . itm_info(cparse($myWeaves[$w][3], 0)) . "</td>";
                      echo "<td width=95 class='text-info' align='center'>" . itm_info(cparse($myWeaves[$w][4], 0)) . "</td>";
                      echo "<td width=95 class='text-warning' align='center'>" . itm_info(cparse($myWeaves[$w][5], 0)) . "</td>";
                      echo "<td width=95 class='text-success' align='center'>" . itm_info(cparse($myWeaves[$w][6], 0)) . "</td>";
                    }
                    ?>
                  </table>
                </div>
              </div>
            </div>
          </div> <!-- close weaves_tab content -->
        <?php
        }
        ?>
      </div> <!-- close tab content -->
    </div> <!-- close tabbable -->
  </div> <!-- close outer col -->
</div> <!-- close row -->
<script type="text/javascript">
  var inv = new Array();
  var skills = '<?php echo $skills; ?>';
  var myPts = <?php echo $char['equip_pts']; ?>;
  var cnation = <?php echo $char['nation']; ?>;
  var ai = -1;
  var bi = -1;
  var ci = -1;
  var di = -1;
  var ei = -1;
  <?php
  for ($i = 0; $i < $listsize; ++$i) {
    echo "  inv[" . $i . "] = new Array('" . str_replace(" ", "_", $itmlist[$i]['base']) . "','" . str_replace(" ", "_", $itmlist[$i]['prefix']) . "','" . str_replace(" ", "_", $itmlist[$i]['suffix']) . "','" . $itmlist[$i]['cond'] . "','" . $itmlist[$i]['id'] . "');";
    if ($itmlist[$i]['istatus'] == 1)      echo "  ai=" . $i . ";";
    else if ($itmlist[$i]['istatus'] == 2) echo "  bi=" . $i . ";";
    else if ($itmlist[$i]['istatus'] == 3) echo "  ci=" . $i . ";";
    else if ($itmlist[$i]['istatus'] == 4) echo "  di=" . $i . ";";
    else if ($itmlist[$i]['istatus'] == 5) echo "  ei=" . $i . ";";
  }

  if (isChanneler($types)) {
    echo "  var weaveinfo = new Array();";
    for ($w = 0; $w < count($myWeaves); $w++) {
      $weaveinfo[$w] = "<div class='panel panel-danger'><div class='panel-heading'><h3 class='panel-title'>" . ucwords($myWeaves[$w][0]) . "</h3></div><div class='panel-body abox' align='center'><img class='img-responsive hidden-xs img-optional-nodisplay' border='0' bordercolor='black' src='items/" . str_replace(' ', '', $myWeaves[$w][0]) . ".gif'/>";
      $weaveinfo[$w] .= itm_info(cparse(weaveStats($myWeaves[$w][0], $skills)));
      $weaveinfo[$w] .= "</div></div>";
      echo "  weaveinfo[" . $w . "] = \"" . $weaveinfo[$w] . "\";\n";
    }
  }
  ?>

  function submitFormItem(act) // also used by myquests.php
  {
    document.itemForm.action.value = act;
    document.itemForm.submit();
  }

  function swapinfo(myitm) {
    document.getElementById('itemstats').innerHTML = invinfo[myitm];
  }

  function swapcinfo(myitm) {
    document.getElementById('citemstats').innerHTML = coninfo[myitm];
  }

  function swapsinfo(myitm) {
    document.getElementById('citemstats').innerHTML = stominfo[myitm];
  }

  function weaveSwapinfo(myitm) {
    document.weaveForm.newweave.value = myitm;
    document.getElementById('weavestats').innerHTML = weaveinfo[myitm];
  }

  function updateEquipLists(init) {
    var equip1 = document.getElementById('equip1');
    var equip2 = document.getElementById('equip2');
    var equip3 = document.getElementById('equip3');
    var equip4 = document.getElementById('equip4');
    var equip5 = document.getElementById('equip5');
    var a = equip1.value;
    var b = equip2.value;
    var c = equip3.value;
    var d = equip4.value;
    var e = equip5.value;
    if (init != 0) {
      a = ai;
      b = bi;
      c = ci;
      d = di;
      e = ei;
    }

    var estats = getstats(a, b, c, d, e);
    var tskills = skills + " " + estats[0];

    equip1.options.length = 0;
    equip2.options.length = 0;
    equip3.options.length = 0;
    equip4.options.length = 0;
    equip5.options.length = 0;

    var t1 = 0;
    var t2 = 0;
    var t3 = 0;
    var t4 = 0;
    var t5 = 0;
    if (a != -1) t1 = item_base[inv[a][0]][1];
    if (b != -1) t2 = item_base[inv[b][0]][1];
    if (c != -1) t3 = item_base[inv[c][0]][1];
    if (d != -1) t4 = item_base[inv[d][0]][1];
    if (e != -1) t5 = item_base[inv[e][0]][1];

    var tmp = '';
    var ttype = 0;
    var tpts = 0;
    var tstats = '';
    var aielsword = 0;
    for (var i = -1; i < inv.length; i++) {
      aielsword = 0;
      if (i >= 0) {
        ttype = item_base[inv[i][0]][1];
        if (ttype == 8) {
          tstats = weaveStats(inv[i][0], tskills);
        } else {
          tstats = iparse(inv[i][0], inv[i][1], inv[i][2], inv[i][3]);
        }
        if (cnation == 1 && ttype == 1) aielsword = 1;

        tpts = (lvl_req(tstats, getTypeMod(tskills, item_base[inv[i][0]][1])));
        if (myPts > tpts * 2 && aielsword == 0) {
          tmp = ucwords(inv[i][1].replace(/_/g, " ") + " " + inv[i][0].replace(/_/g, " ") + " " + inv[i][2].replace(/_/g, " ") + " - " + tpts);

          if (ttype > 0 && ttype != 14) {
            if (ttype < 9 || ttype == 12) {
              if (ttype != t5) equip1.options[equip1.options.length] = new Option(tmp, i);
              if (ttype != t1) equip5.options[equip5.options.length] = new Option(tmp, i);
              if (i == a) {
                equip1.selectedIndex = equip1.options.length - 1;
              } else if (i == e) {
                equip5.selectedIndex = equip5.options.length - 1;
              }
            } else {
              if (ttype != t3 && ttype != t4) equip2.options[equip2.options.length] = new Option(tmp, i);
              if (ttype != t2 && ttype != t4) equip3.options[equip3.options.length] = new Option(tmp, i);
              if (ttype != t2 && ttype != t3) equip4.options[equip4.options.length] = new Option(tmp, i);
              if (i == b) {
                equip2.selectedIndex = equip2.options.length - 1;
              } else if (i == c) {
                equip3.selectedIndex = equip3.options.length - 1;
              } else if (i == d) {
                equip4.selectedIndex = equip4.options.length - 1;
              }
            }
          }
        }
      } else {
        equip1.options[equip1.options.length] = new Option('Unequipped', i);
        equip2.options[equip2.options.length] = new Option('Unequipped', i);
        equip3.options[equip3.options.length] = new Option('Unequipped', i);
        equip4.options[equip4.options.length] = new Option('Unequipped', i);
        equip5.options[equip5.options.length] = new Option('Unequipped', i);
      }
    }
    calcstats(a, b, c, d, e, 'newstats');

  }

  function getstats(myitm1, myitm2, myitm3, myitm4, myitm5) {
    var estats = new Array();
    estats[0] = "";
    if (myitm1 >= 0) {
      if (item_base[inv[myitm1][0]][1] == 8)
        estats[1] = weaveStats(inv[myitm1][0], skills);
      else
        estats[1] = iparse(inv[myitm1][0], inv[myitm1][1], inv[myitm1][2], inv[myitm1][3]);
      estats[0] += " " + (estats[1]);
    }
    if (myitm2 >= 0) {
      if (item_base[inv[myitm2][0]][1] == 8)
        estats[2] = weaveStats(inv[myitm2][0], skills);
      else
        estats[2] = iparse(inv[myitm2][0], inv[myitm2][1], inv[myitm2][2], inv[myitm2][3]);
      estats[0] += " " + (estats[2]);
    }
    if (myitm3 >= 0) {
      if (item_base[inv[myitm3][0]][1] == 8)
        estats[3] = weaveStats(inv[myitm3][0], skills);
      else
        estats[3] = iparse(inv[myitm3][0], inv[myitm3][1], inv[myitm3][2], inv[myitm3][3]);
      estats[0] += " " + (estats[3]);
    }
    if (myitm4 >= 0) {
      if (item_base[inv[myitm4][0]][1] == 8)
        estats[4] = weaveStats(inv[myitm4][0], skills);
      else
        estats[4] = iparse(inv[myitm4][0], inv[myitm4][1], inv[myitm4][2], inv[myitm4][3]);
      estats[0] += " " + (estats[4]);
    }
    if (myitm5 >= 0) {
      if (item_base[inv[myitm5][0]][1] == 8)
        estats[5] = weaveStats(inv[myitm5][0], skills);
      else
        estats[5] = iparse(inv[myitm5][0], inv[myitm5][1], inv[myitm5][2], inv[myitm5][3]);
      estats[0] += " " + (estats[5]);
    }

    return estats;
  }

  function calcstats(myitm1, myitm2, myitm3, myitm4, myitm5, target) {
    var str = "";
    var pts_tot = 0;
    var estats = getstats(myitm1, myitm2, myitm3, myitm4, myitm5);

    var tskills = skills + " " + estats[0];

    if (myitm1 >= 0) {
      pts_tot += (lvl_req(estats[1], getTypeMod(tskills, item_base[inv[myitm1][0]][1])));
    }
    if (myitm2 >= 0) {
      pts_tot += (lvl_req(estats[2], getTypeMod(tskills, item_base[inv[myitm2][0]][1])));
    }
    if (myitm3 >= 0) {
      pts_tot += (lvl_req(estats[3], getTypeMod(tskills, item_base[inv[myitm3][0]][1])));
    }
    if (myitm4 >= 0) {
      pts_tot += (lvl_req(estats[4], getTypeMod(tskills, item_base[inv[myitm4][0]][1])));
    }
    if (myitm5 >= 0) {
      pts_tot += (lvl_req(estats[5], getTypeMod(tskills, item_base[inv[myitm5][0]][1])));
    }

    var oldstats = '';
    if (target == "newstats") {
      var ostats = getstats(ai, bi, ci, di, ei);
      oldstats = cparse(ostats[0]);
    }
    var totstats = itm_info(cparse(estats[0]), oldstats);
    var style = "littletext";
    if (pts_tot > myPts) style = "littletext_r";
    var output = "<font class='" + style + "'>Equip Points: " + pts_tot + "/" + myPts + "<br/><br/>" + totstats;
    document.getElementById(target).innerHTML = output;
  }

  function doEquip() {
    var equip1 = document.getElementById('equip1');
    var equip2 = document.getElementById('equip2');
    var equip3 = document.getElementById('equip3');
    var equip4 = document.getElementById('equip4');
    var equip5 = document.getElementById('equip5');
    var a = equip1.value;
    var b = equip2.value;
    var c = equip3.value;
    var d = equip4.value;
    var e = equip5.value;

    var estats = getstats(a, b, c, d, e);
    var tskills = skills + " " + estats[0];
    var pts_tot = 0;


    if ((a != ai) || (b != bi) || (c != ci) || (d != di) || (e != ei)) {
      if (a >= 0) {
        pts_tot += (lvl_req(estats[1], getTypeMod(tskills, item_base[inv[a][0]][1])));
      }
      if (b >= 0) {
        pts_tot += (lvl_req(estats[2], getTypeMod(tskills, item_base[inv[b][0]][1])));
      }
      if (c >= 0) {
        pts_tot += (lvl_req(estats[3], getTypeMod(tskills, item_base[inv[c][0]][1])));
      }
      if (d >= 0) {
        pts_tot += (lvl_req(estats[4], getTypeMod(tskills, item_base[inv[d][0]][1])));
      }
      if (e >= 0) {
        pts_tot += (lvl_req(estats[5], getTypeMod(tskills, item_base[inv[e][0]][1])));
      }

      if (pts_tot <= myPts) {
        if (a >= 0) {
          document.getElementById('doequipid1').value = inv[a][4];
        }
        if (b >= 0) {
          document.getElementById('doequipid2').value = inv[b][4];
        }
        if (c >= 0) {
          document.getElementById('doequipid3').value = inv[c][4];
        }
        if (d >= 0) {
          document.getElementById('doequipid4').value = inv[d][4];
        }
        if (e >= 0) {
          document.getElementById('doequipid5').value = inv[e][4];
        }
        document.getElementById('doequip').value = 1;
        document.equipForm.submit();
      } else {
        alert("You don't have enough points to equip all those!");
      }
    } else {
      alert("You already have that equipped!");
    }
  }

  function submitConsumeForm(act) {
    document.consumeForm.consume.value = act;
    document.consumeForm.submit();
  }

  function submitWeaveForm(isEq) {
    var neww = document.weaveForm.newweave.value;
    if (neww < 0) alert("You must select a new weave first!");
    document.weaveForm.submit();
  }

  updateEquipLists(1);
  var itma = -1;
  var itmb = -1;
  var itmc = -1;
  var itmd = -1;
  var itme = -1;
  if (ai >= 0) itma = inv[ai];
  if (bi >= 0) itmb = inv[bi];
  if (ci >= 0) itmc = inv[ci];
  if (di >= 0) itmd = inv[di];
  if (ei >= 0) itme = inv[ei];
  calcstats(ai, bi, ci, di, ei, 'currstats');
  calcstats(ai, bi, ci, di, ei, 'newstats');
</script>
<?php
include('footer.htm');
?>