<!--                             
Nom du fichier: add_device_view.php
Date: 2.septembre.2014
Auteur: Gerstein Liam (gerstein.liam@hotmail.com)
-------------------------------------------------
Explication: Vue (cadre) principal pour l'ajout de materiel
On demande d'abord quelle type de materiel (relais ou ruche)
il faut ajouter.Puis on charge le contenu correspondant
           Controlleur associe: ChooseDeviceType_Controler de 
		   add_device_controller.js
--> 
 <!--Cadre pour la view permettant de entrer les informations materiel--> 

<h1>Ajout de materiel</h1>
<p>Choisissez le type de materiel a ajouter</p>
<select name="select" id="DeviceSelect" onchange="ChooseDeviceType()">
       <option>                </option><br/>
	   <option>Relais de groupe</option><br/>
	   <option>Ruche equipee</option><br/>
</select>  
 <!--Cadre ou est chargee la vue permettant de entrer les informations materiel.
 Il peut soit charger soit la vue "add_ruche_form_view" ou "add_relais_form_view"
 --> 
<div id="add_device_form_content">  </div>
 <!--Cadre ou est charge le statut de l'ajout--> 
<div id="add_device_form_status"> </div>


	









	