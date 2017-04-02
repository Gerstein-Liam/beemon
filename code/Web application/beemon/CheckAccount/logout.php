
<!--                             
Nom du fichier: log_out.php
Date: 2.septembre.2014
Auteur: Gerstein Liam (gerstein.liam@hotmail.com)
-------------------------------------------------
Explication: Permet de se deconnecter, pour cela il faut detruire la 
session PHP
--> 


<?php
session_start();
session_destroy();
echo "Vous etes deconnecte";
?>