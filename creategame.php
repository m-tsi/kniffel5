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
  $setdatabase=" USE databasekniffel; ";
  $connection -> query($setdatabase);

  /* create tables */
  $createtables1=" CREATE TABLE IF NOT EXISTS gamestate(
    sessionid INT NOT NULL AUTO_INCREMENT,
    playerlist VARCHAR(300),
    PRIMARY KEY (sessionid)
  );";


  $createtables2="CREATE TABLE IF NOT EXISTS player(
    nickname VARCHAR(128),
    playerid VARCHAR(300),
    sessionid INT
  );";
    
  $createtables3="CREATE TABLE IF NOT EXISTS gamesheet(
    playerid VARCHAR(300),
    einser INT,
    zweier INT,
    dreier INT,
    vierer INT,
    fuenfer INT,
    sechser INT,
    dreierpasch INT,
    viererpasch INT,
    kleinestrasse INT,
    grossestrasse INT,
    fullhouse INT,
    chance INT,
    kniffel INT,
    bonus INT,
    untersumme INT,
    obersumme INT,
    summe INT
  );";


  $createtables4="CREATE TABLE IF NOT EXISTS runde(
    sessionid INT,
    rundeid INT NOT NULL AUTO_INCREMENT,
    playerid VARCHAR(300),
    PRIMARY KEY (rundeid)
  );";

  $createtables5="CREATE TABLE IF NOT EXISTS wuerfe(
    rundeid INT,
    rollid INT NOT NULL AUTO_INCREMENT,
    randomvalues VARCHAR(128),
    selection VARCHAR(128),
    PRIMARY KEY (rollid)
  );";

  /* create tables */
  $connection -> query($createtables1);
  $connection -> query($createtables2);
  $connection -> query($createtables3);
  $connection -> query($createtables4);
  $connection -> query($createtables5);

  /*change startpoints of autoincremented ids */
  $changestart1="ALTER TABLE gamestate AUTO_INCREMENT=1000; ";
  $connection -> query($changestart1);
  $changestart2="ALTER TABLE wuerfe AUTO_INCREMENT=100; ";
  $connection -> query($changestart2);
  $changestart3="ALTER TABLE runde AUTO_INCREMENT=10; ";
  $connection -> query($changestart3);

  /* if nickname is set correctly */
  if (isset($_POST['nickname']) && $_POST['nickname']!=''){

    /*read post variables */
    $nickname=$_POST['nickname'];
    $_SESSION['nickname']=$_POST['nickname'];

    /* create id for user */
    $salt=random_bytes(8);
    $time=new DateTime();
    $timestamp= $time->getTimestamp();
    $usernamehash=md5($salt.$nickname.$timestamp);


    /*make gamestate*/
    $makegamestateprep="INSERT INTO gamestate (playerlist) VALUES (?)";
    $makegamestate=$connection->prepare($makegamestateprep);
    $playerlist=json_encode([$usernamehash]);
    $makegamestate->bind_param("s",$playerlist);
    $makegamestate->execute();
    $makegamestate->close();

    /*get sessionid*/
    $getsessionidprep= "SELECT MAX(sessionid) AS sessionid FROM gamestate";
    $getsessionid=$connection -> query($getsessionidprep);
    $sessionid=mysqli_fetch_array($getsessionid);
    $sessionid=$sessionid['sessionid'];
    $_SESSION['sessionid']=$sessionid;

  
    /*make first user */
    $makeplayerprep="INSERT INTO player (nickname,playerid,sessionid) VALUES (?,?,?)";
    $makeplayer=$connection->prepare($makeplayerprep);
    $makeplayer->bind_param("sss",$nickname,$usernamehash,$sessionid);
    $makeplayer->execute();
    $makeplayer->close();

    /*create new runde for first player*/
    $makerundeprep="INSERT INTO runde (sessionid,playerid) VALUES (?,?)";
    $makerunde=$connection->prepare($makerundeprep);
    $makerunde->bind_param("ss",$sessionid, $usernamehash);
    $makerunde->execute();
    $makerunde->close();

    /* create table for new player */
    $makegamesheetprep="INSERT INTO gamesheet (playerid,obersumme,untersumme,bonus) VALUES (?,0,0,0)";
    $makegamesheet=$connection->prepare($makegamesheetprep);
    $makegamesheet->bind_param("s",$usernamehash);
    $makegamesheet->execute();
    $makegamesheet->close();

    echo "Spielcode:" . $sessionid . "<br/>";
    echo "Bitte an Spieler weitergeben!";


    header('location: http://localhost/game5.html');
  }

  else{ 
    header('location: http://localhost/creategame.html');
  }
?>