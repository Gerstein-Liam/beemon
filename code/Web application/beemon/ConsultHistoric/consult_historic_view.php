<!--                             
Nom du fichier: consult_historic_view.php
Date: 4.septembre.2014
Auteur: Gerstein Liam (gerstein.liam@hotmail.com)
-------------------------------------------------
Explication: Cadre principal pour la consultation des mesures 
             Il est decoupe en un panneau de selection et 
			 un panneau de visualisation.
			 
			 Remarque: ----------
			  
-->       
 <h1>Consultation historique de mesure</h1>
	<p>Choisir exploitation a consulter</p>
	 <div id="consult_historic_ruche_selector_panel_content_view"> 
		<select name="select" id="mySelect" onchange="BuildHistoricalGraph()" style="width: 220px">
				<option>              </option><br/>
				<?php include 'liste_relais.php'; ?>
		</select> 
	<div id="consult_historic_ruche_list">  </div>
	</div>
	
	<div id="dashboard_div">  
			<div id="line_div"   ><!-- Line chart renders here --></div>  
			<div id="control_div" style="margin-left:35px"  ><!-- Controls renders here --></div>  
	</div>
     
