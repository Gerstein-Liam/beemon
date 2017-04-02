<!--                             
Nom du fichier: add_ruche_form_view.php
Date: 2.septembre.2014
Auteur: Gerstein Liam (gerstein.liam@hotmail.com)
-------------------------------------------------
Explication: Formulaire pour l'ajout d'une ruche
             Controlleur-> AddDevice ('add_ruche')
			 de AddDeviceController.js
--> 
<form name="add_ruche_Form" method="post" action="">
			<p>Adresse Mac</p>
			<input type="text" name="adress_mac">
			<p>Description</p>
			<input type="text" name="description">
                        <p>Relais responsable de la ruche</p>
			<?php include 'liste_relais.php'; ?>
                        <p></p>
			<button type="button" style="width:130px" onclick="AddDevice('add_ruche')"  value="submit">Ajouter ruche</button>	
</form>