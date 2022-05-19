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

    $sessionid=$_SESSION['sessionid'];
    $nickname=$_SESSION['nickname'];

    echo '$sessionid:'.$sessionid.'<br>';
    echo '$nn:'.$nickname.'<br>';

    /*if button is clicked */
        $fieldname=$_GET['fieldname'];
        echo '$fieldname:'.$fieldname.'<br>';
        /*get playerid for nickname */
        $selectrundeprep="SELECT playerid FROM player WHERE sessionid=? AND nickname=?";
        $selectrunde=$connection->prepare($selectrundeprep);
        $selectrunde->bind_param("ss",$sessionid,$nickname);
        $selectrunde->execute();
        $selectrunde->bind_result($playerid);
        $selectrunde->fetch();
        $selectrunde->close();
        echo '$playerid:'.$playerid.'<br>';


        /*load row2(selection) of current player last session */
        /*select last inserted runde  with session id*/
        $selectrundeprep="SELECT max(rundeid) FROM runde WHERE sessionid=? AND playerid=?";
        $selectrunde=$connection->prepare($selectrundeprep);
        $selectrunde->bind_param("ss",$sessionid,$playerid);
        $selectrunde->execute();
        $selectrunde->bind_result($rundeid);
        $selectrunde->fetch();
        $selectrunde->close();
        echo '$rundeid:'.$rundeid.'<br>';


        /*selct rollid of current wurf (max) */
        $selectrollidprep="SELECT rollid,selection,randomvalues FROM wuerfe WHERE rollid=(SELECT max(rollid) FROM wuerfe WHERE rundeid=?)";
        $selectrollid=$connection->prepare($selectrollidprep);
        $selectrollid->bind_param("s",$rundeid);
        $selectrollid->execute();
        $selectrollid->bind_result($rollid,$selection,$randomvalues);
        $selectrollid->fetch();
        $selectrollid->close();
        echo '$rollid:'.$rollid.'<br>';
        echo '$seleceti:'.$selection.'<br>';
        echo '$rand val:'.$randomvalues.'<br>';



        /*select previous points from table */
        $getpointsprep="SELECT obersumme,untersumme,bonus FROM gamesheet WHERE rundeid=? AND playerid=?";
        $getpoints=$connection->prepare($getpointsprep);
        $getpoints->bind_param("ss",$rundeid,$playerid);
        $getpoints->execute();
        $getpoints->bind_result($obersumme,$untersumme,$bonus);
        $getpoints->fetch();
        $getpoints->close();

        echo $obersumme;
        echo $untersumme;
        echo $bonus;
        
        /*get array of dice */
        $dice=json_decode($selection);

        $points=0;

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

        else if ($fieldname=='fullhouse' && count(array_count_values($dice))==2 && count($dice)==5){//todotodotodo
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


        

        /*update table values */
        
        /*selct rollid of current wurf (max) */
        /*$selectrollidprep="UPDATE gamesheet SET obersumme=?,untersumme=?,bonus=? WHERE rundeid=? AND playerid=?";
        $selectrollid=$connection->prepare($selectrollidprep);
        $selectrollid->bind_param("sss",$points,$rundeid,$playerid);
        $selectrollid->execute();
        $selectrollid->close();*/


        
        /*selct rollid of current wurf (max) */
        $selectrollidprep="UPDATE gamesheet SET ".$fieldname."=?,obersumme=?,untersumme=?,bonus=? WHERE rundeid=? AND playerid=?";
        $selectrollid=$connection->prepare($selectrollidprep);
        $selectrollid->bind_param("ssssss",$points,$obersumme,$untersumme,$bonus,$rundeid,$playerid);
        $selectrollid->execute();
        $selectrollid->close();

    
        

        

    

    
    



    /*header('location: http://localhost/game4.html');*/


?>