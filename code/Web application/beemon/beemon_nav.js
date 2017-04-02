/*                           
Nom du fichier: beemon_nav.js
Date: 2.septembre.2014
Auteur: Gerstein Liam (gerstein.liam@hotmail.com)
-------------------------------------------------
Explication: Controleur du menu principal.Il se charge de recuperer le choix de l'utilisateur
et de charger dans la section la fonction demandees
*/
function load_section(command)
{
    //Objet ActiveX ou JavaScript qui permet d'obtenir des donnEes au format XML, JSON,HTML,texte
	//a l'aide de requêtes HTTP.
	var xmlhttp;
	if (window.XMLHttpRequest)
	{
		xmlhttp=new XMLHttpRequest();
	}
	else
	{
		xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
	}
    // Test choix effectuee et requetes du contenu associe
	switch(command) {
	case "register_body":
		xmlhttp.open("GET","./CreateAccount/create_account_view.php",true);
		break;
	case "login_body":
		xmlhttp.open("GET","./CheckAccount/check_account_view.php",true);
		break;
	case "addnewdevice_body":
		xmlhttp.open("GET","./AddDevice/add_device_access.php",true);
		break;
	case "setup_body":
		xmlhttp.open("GET","./SetupDevice/setup_device_access.php",true);
       
		break;
	case "monitor_body":
		xmlhttp.open("GET","/MonitorBeehives/monitor_beehives_access.php",true);


		break;
	case "vhistory_body":
		xmlhttp.open("GET","./ConsultHistoric/consult_historic_access.php",true);
		break;
    case "visitor_body":
		xmlhttp.open("GET","./Visiteur/visitor.html",true);
		break;
		
	} 
     // Envoie de la requete
	xmlhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");
	xmlhttp.send();
    // Chargement du contenu recu dans la section
	xmlhttp.onreadystatechange=function()
	{
		if (xmlhttp.readyState==4 && xmlhttp.status==200)
		{
			document.getElementById("section_content").innerHTML=xmlhttp.responseText;
		}
	}

}
