<?php

  


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





?>