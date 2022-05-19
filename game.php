<!DOCTYPE html>
<?php

/*start session */
session_start();

/* establish connection */
$user='localhost';
$host='root';
$connection=new mysqli($user,$host);
if ($connection-> connect_error){
  echo "Verbindung fehlgeschlagen";
} 

/*check if database exists and create if needed */
$createdatabase="CREATE DATABASE IF NOT EXISTS databasekniffel;";
$connection -> query($createdatabase);

/*set database */
$setdatabase="USE databasekniffel;";
$connection -> query($setdatabase);

if (isset($_SESSION['sessionid'])){
  
  /*get sessionid */
  $sessionid=$_SESSION['sessionid'];
  $nickname=$_SESSION['nickname'];


  /* establish connection */
  $user='localhost';
  $host='root';
  $connection=new mysqli($user,$host);
  if ($connection-> connect_error){
    echo "Verbindung fehlgeschlagen";
  } 

  /*check if database exists and create if needed */
  $createdatabase="CREATE DATABASE IF NOT EXISTS databasekniffel;";
  $connection -> query($createdatabase);

  /*set database */
  $setdatabase=" USE databasekniffel; ";
  $connection -> query($setdatabase);


  /*get all active ids from playerlist */
  $selectplayersprep="SELECT playerlist FROM gamestate WHERE sessionid=?";
  $selectplayers=$connection->prepare($selectplayersprep);
  $selectplayers->bind_param("s",$sessionid);
  $selectplayers->execute();
  $selectplayers->bind_result($playerlist);
  $selectplayers->fetch();
  $selectplayers->close();

  /*get array of playerids*/
  $playerlistarray=json_decode($playerlist);



  /* create table for each player */ 
  for ($i=0;$i<count($playerlistarray);$i++){  

    echo"<div id='table1'> <table><tr> <th> ";


    $selectnicknameprep="SELECT nickname FROM player WHERE playerid=?";
    $selectnickname=$connection->prepare($selectnicknameprep);
    $playerid0= $playerlistarray[$i];
    $selectnickname->bind_param("s",$playerid0);
    $selectnickname->execute();
    $selectnickname->bind_result($printplayer);
    $selectnickname->fetch();
    $selectnickname->close();
    echo $printplayer;

    /*select last inserted runde with session id*/
    $selectrundeprep="SELECT rundeid FROM runde WHERE sessionid=?";
    $selectrunde=$connection->prepare($selectrundeprep);
    $selectrunde->bind_param("s",$sessionid);
    $selectrunde->execute();
    $selectrunde->bind_result($rundeid);
    $selectrunde->fetch();
    $selectrunde->close();

    $selectgamesheetprep="SELECT einser ,zweier, dreier , vierer ,fuenfer ,sechser, dreierpasch ,viererpasch,kleinestrasse ,grossestrasse,fullhouse,chance ,kniffel ,bonus ,untersumme,obersumme ,summe
    FROM gamesheet WHERE rundeid=? AND playerid=?";
    $selectgamesheet=$connection->prepare($selectgamesheetprep);
    $selectgamesheet->bind_param("ss",$rundeid,$playerid0);
    $selectgamesheet->execute();
    $selectgamesheet->bind_result($einser ,$zweier, $dreier , $vierer ,$fuenfer ,$sechser, $dreierpasch ,$viererpasch,$kleinestrasse ,$grossestrasse,$fullhouse,$chance ,$kniffel ,$bonus ,$untersumme,$obersumme ,$summe);
    $selectgamesheet->fetch();
    $selectgamesheet->close();

    echo"
    <form action='selectfield.php?fieldname=grossestrasse' name='roll' method='post'> 
    <input type='submit' name='roll' value='MEOW'>
    </form>
      </th>
      <th id=points'> Punkte </th>
      </tr>
      <tr>
        <th> 1er </th>
        <th id='einser' onclick='selectfieldnum(".'"einser"'.")'>". $einser ." </th>

      </tr>
      <tr>
        <th> 2er </th>
        <th onclick='selectfieldnum(".'"zweier"'.")'> ". $zweier ." </th>
      </tr>
      <tr>
        <th> 3er </th>
        <th onclick='selectfieldnum(".'"dreier"'.")'> ". $dreier ." </th>
      </tr>
      <tr>
        <th> 4er </th>
        <th onclick='selectfieldnum(".'"vierer"'.")'> ". $vierer ." </th>
      </tr>
      <tr>
        <th> 5er </th>
        <th onclick='selectfieldnum(".'"fuenfer"'.")'>". $fuenfer ." </th>
      </tr>
      <tr>
        <th> 6er </th>
        <th onclick='selectfieldnum(".'"sechser"'.")'>". $sechser ." </th>
      </tr>
      <tr>
        <th> Summe </th>
        <th > ". $obersumme ."</th>
      </tr>
      <tr>
        <th> Bonus </th>
        <th >" . $bonus ." </th>
      </tr>
      <tr>
        <th> Summe mit Bonus </th>
        <th >  ".$obersumme+$bonus ." </th>
      </tr>
      <tr>
        <th> Dreierpasch </th>
        <th  onclick='selectfieldnum(".'"dreierpasch"'.")'>". $dreierpasch ." </th>
      </tr>
      <tr>
        <th> Viererpasch </th>
        <th onclick='selectfieldnum(".'"viererpasch"'.")'>". $viererpasch ."</th>
      </tr>
      <tr>
        <th> Kleine Straße </th>
        <th onclick='selectfieldnum(".'"kleinestrasse"'.")'>". $kleinestrasse ." </th>
      </tr>
      <tr>
        <th> Große Straße </th>
        <th onclick='selectfieldnum(".'"grossestrasse"'.")'>". $grossestrasse ." </th>
      </tr>
      <tr>
        <th> FullHouse </th>
        <th onclick='selectfieldnum(".'"fullhouse"'.")'>". $fullhouse ." </th>
      </tr>
      <tr>
        <th> Kniffel </th>
        <th onclick='selectfieldnum(".'"kniffel"'.")'>". $kniffel ." </th>
      </tr>
      <tr>
        <th> Chance </th>
        <th onclick='selectfieldnum(".'"chance"'.")'>" . $chance . "</th>
      </tr>
      <tr>
        <th> Summe Unten </th>
        <th > " . $untersumme ." </th>
      </tr>
      <tr>
        <th> Gesamtsumme </th>
        <th > ". $obersumme+$untersumme+$bonus ." </th>
      </tr>
      </table> 
      </div>";
  }

  echo"
    <div id='board'>
      <center> 
      <!--<form action='rolldice.php' name='roll' method='post'> -->
        <input type='submit' name='roll' value='Rollen' onclick='rolldice();'>
      <!-- </form> -->
      <br>
      </center>"; /*FIX */

      /*select last inserted runde  with session id*/
      $selectrundeprep="SELECT max(rundeid) FROM runde WHERE sessionid=?";
      $selectrunde=$connection->prepare($selectrundeprep);
      $selectrunde->bind_param("s",$sessionid);
      $selectrunde->execute();
      $selectrunde->bind_result($rundeid);
      $selectrunde->fetch();
      $selectrunde->close();


      /*selct rollid of current wurf */
      $selectrollidprep="SELECT rollid FROM wuerfe WHERE rundeid=?";
      $selectrollid=$connection->prepare($selectrollidprep);
      $selectrollid->bind_param("s",$rundeid);
      $selectrollid->execute();
      $selectrollid->bind_result($rollid);
      $selectrollid->fetch();
      $selectrollid->close();


      
      /*create empty rows for inserting dice values*/
      $row1=[0,0,0,0,0];
      $row2=[0,0,0,0,0];


      /*get dice values if wurf exists */
      if ($rollid!=null){
      
        /*get dice-rows of wurf */
        $wurfinfoprep="SELECT randomvalues,selection FROM wuerfe WHERE rundeid=? AND rollid=(SELECT max(rollid) FROM wuerfe)";
        $wurfinfo=$connection->prepare($wurfinfoprep);
        $wurfinfo->bind_param("s",$rundeid);
        $wurfinfo->execute();
        $wurfinfo->bind_result($randomvaluesstring,$selectionstring);
        $wurfinfo->fetch();
        $wurfinfo->close();


        /*get arrays of dice-rows */
        $randomvalues=json_decode($randomvaluesstring);
        $selection=json_decode($selectionstring);


        /*combine empty arrays and dice-values */
        for ($i=0; $i < count($randomvalues); $i++) { 
          $row1[$i]+=$randomvalues[$i];
        }
  
        for ($i=0; $i < count($selection); $i++) { 
          $row2[$i]+=$selection[$i];
        }
      }


    /*show dice */
    echo"<div class='dice_first_row'>";
    for ($i=0; $i < count($row1); $i++) {  
      //echo"<a onclick='selectdice($row1[$i])' href='clickdice.php?dice=".$row1[$i]."&position=".$i."' > <img class='single_dice' src='img/dice".$row1[$i] . ".png' width='50'> </a>";
      echo"<img class='single_dice' src='img/dice".$row1[$i] . ".png' width='50' onclick='selectdice($row1[$i], $i)'>";
    }
    echo" </div> <br/>
      <div class='dice_second_row'>";
    for ($i=0; $i < count($row2); $i++) {  
      //echo"<img class='single_dice' src='img/dice".$row2[$i] . ".png' value='".$row2[$i]."'width='50'>";
      echo"<img class='single_dice' src='img/dice".$row2[$i] . ".png' width='50'>";
    }
  echo"</div><center> ";


  /*show gameinfo*/
  echo "Spiel-ID: ".$sessionid . "<br/>";
  echo "Spielername: ".$nickname."<br/>";
        
  /* find current player and return their name and how many throws they have left */
  for ($i=0;$i<count($playerlistarray);$i++){  
    $selectrundeprep="SELECT MAX(rundeid) AS rundeid FROM runde WHERE playerid=?";
    $selectrunde=$connection->prepare($selectrundeprep);
    $playerid=$playerlistarray[$i];
    $selectrunde->bind_param("s",$playerid);

    if ($selectrunde->execute()===TRUE){
      $selectrunde->bind_result($player);
      $selectrunde->fetch();
      $selectrunde->close();

      /*return current player */
      $selectnicknameprep="SELECT nickname AS nickname FROM player WHERE playerid=?";
      $selectnickname=$connection->prepare($selectnicknameprep);
      $selectnickname->bind_param("s",$playerid);
      $selectnickname->execute();
      $selectnickname->bind_result($playername);
      $selectnickname->fetch();
      $selectnickname->close();
      echo "Am Zug: ".$playername. "<br/>";

      /*return remaining turns */
      $countwuerfeprep="SELECT COUNT(rollid) FROM wuerfe WHERE rundeid=?";
      $countwuerfe=$connection->prepare($countwuerfeprep);
      $countwuerfe->bind_param("s",$rundeid);
      $countwuerfe->execute();
      $countwuerfe->bind_result($printrollnumber);
      $countwuerfe->fetch();
      $countwuerfe->close();
      echo "Verbleibende Züge: ".(3-$printrollnumber);
      break;
    }           
  }
  echo" </div>  <br></center></div></div>";
  
}
?>