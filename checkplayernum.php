<?php

  /*start session */
  session_start();

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
  $setdatabase="USE databasekniffel;";
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

  /*return number of players */
  echo count($playerlistarray);
  

?>