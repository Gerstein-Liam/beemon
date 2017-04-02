/*                          
Nom du fichier: add_device_controller.js
Date: 2.septembre.2014
Auteur: Gerstein Liam (gerstein.liam@hotmail.com)
-------------------------------------------------
Explication: Controlleur pour l'ajout de materiel.
             -ChooseDeviceType: Recupere choix type materiel a configurer et charge
			 la vue correspondents
	      -AddDevice : Recupere informations materiel a ajouter et charges le modèle correspondant
			   Modeles associe:
			   Relais:add_ruche_form_modele
			   Ruche:add_relais_form_modele
             
*/

function AddDevice(command)
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
	// Envoie des donnees du materiel pour ajout au modele correspondant
	switch(command) {
	case "add_relai":
	    xmlhttp.open("POST","./AddDevice/add_relais_form_modele.php",true);
	    xmlhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		xmlhttp.send("no_gsm="+document.forms["add_relais_Form"].elements["no_gsm"].value+"&"+"description="+document.forms["add_relais_Form"].elements["description"].value);
		break;
	case "add_ruche":
            var x = document.getElementById("RucheSelect").selectedIndex;
	    var y = document.getElementById("RucheSelect").options;
	    xmlhttp.open("POST","./AddDevice/add_ruche_form_modele.php",true);
	    xmlhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		xmlhttp.send("adr_mac="+document.forms["add_ruche_Form"].elements["adress_mac"].value+"&"+"description="+document.forms["add_ruche_Form"].elements["description"].value+"&"+"description_relais_responsable="+y[x].text);
		break;
	}
	// Chargement du contenu recu dans la div
	xmlhttp.onreadystatechange=function()
	{
		if (xmlhttp.readyState==4 && xmlhttp.status==200)
		{
			document.getElementById("add_device_form_status").innerHTML=xmlhttp.responseText;
		}
	}
}
 

function ChooseDeviceType() {

       	var xmlhttp;
	if (window.XMLHttpRequest)
	{
		xmlhttp=new XMLHttpRequest();
	}
	else
	{
		xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
	}
	


    document.getElementById("add_device_form_status").innerHTML=" ";
	
	var x = document.getElementById("DeviceSelect").selectedIndex;
	var y = document.getElementById("DeviceSelect").options;

	//On regarde s'il faut charger la vue formulaire de la ruche ou d'une relai
		switch(y[x].text) {
	case "Relais de groupe":
		xmlhttp.open("GET","./AddDevice/add_relais_form_view.php",true);
		break;
	case "Ruche equipee":
		xmlhttp.open("GET","./AddDevice/add_ruche_form_view.php",true);
		break;
	}
	
	
	xmlhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");
	xmlhttp.send(null);

	xmlhttp.onreadystatechange=function()
	{
		if (xmlhttp.readyState==4 && xmlhttp.status==200)
		{
			document.getElementById("add_device_form_content").innerHTML=xmlhttp.responseText;
		}
	}
	
}
