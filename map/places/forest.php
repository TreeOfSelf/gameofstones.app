<?php
$title="Hunting";
$img="deer";
$page="forest.php";
$again="Hunt Again";
$total_text="Yield this hunt:";
$dontGo="Finish Hunting First";
$res_name="pelt";
$res_id=1;
$time_to_action=4;
$num_to_action=3;

$event_list=array(
array("You see a deer approaching from the right.",0),
array("You watch as a bear slowly comes toward you.",0),
array("A deer is eating grass in the clearing up ahead.",0),
array("A lone wolf is limping through the woods.",0),
array("You come across a grazing deer, totally unaware of your presence.",0),
array("You see a deer.",0),
array("You see a bear.",0),
array("You see a wolf.",0),
array("A large animal is crashing through the woods towards you.",0),
array("You see a bear a long ways off through the woods.",0),
array("You see rabbit tracks.",1),
array("You catch sight of a rabbit darting into its hole.",1),
array("You stumble across a patch of clover.",1),
array("You see rabbit signs.",1),
array("It looks like a number of small rodents use this path regularly.",1),
array("Squirrels and chipmunks are probably nearby.",1),
array("A small animal will probably come this way soon.",1),
array("A patch of clover is growing in the clearing up ahead.",1),
array("You need more small game.",1),
array("You brought down a deer, and now you need the pelt.",2),
array("You caught a rabbit, and you need the pelt.",2),
array("You killed a bear, collect the fur.",2),
array("Collect the wolf hide.",2),
array("You are caught in your own trap.",2),
array("This piece of leather is too large.",2),
array("This pelt is too large.",2),
array("This leather must be cut into strips.",2),
array("Cut a smaller piece of wood for your new trap.",2),
array("This wooden arrow needs sharpening.",2), 
);

$resource_list=array("Rawhide","Buckskin","Pelt","Leather");

$ability_list=array(0,0,0,0); // LOAD THIS FROM DATABASE

$tool_list=array("Bow","Snare","Knife");

include('resources.php');
?>
