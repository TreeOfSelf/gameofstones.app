<?php
$title="Chopping Wood";
$img="cut_l";
$page="timber_yard.php";
$again="Chop More";
$total_text="Wood collected:";
$dontGo="Finish Chopping Wood First";
$res_name="board";
$res_id=0;
$time_to_action=4;
$num_to_action=3;

$event_list=array(
array("You find an already fallen tree.",0),
array("These segments of wood are too long.",0),
array("You need some boards.",0),
array("Flat pieces of wood are easier to transport.",0),
array("You just chopped down a tree.",0),
array("Cut this wood into smaller pieces.",0),
array("You need this wood cut into smaller pieces.",0),
array("You need to move something.",1),
array("You must move these trees.",1),
array("Load the lumber into your cart.",1),
array("This log must be loaded into your cart.",1),
array("Bring your heavy tools up a small embankment.",1),
array("Move that pile of cut logs.",1),
array("You must move this lumber into a different area.",1),
array("Lift this fallen tree.",1),
array("You see a standing tree.",2),
array("You come across a tree in the center of the clearing.",2),
array("You find a tall tree in the center of a clearing.",2),
array("You find yourself in front of a good sized tree.",2),
array("You walk along a pass and find a tall, straight, tree.",2),
array("This tree should be felled.",2),
array("You could harvest a lot of lumber from this tree.",2),
array("Chop down a tree.",2),
);

$resource_list=array("Pine","Oak","Ash","Yew");

$ability_list=array(0,0,0,0); // LOAD THIS FROM DATABASE

$tool_list=array("Saw","Block & Tackle","Axe");

include('resources.php');
?>
