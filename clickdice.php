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

  /*get session variables*/
  $sessionid=$_SESSION['sessionid'];
  $nickname=$_SESSION['nickname'];

  /*get playerid of current turn*/
  $playerturnprep="SELECT playerid from runde WHERE rundeid=(SELECT MAX(rundeid) from runde WHERE sessionid=?)"; 
  $playerturn=$connection->prepare($playerturnprep);
  $playerturn->bind_param("s",$sessionid);
  $playerturn->execute();
  $playerturn->bind_result($turnid);
  $playerturn->fetch();
  $playerturn->close();

  /*return name of player of current turn */
  $turnnicknameprep="SELECT nickname AS nickname FROM player WHERE playerid=?";
  $turnnickname=$connection->prepare($turnnicknameprep);
  $turnnickname->bind_param("s",$turnid);
  $turnnickname->execute();
  $turnnickname->bind_result($turnname);
  $turnnickname->fetch();
  $turnnickname->close();


  /*check if clicked dice is not empty and if correct player clicked button*/
  if (isset($_GET['dice']) && isset($_GET['position']) && $_GET['dice']!=0 && $turnname==$nickname){
    
    /*get variables */
    $dice=$_GET['dice'];
    $position=$_GET['position'];

    /*select last inserted runde rundelast with session id*/
    $selectrundeprep="SELECT max(rundeid) FROM runde WHERE sessionid=?";
    $selectrunde=$connection->prepare($selectrundeprep);
    $selectrunde->bind_param("s",$sessionid);
    $selectrunde->execute();
    $selectrunde->bind_result($rundeid);
    $selectrunde->fetch();
    $selectrunde->close();

    /*select max roll in wuerfe */
    $selectrollprep="SELECT max(rollid) FROM wuerfe WHERE rundeid=?";
    $selectroll=$connection->prepare($selectrollprep);
    $selectroll->bind_param("s",$rundeid);
    $selectroll->execute();
    $selectroll->bind_result($rollid);
    $selectroll->fetch();
    $selectroll->close();

    /*get dice-values from current roll */
    $wurfinfoprep="SELECT randomvalues, selection FROM wuerfe WHERE rundeid=? AND rollid=?";
    $wurfinfo=$connection->prepare($wurfinfoprep);
    $wurfinfo->bind_param("ss",$rundeid,$rollid);
    $wurfinfo->execute();
    $wurfinfo->bind_result($randomvaluesstring,$selectionstring);
    $wurfinfo->fetch();
    $wurfinfo->close();


    /*get dice-results as arrays */
    $randomvalues=json_decode($randomvaluesstring);
    $selection=json_decode($selectionstring);

    /*update arrays */
    array_splice($randomvalues,$position,1);
    array_push($selection,$dice);

    /* turn arrays back to strings */
    $randomvaluesnew=json_encode($randomvalues);
    $selectionnew=json_encode($selection);

    /*insert new value into wuerfe */
    $updateplayersprep="UPDATE wuerfe SET randomvalues=?,selection=? WHERE rundeid=? AND rollid=?";
    $updateplayers=$connection->prepare($updateplayersprep);
    $updateplayers->bind_param("ssss",$randomvaluesnew,$selectionnew,$rundeid,$rollid);
    $updateplayers->execute();
    $updateplayers->close();

  }

?>