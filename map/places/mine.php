<?php
$title="Mining";
$img="mine_l";
$page="mine.php";
$again="Mine More";
$total_text="Ore removed:";
$dontGo="Finish Mining First";
$res_name="lb";
$res_id=2;
$time_to_action=4;
$num_to_action=3;

$event_list=array(
array("You need to dig deeper into the soft dirt.",0),
array("This pile of ore needs to be moved.",0),
array("Move this pile of gravel.",0),
array("Find a way through this dirt.",0),
array("You need a pile of iron ore.",0),
array("There is too much loose dirt ahead.",0),
array("Clear the excess dirt from this area of the tunnel.",0),
array("You can barely see what you are doing.",1),
array("The tunnel is dark up ahead.",1),
array("You must be able to see.",1),
array("In order to extract this ore properly you must be able to see.",1),
array("The darkness is pressing.",1),
array("Find your way through a dark tunnel.",1),
array("The metal deposits are more easily found if you can see.",1),
array("You are afraid of the dark.",1),
array("Light your path.",1),
array("You hear something in the darkness.",1),
array("You could work better if there was light.",1),
array("It's dark.",1),
array("You see a vein of precious metal embedded in the solid rock.",2),
array("You need to get deeper into the rock.",2),
array("You suspect there is some precious metal behind this rock.",2),
array("This rock must be broken into smaller pieces.",2),
array("You want to crack open this rock.",2),
array("This rock should be reduced to a smaller size.",2),
array("Find a way through a solid rock wall.",2),
array("You hope there is precious metal within this rock.",2),
array("Expand the cave walls 2 paces.",2),
array("Chip away at this wall.",2),
);

$resource_list=array("Copper","Iron","Silver","Gold");

$ability_list=array(0,0,0,0); // LOAD THIS FROM DATABASE

$tool_list=array("Shovel","Lantern","Pickaxe");

include('resources.php');
?>
