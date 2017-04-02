<!--                             
Nom du fichier: consult_historic_access.php
Date: 4.septembre.2014
Auteur: Gerstein Liam (gerstein.liam@hotmail.com)
-------------------------------------------------
Explication: Verifie si l'utilisateur a une Session php
ouverte.Si oui il autorise a afficher la vue 
--> 

<?php
try {
	session_start();
} 
catch(ErrorExpression $e) {
	echo "Veuillez vous connecté ";
}  

include 'consult_historic_view.php';


?>




