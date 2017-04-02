/*                          
Nom du fichier: check_account_controller.js
Date: 2.septembre.2014
Auteur: Gerstein Liam (gerstein.liam@hotmail.com)
-------------------------------------------------
Explication: Controlleur pour le formulaire de connexion, renvoie le statut d'enr
istrement dans la div "log_rsp" de la vue.
             Modele associe: create_account_modele.php
*/

function LogUser(command)
{
	var xmlhttp;
	if (window.XMLHttpRequest)
	{
		xmlhttp=new XMLHttpRequest();
	}
	else
	{
		xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
	}

	switch(command) {
	case "log_in":
		xmlhttp.open("POST","./CheckAccount/check_account_model.php",true);
		xmlhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		xmlhttp.send("username="+document.forms["logForm"].elements["username"].value+"&"+"password="+document.forms["logForm"].elements["password"].value);



		break;
	case "log_out":
		xmlhttp.open("POST","./CheckAccount/logout.php",true);
		xmlhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		xmlhttp.send();
		break;
	}

	xmlhttp.onreadystatechange=function()
	{
		if (xmlhttp.readyState==4 && xmlhttp.status==200)
		{
			document.getElementById("log_rsp").innerHTML=xmlhttp.responseText;
		}
	}
}


