<!--                             
Nom du fichier: index.php
Date: 2.septembre.2014
Auteur: Gerstein Liam (gerstein.liam@hotmail.com)
-------------------------------------------------
Explication: Page HTML racine du site Web. Definie l'ossature de l'application
--> 
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<HTML>
<head>
	<link rel="stylesheet"  type="text/css" href="style.css" />
	<meta charset="utf-8" />
	<!-- API Google pour faire de l'AJAX, utilisee notamment pour la supervision en direct  --> 
	<script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
	<!-- API Google pour construire les graphiques "google chart" --> 
	<script type="text/javascript" src="https://www.google.com/jsapi"></script>
	<script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
    <!-- Controleurs du menu principal(modele MVC) " --> 
	<script src="beemon_nav.js"></script>
	<script src="monitor_cmd.js"></script>
	<!-- Controleurs pour les differentes fonctions (modele MVC) " --> 
	<script src="./CreateAccount/create_account_controller.js"></script>
	<script src="./CheckAccount/check_account_controller.js"></script>
	<script src="./AddDevice/add_device_controller.js"></script>
	<script src="./SetupDevice/setup_device_controller.js"></script>
	<script src="./MonitorBeehives/monitor_beehives_controller.js"></script>
	<script src="./ConsultHistoric/consult_historic_controller.js"></script>	

	<title>BEEMON APP</title>
</head>
<body>
	<header>
			<img class="Image_titre" src="titre_eiafr.jpg" alt="some_text">
	</header>
	 <!-- Menu de navigation " --> 
	<nav>
		<ul>
			<menu type="toolbar">
			<button type="button" style="width:130px" onclick="load_section('register_body')">S'inscrire</button>
			<button type="button" style="width:130px" onclick="load_section('login_body')">Se connecter</button><br/> <br/>
			<button type="button" style="width:130px" onclick="load_section('addnewdevice_body')">Ajouter relais/ruches</button>
			<button type="button" style="width:130px" onclick="load_section('setup_body')">Administrer relais/ruches</button><br/> <br/>
			<button type="button" style="width:130px" onclick="load_section('monitor_body')">Surveiller ruches en direct</button>
			<button type="button" style="width:130px" onclick="load_section('vhistory_body')">Consulter donnees collectees</button><br/> <br/>
            <button type="button" style="width:130px" onclick="load_section('visitor_body')">visiteur</button>
			</menu>
		</ul>
	</nav>

	<section>
	     <!-- container central ou sont chargees les fonctions" --> 
		<div  id="section_content"></div>
	</section>

	<footer>
		<p class="footer">EIA-FR - Projet de bachelor 2014</p>
	</footer>
</body>
</html> 





	
