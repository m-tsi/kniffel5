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


    /*count rounds */
    $countwuerfeprep="SELECT COUNT(*) FROM runde WHERE sessionid=?";
    $countwuerfe=$connection->prepare($countwuerfeprep);
    $countwuerfe->bind_param("s",$sessionid);
    $countwuerfe->execute();
    $countwuerfe->bind_result($rundeamount);
    $countwuerfe->fetch();
    $countwuerfe->close();

    /*print number of rounds */
    echo $rundeamount;


?>