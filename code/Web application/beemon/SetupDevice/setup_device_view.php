<!--                             
Nom du fichier: setup_device_view.php
Date: 2.septembre.2014
Auteur: Gerstein Liam (gerstein.liam@hotmail.com)
-------------------------------------------------
Explication: Cadre principal pour la configuration
             Le contenu charger dans la DIV est 
             different selon le choix effectue par 
             l'utilisateur. Il charge soit la vue de configuration
	     de relais ou soit la vue de configuration de ruche
			  
--> 
<h1>Configuration de materiel</h1>
<p>Choisissez le type de materiel a configurer</p>
<select name="select" id="SetupDeviceSelect" onchange="SetupChooseDeviceType()">
   <option>               </option>
   <br/>
   <option>Relais de groupe</option>
   <br/>
   <option>Ruche equipee</option>
   <br/>
</select>
 <!-- On charge ici soit le panneau de configuration de ruches
     ou celui pour les relais  (setup_relais_view ou setup_ruche_view)
 --> 
<div id="selected_device_content_view">  </div>
