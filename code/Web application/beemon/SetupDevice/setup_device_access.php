<!--                             
Nom du fichier: setup_device_access.php
Date: 2.septembre.2014
Auteur: Gerstein Liam (gerstein.liam@hotmail.com)
-------------------------------------------------
Explication: Verifie si l'utilisateur a une Session php
ouverte.Si oui il autorise a afficher la vue 
--> 
<?php
try {
  session_start();
} catch(ErrorExpression $e) {
	echo "Pls log in ";
}  

if ($_SESSION['username']){
	
include 'setup_device_view.php';
	
}
?>









