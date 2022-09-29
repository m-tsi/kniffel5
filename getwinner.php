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

    /*get session variables */
    $sessionid=$_SESSION['sessionid'];
    $nickname=$_SESSION['nickname'];


    /*get list of players in session */
    $selectplayersprep="SELECT playerlist FROM gamestate WHERE sessionid=?";
    $selectplayers=$connection->prepare($selectplayersprep);
    $selectplayers->bind_param("s",$sessionid);
    $selectplayers->execute();
    $selectplayers->bind_result($playerlist);
    $selectplayers->fetch();
    $selectplayers->close();

    /*get array from string*/
    $playerlistarray=json_decode($playerlist);


    /*get player with highest point-value */
    $winnerid=NULL;
    $points=0;
    for ($i=0; $i < count($playerlistarray); $i++) { 
        $selectplayersprep="SELECT obersumme+untersumme+bonus FROM gamesheet WHERE playerid=?";
        $selectplayers=$connection->prepare($selectplayersprep);
        $player=$playerlistarray[$i];
        $selectplayers->bind_param("s",$player);
        $selectplayers->execute();
        $selectplayers->bind_result($summe);
        $selectplayers->fetch();
        $selectplayers->close();
        if ($summe>$points){
            $points=$summe;
            $winnerid=$player;
        }
    }

    /*get nickname of winner */
    $selectnicknameprep="SELECT nickname FROM player WHERE playerid=?";
    $selectnickname=$connection->prepare($selectnicknameprep);
    $selectnickname->bind_param("s",$winnerid);
    $selectnickname->execute();
    $selectnickname->bind_result($winner);
    $selectnickname->fetch();
    $selectnickname->close();

    /*return winner */
    echo $winner . " gewinnt mit ". $points ." Punkten!";


?>