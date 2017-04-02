<!--                             
Nom du fichier: connect_beemon_bd.php
Date: 2.septembre.2014
Auteur: Gerstein Liam (gerstein.liam@hotmail.com)
-------------------------------------------------
Explication: Permet de se connecter a la base de donnee Beemon
--> 
<?php

    $DB_NAME = 'beemon_bd';
    $DB_HOST = 'localhost';
    $DB_USER = 'root';
    $DB_PASS = '';

    $mysqli = new mysqli($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);
    
    if (mysqli_connect_errno()) {
        printf("Connect failed: %s\n", mysqli_connect_error());
        exit();
    }
?>
	