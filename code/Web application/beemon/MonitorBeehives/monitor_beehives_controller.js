/*                          
Nom du fichier: monitor_beehives_controller.js
Date: 2.septembre.2014
Auteur: Gerstein Liam (gerstein.liam@hotmail.com)
-------------------------------------------------
Explication: Controlleur pour la supervision en directories
Pas aboutie
Permet juste rafraichir contenu de la page régulierement.
                  
*/
var intervalId = null;

function CommunicationWithRelais_Controller(command) {
    var x = document.getElementById("mySelect").selectedIndex;
    var y = document.getElementById("mySelect").options;
    var xmlhttp;
    if (window.XMLHttpRequest) {
        xmlhttp = new XMLHttpRequest();
    } else {
        xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
    }
    xmlhttp.open("POST", "./MonitorBeehives/monitor_beehives_beedatas_model.php", true);
    xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xmlhttp.send("ssc_to_join=" + y[x].text + "&" + "command=" + command);
    switch (command) {
        case "join":
            /*                          
		Lance un polling javascript-> PHP (AJAX).La reponse va dans la div conn_status
                  
		*/
            intervalId = setInterval(function() {
                $.ajax({
                    type: "POST",
                    url: "./MonitorBeehives/monitor_beehives_beedatas_model.php",
                    data: "command=refresh",
                    success: function(msg) {
                        $('#conn_status').html(msg);
                    },
                    dataType: "html"
                });
            }, 1000);
            break;
        case "disconnect":
            clearInterval(intervalId);
            $('#conn_status').html("");
            break;
    }
}