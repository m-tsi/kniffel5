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

    /*get playerid from nickname */
    $selectplayeridprep="SELECT playerid FROM player WHERE sessionid=? AND nickname=?";
    $selectplayerid=$connection->prepare($selectplayeridprep);
    $selectplayerid->bind_param("ss",$sessionid,$nickname);
    $selectplayerid->execute();
    $selectplayerid->bind_result($playerid);
    $selectplayerid->fetch();
    $selectplayerid->close();

    /*get last runde for current player*/
    $selectrundeprep="SELECT max(rundeid) FROM runde WHERE sessionid=? AND playerid=?";
    $selectrunde=$connection->prepare($selectrundeprep);
    $selectrunde->bind_param("ss",$sessionid,$playerid);
    $selectrunde->execute();
    $selectrunde->bind_result($rundeid);
    $selectrunde->fetch();
    $selectrunde->close(); 

        
    /*get rollid for current runde */
    $selectrollidprep="SELECT rollid FROM wuerfe WHERE rundeid=?";
    $selectrollid=$connection->prepare($selectrollidprep);
    $selectrollid->bind_param("s",$rundeid);
    $selectrollid->execute();
    $selectrollid->bind_result($rollid);
    $selectrollid->fetch();
    $selectrollid->close(); 
    
    /*number of random dice for current round */
    $randomlength=0;

    /*set length to 5 if no rolls for current round exist yet*/
    if ($rollid==NULL){
        $randomlength=5;
    }

    else{

        /*count wuerfe */
        $countwuerfeprep="SELECT COUNT(*) FROM wuerfe WHERE rundeid=?";
        $countwuerfe=$connection->prepare($countwuerfeprep);
        $countwuerfe->bind_param("s",$rundeid);
        $countwuerfe->execute();
        $countwuerfe->bind_result($wurfamount);
        $countwuerfe->fetch();
        $countwuerfe->close();


        /* get rundeid,rollid,randomvalues,selection of laft wurf */
        $wurfinfoprep="SELECT rundeid,rollid,randomvalues,selection FROM wuerfe WHERE rollid=(SELECT max(rollid) FROM wuerfe WHERE rundeid=?)";
        $wurfinfo=$connection->prepare($wurfinfoprep);
        $wurfinfo->bind_param("s",$rundeid);
        $wurfinfo->execute();
        $wurfinfo->bind_result($rundeid,$rollid,$randomvalues,$selection);
        $wurfinfo->fetch();
        $wurfinfo->close();

        /*set number of dice as length of last dice set */
        $randomlength=count(json_decode($randomvalues));

    }

    /*check if number of dice >0 and number of rolls in runde <3*/
    if ($wurfamount<3 && $randomlength>0){

        /*create new wurf */
        $newwurfdraft="INSERT INTO wuerfe (rundeid,randomvalues,selection) VALUES (?,?,?)";
        $newwurf=$connection->prepare($newwurfdraft);

        /*create random array*/
        $randarray=array();
        for ($i=0 ; $i<$randomlength; $i++){
            $randarray[$i]=rand(1,6);
        }
        $randomstring=json_encode($randarray);
        
        
        /*update random values into table */
        if ($selection==null){
            $newselection=[];
        }
        else{
            $newselection=json_decode($selection);
        }
        $newselectionstring=json_encode($newselection);

        $newwurf->bind_param("sss",$rundeid,$randomstring,$newselectionstring);
        $newwurf->execute();

    }

?>