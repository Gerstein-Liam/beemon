/*                          
Nom du fichier: setup_device_controller.js
Date: 2.septembre.2014
Auteur: Gerstein Liam (gerstein.liam@hotmail.com)
-------------------------------------------------
Explication: Controlleur pour l'ajout de materiel.
                  
*/
/*
Controlleur de la liste principal appelant soit la vue de selection+configuration de ruche ou soit 
vue de selection+configuration du relais ()
*/
function SetupChooseDeviceType() {
    var xmlhttp;
    if (window.XMLHttpRequest) {
        xmlhttp = new XMLHttpRequest();
    } else {
        xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
    }
    var x = document.getElementById("SetupDeviceSelect").selectedIndex;
    var y = document.getElementById("SetupDeviceSelect").options;
    switch (y[x].text) {
        case "Relais de groupe":
            xmlhttp.open("GET", "./SetupDevice/setup_relais_view.php", true);
            break;
        case "Ruche equipee":
            xmlhttp.open("GET", "./SetupDevice/setup_ruche_view.php", true);
            break;
    }
    xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xmlhttp.send(null);
    xmlhttp.onreadystatechange = function() {
        if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
            document.getElementById("selected_device_content_view").innerHTML = xmlhttp.responseText;
            switch (y[x].text) {
                case "Relais de groupe":
                    // Ici on charge la liste de relais et on indique que lorsqu'un relais est selection on charge 
                    // un panneau de configuration de relais
                    ListHandler('ListeRelais', 'LoadRelaiConfPannel(\'useless arg\')');
                    break;
                case "Ruche equipee":
                    //Ici on on charge la liste des relais aussi, mais on la liste pointe sur celle de la liste de ruche
                    // et que cette liste va appeller le panneau de configuration de ruche.
                    ListHandler('ListeRelais', 'ListHandler(\'ListeRuches\' , \' LoadRucheConfPannel("useless arg")\')');
                    break;
            }
        }
    }
}
/*
	Gestionnaire de liste BEEMON programmable, creation de l'arborescence de liste BEEMON
 Le premier argument correspondant a la liste qu'il faut charger, le second corresponds 
a la function qui est appellé lors qu il y a une action sur cette liste par l'utilisateur
                                    Exemple    
ListHandler('ListeRelais','ListHandler(\'ListeRuches\' , \' LoadRucheConfPannel("useless arg")\')'); 
            -----------   ----------------------------------------------------------------------
               ARG1                                         ARG2 
          Liste souhaitee                             fonction appelle par la liste 
*/
function ListHandler(desired_list, fct_called_by_the_list) {
    var xmlhttp;
    if (window.XMLHttpRequest) {
        xmlhttp = new XMLHttpRequest();
    } else {
        xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
    }
    switch (desired_list) {
        ///////En fonction de relai choisi, on recupere la liste des ruches//////
        case "ListeRelais":
            xmlhttp.open("POST", "./SetupDevice/liste_relais.php", true);
            xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
            xmlhttp.send("next_step=" + fct_called_by_the_list);
            break;
        case "ListeRuches":
            var x = document.getElementById("SetupRelaisSelect").selectedIndex;
            var y = document.getElementById("SetupRelaisSelect").options;
            xmlhttp.open("POST", "./SetupDevice/liste_ruche.php", true);
            xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
            xmlhttp.send("description=" + y[x].text + "&" + "next_step=" + fct_called_by_the_list);
            break;
            ///////En fonction de la ruche choisie, on recupere la configuration///////
    }
    xmlhttp.onreadystatechange = function() {
        if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
            switch (desired_list) {
                case "ListeRelais":
                    document.getElementById("relais_list").innerHTML = xmlhttp.responseText;
                    break;
                case "ListeRuches":
                    document.getElementById("ruche_list").innerHTML = xmlhttp.responseText;
                    break;
            }
        }
    }
}
/*
				    Chargement du panneau de configuration du relais qui vient d'etre selectionné
*/
function LoadRelaiConfPannel(arg) {
    var xmlhttp;
    if (window.XMLHttpRequest) {
        xmlhttp = new XMLHttpRequest();
    } else {
        xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
    }
    var x = document.getElementById("SetupRelaisSelect").selectedIndex;
    var y = document.getElementById("SetupRelaisSelect").options;
    ///////En fonction de la relais choisie, on recupere la configuration///////
    xmlhttp.open("POST", "./SetupDevice/setup_relais_configuration_panel_view.php", true);
    xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xmlhttp.send("description=" + y[x].text);
    xmlhttp.onreadystatechange = function() {
        if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
            document.getElementById("setup_relai_setup_panel_content_view").innerHTML = xmlhttp.responseText;
        }
    }
}
/*
				    Chargement du panneau de configuration du de la ruche qui vient d'etre selectionné
*/
function LoadRucheConfPannel(arg) {
    var xmlhttp;
    if (window.XMLHttpRequest) {
        xmlhttp = new XMLHttpRequest();
    } else {
        xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
    }
    var x = document.getElementById("SetupRucheSelect").selectedIndex;
    var y = document.getElementById("SetupRucheSelect").options;
    xmlhttp.open("POST", "./SetupDevice/setup_ruche_configuration_panel_view.php", true);
    xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xmlhttp.send("description=" + y[x].text);
    xmlhttp.onreadystatechange = function() {
        if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
            document.getElementById("setup_ruche_setup_panel_content_view").innerHTML = xmlhttp.responseText;
        }
    }
}
///////Controlleur pour la modifications de la configuration d'une ruche///////
function ChangeConfiguration() {
    var xmlhttp;
    if (window.XMLHttpRequest) {
        xmlhttp = new XMLHttpRequest();
    } else {
        xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
    }
    // On recupere la description de la ruche que l'on est en train de modifier
    var x = document.getElementById("SetupRucheSelect").selectedIndex;
    var y = document.getElementById("SetupRucheSelect").options;
    xmlhttp.open("POST", "./SetupDevice/setup_ruche_configuration_panel_model.php", true);
    //Envoie nouvelle configuration au modele, lui s'occupera de la mettre dans la BD
    xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xmlhttp.send("old_description_ruche=" + y[x].text + "&" + "new_description_ruche=" + document.getElementById("description_ruche").value + "&" + "new_f_collecte=" + document.getElementById("f_collecte").value + "&" + "new_f_proposition=" + document.getElementById("f_proposition").value);
    xmlhttp.onreadystatechange = function() {
        if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
            document.getElementById("setup_ruche_setup_panel_validation_content_view").innerHTML = xmlhttp.responseText;
        }
    }
}