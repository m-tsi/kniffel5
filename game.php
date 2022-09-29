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

    echo"<div id='table".$i."'> <table><tr> <th> ";

    /*get nickname of player at position i*/
    $selectnicknameprep="SELECT nickname FROM player WHERE playerid=?";
    $selectnickname=$connection->prepare($selectnicknameprep);
    $playeridcurrent= $playerlistarray[$i];
    $selectnickname->bind_param("s",$playeridcurrent);
    $selectnickname->execute();
    $selectnickname->bind_result($nicknamecurrent);
    $selectnickname->fetch();
    $selectnickname->close();

    /*print nickname */
    echo $nicknamecurrent;

    

    /*select last inserted round with sessionid and playerid*/
    $selectrundeidprep="SELECT rundeid FROM runde WHERE sessionid=? AND playerid=?";
    $selectrundeid=$connection->prepare($selectrundeidprep);
    $selectrundeid->bind_param("ss",$sessionid,$playeridcurrent);
    $selectrundeid->execute();
    $selectrundeid->bind_result($rundeid);
    $selectrundeid->fetch();
    $selectrundeid->close();

    /*get gamesheet for current player */
    $selectgamesheetprep="SELECT einser ,zweier, dreier , vierer ,fuenfer ,sechser, dreierpasch ,viererpasch,kleinestrasse ,grossestrasse,fullhouse,chance ,kniffel ,bonus ,untersumme,obersumme ,summe
    FROM gamesheet WHERE playerid=?";
    $selectgamesheet=$connection->prepare($selectgamesheetprep);
    $selectgamesheet->bind_param("s",$playeridcurrent);
    $selectgamesheet->execute();
    $selectgamesheet->bind_result($einser ,$zweier, $dreier , $vierer ,$fuenfer ,$sechser, $dreierpasch ,$viererpasch,$kleinestrasse ,$grossestrasse,$fullhouse,$chance ,$kniffel ,$bonus ,$untersumme,$obersumme ,$summe);
    $selectgamesheet->fetch();
    $selectgamesheet->close();


    /*show gamesheet in table */
    echo"
      </th>
      <th id='points'> Punkte </th>
      </tr>
      <tr>
        <th> 1er </th>
        <th id='einser' onclick='selectfieldnum(".'"einser"'.",".'"'.$nicknamecurrent.'"'.")'>". $einser ."</th>

      </tr>
      <tr>
        <th> 2er </th>
        <th id='zweier' onclick='selectfieldnum(".'"zweier"'.",".'"'.$nicknamecurrent.'"'.")'>". $zweier ."</th>
      </tr>
      <tr>
        <th> 3er </th>
        <th id='dreier' onclick='selectfieldnum(".'"dreier"'.",".'"'.$nicknamecurrent.'"'.")'>". $dreier ."</th>
      </tr>
      <tr>
        <th> 4er </th>
        <th id='vierer' onclick='selectfieldnum(".'"vierer"'.",".'"'.$nicknamecurrent.'"'.")'>". $vierer ."</th>
      </tr>
      <tr>
        <th> 5er </th>
        <th id='fuenfer' onclick='selectfieldnum(".'"fuenfer"'.",".'"'.$nicknamecurrent.'"'.")'>". $fuenfer ."</th>
      </tr>
      <tr>
        <th> 6er </th>
        <th id='sechser' onclick='selectfieldnum(".'"sechser"'.",".'"'.$nicknamecurrent.'"'.")'>". $sechser ."</th>
      </tr>
      <tr>
        <th> Summe </th>
        <th id='obersumme' >". $obersumme ."</th>
      </tr>
      <tr>
        <th> Bonus </th>
        <th  id='bonus'>" . $bonus ."</th>
      </tr>
      <tr>
        <th> Summe mit Bonus </th>
        <th id='summemitbonus'>".$obersumme+$bonus ."</th>
      </tr>
      <tr>
        <th> Dreierpasch </th>
        <th  id='dreierpasch' onclick='selectfieldnum(".'"dreierpasch"'.",".'"'.$nicknamecurrent.'"'.")'>". $dreierpasch ."</th>
      </tr>
      <tr>
        <th> Viererpasch </th>
        <th id='viererpasch' onclick='selectfieldnum(".'"viererpasch"'.",".'"'.$nicknamecurrent.'"'.")'>". $viererpasch ."</th>
      </tr>
      <tr>
        <th> Kleine Straße </th>
        <th id='kleinestrasse' onclick='selectfieldnum(".'"kleinestrasse"'.",".'"'.$nicknamecurrent.'"'.")'>". $kleinestrasse ."</th>
      </tr>
      <tr>
        <th> Große Straße </th>
        <th id='grossestrasse' onclick='selectfieldnum(".'"grossestrasse"'.",".'"'.$nicknamecurrent.'"'.")'>". $grossestrasse ."</th>
      </tr>
      <tr>
        <th> FullHouse </th>
        <th id='fullhouse' onclick='selectfieldnum(".'"fullhouse"'.",".'"'.$nicknamecurrent.'"'.")'>". $fullhouse ."</th>
      </tr>
      <tr>
        <th> Kniffel </th>
        <th id='kniffel' onclick='selectfieldnum(".'"kniffel"'.",".'"'.$nicknamecurrent.'"'.")'>". $kniffel ."</th>
      </tr>
      <tr>
        <th> Chance </th>
        <th id='chance' onclick='selectfieldnum(".'"chance"'.",".'"'.$nicknamecurrent.'"'.")'>" . $chance . "</th>
      </tr>
      <tr>
        <th> Summe Unten </th>
        <th id='untersumme'>" . $untersumme ."</th>
      </tr>
      <tr>
        <th> Gesamtsumme </th>
        <th id='gesamtsumme'>". $obersumme+$untersumme+$bonus ."</th>
      </tr>
      </table> 
      </div>";
    }

    echo"
      <div id='board'>
      <center> 
      <input type='submit' name='roll' value='Rollen' onclick='rolldice();'>
      
      <br>
      </center>";

    /*select last inserted runde  with session id*/
    $selectrundeidprep="SELECT max(rundeid) FROM runde WHERE sessionid=?";
    $selectrundeid=$connection->prepare($selectrundeidprep);
    $selectrundeid->bind_param("s",$sessionid);
    $selectrundeid->execute();
    $selectrundeid->bind_result($rundeid);
    $selectrundeid->fetch();
    $selectrundeid->close();

    
    /*selct rollid of current wurf */
    $selectrollidprep="SELECT MAX(rollid) FROM wuerfe WHERE rundeid=?";
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

      /*get dice-rows of wurf*/
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
      if ($randomvalues!=null){
        for ($i=0; $i < count($randomvalues); $i++) { 
          $row1[$i]+=$randomvalues[$i];
        }
      }
      if ($selection!=null){
        for ($i=0; $i < count($selection); $i++) { 
          $row2[$i]+=$selection[$i];
        }
      }
    }


  /*show dice */
  echo"<div class='dice_first_row'>";
  for ($i=0; $i < count($row1); $i++) {  
    echo"<img class='open_dice' id='dice". $i  ."'     src='img/dice".$row1[$i] . ".png' width='50' onclick='selectdice($row1[$i], $i)'>";
  }
  echo" </div> <br/>
    <div class='dice_second_row'>";
  for ($i=0; $i < count($row2); $i++) {  
    echo"<img class='set_dice' id='setdice". $i  ."' src='img/dice".$row2[$i] . ".png' width='50'>";
  }
  echo"</div><center> ";

  /*show gameinfo*/
  echo "Spiel-ID: ".$sessionid . "<br/>";
  echo "Spielername: <div id='myplayer'>".$nickname."</div> <br/>";
        
  /* find current player and return their name and how many throws they have left */
  $selectrundeprep="SELECT playerid from runde WHERE rundeid=(SELECT MAX(rundeid) from runde WHERE sessionid=?)"; 
  $selectrunde=$connection->prepare($selectrundeprep);
  $selectrunde->bind_param("s",$sessionid);
  $selectrunde->execute();
  $selectrunde->bind_result($playerid);
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
  echo "Am Zug: <div id='currentplayer'>".$playername."</div> <br/>";

  /*return remaining turns */
  $countwuerfeprep="SELECT COUNT(rollid) FROM wuerfe WHERE rundeid=?";
  $countwuerfe=$connection->prepare($countwuerfeprep);
  $countwuerfe->bind_param("s",$rundeid);
  $countwuerfe->execute();
  $countwuerfe->bind_result($printrollnumber);
  $countwuerfe->fetch();
  $countwuerfe->close();
  echo "Verbleibende Züge:  <div id='turns'>".(3-$printrollnumber)."</div> <br/> ";

  /*create array of current randomvalues*/
  $getrandomprep="SELECT randomvalues as randomvalues from wuerfe where rundeid=?";
  $getrandom=$connection->prepare($getrandomprep);
  $getrandom->bind_param("s",$rundeid);
  $getrandom->execute();
  $getrandomtemp=$getrandom->get_result();
  $fillarrayrandom=array();
  while ($row=mysqli_fetch_assoc($getrandomtemp)){
    array_push($fillarrayrandom,$row['randomvalues']);
  }

  /*create array of current selection*/
  $getselectionprep="SELECT selection as selection from wuerfe where rundeid=?";
  $getselection=$connection->prepare($getselectionprep);
  $getselection->bind_param("s",$rundeid);
  $getselection->execute();
  $getselectiontemp=$getselection->get_result();
  $fillarrayselection=array();
  while ($row=mysqli_fetch_assoc($getselectiontemp)){
    array_push($fillarrayselection,$row['selection']);
  }

  /* print results */
  for ($i=0; $i < count($fillarrayrandom) ; $i++) { 
    echo "<div id='getrandom".$i."'>"  . $fillarrayrandom[$i]   ."</div> <div id='getselection".$i."'>"  . $fillarrayselection[$i]   ."</div> <br/>";
  }
   
  echo" </div>  <br></center></div></div>";
  
}
?>