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

    /*get session variables */
    $sessionid=$_SESSION['sessionid'];
    $nickname=$_SESSION['nickname'];

    /*GET variables */
    $fieldname=$_GET['fieldname'];
    $playername=$_GET['playername'];

    /* get playerid of current turn */
    $selectturnidprep="SELECT playerid from runde WHERE rundeid=(SELECT MAX(rundeid) from runde WHERE sessionid=?)"; 
    $selectturnid=$connection->prepare($selectturnidprep);
    $selectturnid->bind_param("s",$sessionid);
    $selectturnid->execute();
    $selectturnid->bind_result($turnid);
    $selectturnid->fetch();
    $selectturnid->close();

    /*return nickname of player of current turn */
    $selectturnnameprep="SELECT nickname AS nickname FROM player WHERE playerid=?";
    $selectturnname=$connection->prepare($selectturnnameprep);
    $selectturnname->bind_param("s",$turnid);
    $selectturnname->execute();
    $selectturnname->bind_result($turnname);
    $selectturnname->fetch();
    $selectturnname->close();

    /* get own playerid */
    $selectplayeridprep="SELECT playerid FROM player WHERE sessionid=? AND nickname=?";
    $selectplayerid=$connection->prepare($selectplayeridprep);
    $selectplayerid->bind_param("ss",$sessionid,$nickname);
    $selectplayerid->execute();
    $selectplayerid->bind_result($playerid);
    $selectplayerid->fetch();
    $selectplayerid->close();

    /*get current value of clicked field */
    $selectfieldprep="SELECT ".$fieldname." FROM gamesheet WHERE playerid=?";
    $selectfield=$connection->prepare($selectfieldprep);
    $selectfield->bind_param("s",$playerid);
    $selectfield->execute();
    $selectfield->bind_result($fieldnameold);
    $selectfield->fetch();
    $selectfield->close();

    /*if field is empty, current field is clicked and player has the turn*/
    if ($fieldnameold==NULL && $nickname==$playername && $nickname==$turnname){

        /*select last inserted rundeid of current session with own playerid */
        $selectrundeidprep="SELECT max(rundeid) FROM runde WHERE sessionid=? AND playerid=?";
        $selectrundeid=$connection->prepare($selectrundeidprep);
        $selectrundeid->bind_param("ss",$sessionid,$playerid);
        $selectrundeid->execute();
        $selectrundeid->bind_result($rundeid);
        $selectrundeid->fetch();
        $selectrundeid->close();


        /*selct rollid and dice-rows of current roll*/
        $selectrollprep="SELECT rollid,selection,randomvalues FROM wuerfe WHERE rollid=(SELECT max(rollid) FROM wuerfe WHERE rundeid=?)";
        $selectroll=$connection->prepare($selectrollprep);
        $selectroll->bind_param("s",$rundeid);
        $selectroll->execute();
        $selectroll->bind_result($rollid,$selection,$randomvalues);
        $selectroll->fetch();
        $selectroll->close();


        /*select previous points from table */
        $getpointsprep="SELECT obersumme,untersumme,bonus FROM gamesheet WHERE playerid=?";
        $getpoints=$connection->prepare($getpointsprep);
        $getpoints->bind_param("s",$playerid);
        $getpoints->execute();
        $getpoints->bind_result($obersumme,$untersumme,$bonus);
        $getpoints->fetch();
        $getpoints->close();
        
        /*get array of dice-string */
        $dice=json_decode($selection);

        /*count points*/
        $points=0;

        /*calculate points*/
        if ($fieldname=='einser'){
            $points=count(array_keys($dice,1))*1;
            $obersumme+=$points;
        }

        else if ($fieldname=='zweier'){
            $points=count(array_keys($dice,2))*2;
            $obersumme+=$points;
        }
        
        else if ($fieldname=='dreier'){
            $points=count(array_keys($dice,3))*3;
            $obersumme+=$points;
        }

        else if ($fieldname=='vierer'){
            $points=count(array_keys($dice,4))*4;
            $obersumme+=$points;
        }

        else if ($fieldname=='fuenfer'){
            $points=count(array_keys($dice,5))*5;
            $obersumme+=$points;
        }

        else if ($fieldname=='sechser'){
            $points=count(array_keys($dice,6))*6;
            $obersumme+=$points;
        }

        else if ($fieldname=='kleinestrasse'&&(array_intersect(array(' 1',' 2',' 3',' 4'),$dice)==array(' 1',' 2',' 3',' 4')||array_intersect(array(' 2',' 3',' 4',' 5'),$dice)==array(' 2',' 3',' 4',' 5')||array_intersect(array(' 3',' 4',' 5',' 6'),$dice)==array(' 3',' 4',' 5',' 6'))){
            $points=30;
            $untersumme+=$points;
        }

        else if ($fieldname=='grossestrasse'&& (array_intersect(array(' 1',' 2',' 3',' 4',' 5'),$dice)==array(' 1',' 2',' 3',' 4',' 5')||array_intersect(array(' 2',' 3',' 4',' 5',' 6'),$dice)==array(' 2',' 3',' 4',' 5',' 6'))){
            $points=40;
            $untersumme+=$points;
        }

        else if ($fieldname=='dreierpasch' && max(array_count_values($dice))>=3){
            $points=array_sum($dice);
            $untersumme+=$points;
        }

        else if ($fieldname=='viererpasch' && max(array_count_values($dice))>=4){
            $points=array_sum($dice);
            $untersumme+=$points;
        }

        else if ($fieldname=='fullhouse' && count(array_count_values($dice))==2 && count($dice)==5){
            $points=25;
            $untersumme+=$points;
        }

        else if ($fieldname=='kniffel' && max(array_count_values($dice))>=5){
            $points=50;
            $untersumme+=$points;
        }

        else if ($fieldname=='chance'){
            $points=array_sum($dice);
            $untersumme+=$points;
        }
        
        if ($obersumme>63){
            $bonus=35;
        }

        /* update gamesheet */
        $updatepointsprep="UPDATE gamesheet SET ".$fieldname."=?,obersumme=?,untersumme=?,bonus=? WHERE playerid=?";
        $updatepoints=$connection->prepare($updatepointsprep);
        $updatepoints->bind_param("sssss",$points,$obersumme,$untersumme,$bonus,$playerid);
        $updatepoints->execute();
        $updatepoints->close();


        /* get list of players in current session */
        $selectplayersprep="SELECT playerlist FROM gamestate WHERE sessionid=?";
        $selectplayers=$connection->prepare($selectplayersprep);
        $selectplayers->bind_param("s",$sessionid);
        $selectplayers->execute();
        $selectplayers->bind_result($playerlist);
        $selectplayers->fetch();
        $selectplayers->close();

        /*get array of playerids*/
        $playerlistarray=json_decode($playerlist);

        /*look for own id in array */
        $position= array_search($playerid,$playerlistarray);

        /*select next id in list */
        $newposition=($position+1)%(count($playerlistarray));
        $newplayerid= $playerlistarray[$newposition];


        /*create new round for next id in array */
        $makerundeprep="INSERT INTO runde (sessionid,playerid) VALUES (?,?)";
        $makerunde=$connection->prepare($makerundeprep);
        $makerunde->bind_param("ss",$sessionid, $newplayerid);
        $makerunde->execute();
        $makerunde->close();

    }
?>