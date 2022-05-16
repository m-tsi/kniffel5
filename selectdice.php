<!DOCTYPE html>
<?php



/* establish connection */
require('connectdatabase.php');

echo "<html> <body>";


/*get session variables */ 
$rundeid=$_POST['rundeid'];
$playerid=$_POST['playerid'];
$position=$_POST['diceposition'];
$value=$_POST['value'];
/*get selection */


$getlist= "SELECT playerlist FROM gamestate WHERE sessionid=?";
$getlist=$connection->prepare($getlist);
$getlist->bind_param("s",$session);
$list=$getlist->execute();


$array=json_decode($list);
array_push($array,$username);

/*get all wuerfe for current round */
$makegame="INSERT INTO wuerfe (rundeid, randomvalues,selection) VALUES (?,?,?)";
$inserttable=$connection->prepare($makegame);
$playerlist=json_encode($randarray);
$selection=[];
$inserttable->bind_param("ssss",$rundeid,$randarray,$selection);
$inserttable->execute();




echo "</body> </html>"

?>