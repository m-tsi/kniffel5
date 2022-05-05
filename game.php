<?php


if (isset($_GET['gameid'])){
/*get sessionid */
$sessionid=$_GET['gameid'];


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


echo"
<html lang='de'>
  <head>
    <meta charset='utf-8'>
    <link rel='stylesheet'   href='stylesheetpc.css'>
    <link rel='icon' href='img/websiteicon.png'>
    <title> Kniffel - multiplayer </title>
  </head>
  <body onload='initialize_game()'>
";



/* create table for each player */ 
for ($i=0;$i<count($playerlistarray);$i++){  
echo"
    <div id='table1'>
      <table>
        <tr>
          <th> ";
   
    $selectnicknameprep="SELECT nickname FROM player WHERE playerid=?";
    $selectnickname=$connection->prepare($selectnicknameprep);
    $playername= $playerlistarray[$i];
    $selectnickname->bind_param("s",$playername);
    $selectnickname->execute();
    $selectnickname->bind_result($printplayer);
    $selectnickname->fetch();
    $selectnickname->close();
    echo $printplayer;

echo"</th>
          <th id=points'> Punkte </th>
        </tr>
        <tr>
          <th> 1er </th>
          <th id='1' onclick='selectfieldnum(1)'>  </th>
        </tr>
        <tr>
          <th> 2er </th>
          <th id='2' onclick='selectfieldnum(2)'>  </th>
        </tr>
        <tr>
          <th> 3er </th>
          <th id='3' onclick='selectfieldnum(3)'>  </th>
        </tr>
        <tr>
          <th> 4er </th>
          <th id='4' onclick='selectfieldnum(4)'> </th>
        </tr>
        <tr>
          <th> 5er </th>
          <th id='5' onclick='selectfieldnum(5)'> </th>
        </tr>
        <tr>
          <th> 6er </th>
          <th id='6' onclick='selectfieldnum(6)'> </th>
        </tr>
        <tr>
          <th> Summe </th>
          <th id='sumtop_nobonus'> </th>
        </tr>
        <tr>
          <th> Bonus </th>
          <th id='bonus'> </th>
        </tr>
        <tr>
          <th> Summe mit Bonus </th>
          <th id='sumtop_bonus'> </th>
        </tr>
        <tr>
          <th> Dreierpasch </th>
          <th id='Dreierpasch' onclick='selectfield('Dreierpasch')'> </th>
        </tr>
        <tr>
          <th> Viererpasch </th>
          <th id='Viererpasch' onclick='selectfield('Viererpasch')'> </th>
        </tr>
        <tr>
          <th> Kleine Straße </th>
          <th id='KleineStraße' onclick='selectfield('KleineStraße')'> </th>
        </tr>
        <tr>
          <th> Große Straße </th>
          <th id='GroßeStraße' onclick='selectfield('GroßeStraße')'> </th>
        </tr>
        <tr>
          <th> FullHouse </th>
          <th id='FullHouse' onclick='selectfield('FullHouse')'> </th>
        </tr>
        <tr>
          <th> Kniffel </th>
          <th id='Kniffel' onclick='selectfield('Kniffel')'> </th>
        </tr>
        <tr>
          <th> Chance </th>
          <th id='Chance' onclick='selectfield('Chance')'> </th>
        </tr>
        <tr>
          <th> Summe Unten </th>
          <th id='sum_bottom'> </th>
        </tr>
        <tr>
          <th> Gesamtsumme </th>
          <th id='sum_total'> </th>
        </tr>

      </table> </div>";


}

echo"
    <div id='board'>
      <center> <button type='button' name='button' id='roll_click' onclick='roll()'> ROLLEN </button> <br>
      </center>
      <div class='dice_first_row'>
        <img class='single_dice' alt='' name='0' id='dice1-0' width='50'>
        <img class='single_dice' alt='' name='1' id='dice1-1' width='50'>
        <img class='single_dice' alt='' name='2' id='dice1-2' width='50'>
        <img class='single_dice' alt='' name='3' id='dice1-3' width='50'>
        <img class='single_dice' alt='' name='4' id='dice1-4' width='50'>
      </div> <br>
      <div class='dice_second_row'>
        <img class='single_dice' alt='' id='dice2-0' width='50'>
        <img class='single_dice' alt='' id='dice2-1' width='50'>
        <img class='single_dice' alt='' id='dice2-2' width='50'>
        <img class='single_dice' alt='' id='dice2-3' width='50'>
        <img class='single_dice' alt='' id='dice2-4' width='50'>
      </div>

      <center> ";
        
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
                echo "Am Zug:".$playername. "<br/>";

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
    


        /* find number of wuerfe for spieler who is am zug*/


        

        echo" </div>  <br>
      </center>
    </div>

    
    </div>


  </body>
  <footer>
    <div id='innerfooter'>
      <! ... >
      Kontakt |
      Regeln
    </div>
  </footer>


<html>

";


}












?>