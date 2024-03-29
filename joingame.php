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

    /*set database to created database */
    $setdatabase="USE databasekniffel; ";
    $connection -> query($setdatabase);

    /*check if sessionid and nickname were set*/
    if (isset($_POST['sessionid'])&& isset($_POST['nickname'])&& $_POST['nickname']!='' && $_POST['sessionid']!=''){

        /* get id and nickname from post request */
        $sessionid=$_POST['sessionid'];
        $nickname=$_POST['nickname'];

        /*get session variables */
        $_SESSION['sessionid']=$_POST['sessionid'];
        $_SESSION['nickname']=$_POST['nickname'];

        /*get lists of players in current game*/
        $selectplayersprep="SELECT playerlist FROM gamestate WHERE sessionid=?";
        $selectplayers=$connection->prepare($selectplayersprep);
        $selectplayers->bind_param("s",$sessionid);
        $selectplayers->execute();
        $selectplayers->bind_result($playerlist);
        $selectplayers->fetch();
        $selectplayers->close();

        /*if other player exist */
        if ($playerlist!=NULL){

            /*make username hash */
            $salt=random_bytes(8);
            $time=new DateTime();
            $timestamp= $time->getTimestamp();
            $usernamehash=md5($salt.$nickname.$timestamp);

            /* decode playerlist, insert username and encode again  */
            $playerlistarray=json_decode($playerlist);
            array_push($playerlistarray,$usernamehash);
            $playerlistnew=json_encode($playerlistarray);

            /* create new player */
            $makeplayerprep="INSERT INTO player (nickname,playerid,sessionid) VALUES (?,?,?)";
            $makeplayer=$connection->prepare($makeplayerprep);
            $makeplayer->bind_param("sss",$nickname,$usernamehash, $sessionid);
            $makeplayer->execute();
            $makeplayer->close();


            /* update playerlist */
            $updateplayersprep="UPDATE gamestate SET playerlist=? WHERE sessionid=?";
            $updateplayers=$connection->prepare($updateplayersprep);
            $updateplayers->bind_param("ss",$playerlistnew,$sessionid);
            $updateplayers->execute();
            $updateplayers->close();


            /* create table for new player */
            $makegamesheetprep="INSERT INTO gamesheet (playerid,obersumme,untersumme,bonus) VALUES (?,0,0,0)";
            $makegamesheet=$connection->prepare($makegamesheetprep);
            $makegamesheet->bind_param("s",$usernamehash);
            $makegamesheet->execute();
            $makegamesheet->close();


            /* redirect to game */
            header('location: http://localhost/game5.html'); 
        }

        else{
            header('location: http://localhost/connect.html');
        }
    }

    else{
        header('location: http://localhost/connect.html'); 
    }

?>