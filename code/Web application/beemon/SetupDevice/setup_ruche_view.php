<!--                             
Nom du fichier: setup_ruche_view.php
Date: 2.septembre.2014
Auteur: Gerstein Liam (gerstein.liam@hotmail.com)
-------------------------------------------------
Explication: Cadre principal pour la configuration d'un ruche
             Cette vue est decoupe en un panneau de selection et 
			 un panneau de configuration
			  
--> 
<!-- On definie le panneau de selection de ruche, la liste des
 ruches est charge apres selection du relais-->   
<p>Choisir relais responsable de la ruche a configurer</p>
<div id="setup_ruche_selector_panel_content_view"> 
	<div id="relais_list" ></div>
	<div id="ruche_list">  </div>
</div>

 <!-- On charge ici le panneau de configuration de ruche-->  
<div id="setup_ruche_setup_panel_content_view"> </div>
 <!-- On charge ici le statut de configuration -->  
 <div id="setup_ruche_setup_panel_validation_content_view"> </div>
     
	 
 