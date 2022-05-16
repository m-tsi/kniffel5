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


$fieldname=$_GET['fieldname'];
$sessionid=$_SESSION['sessionid'];

if (isset($fieldname)&& isset($sessionid)){




    if is_numeric($fieldname){
        /*...*/

    }

    else if ($fieldname=='kleinestrasse'){
        /*...*/
    }

    else if ($fieldname=='grossestrasse'){
        /*...*/
    }



    else{
        echo "Kein gültiger Feldname";

    }




}


?>