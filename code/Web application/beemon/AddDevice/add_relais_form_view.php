<!--                             
Nom du fichier: add_relais_form_view.php
Date: 2.septembre.2014
Auteur: Gerstein Liam (gerstein.liam@hotmail.com)
-------------------------------------------------
Explication: Formulaire pour l'ajout d'un relais
             Controlleur-> AddDevice
--> 
<form name="add_relais_Form" method="post" action="">
			<p>Numero GSM</p>
			<input type="text" name="no_gsm">
			<p>Description</p>
			<input type="text" name="description">
                        <p></p>
			<button type="button" style="width:130px" onclick="AddDevice('add_relai')"  value="submit">Ajouter relai</button>	
</form> 