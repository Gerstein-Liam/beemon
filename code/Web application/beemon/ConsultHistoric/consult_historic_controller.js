/*                          
Nom du fichier: consult_historic_controller.js
Date: 2.septembre.2014
Auteur: Gerstein Liam (gerstein.liam@hotmail.com)
-------------------------------------------------
Explication: Controlleur la consultation de l'historique
Permet de d'afficher graphiquement les mesures collecte
par une ruche. Cependant comme explique dans le complément 
de réalisation les mesures ont été accolés au relais.
Il faut encore : 1) Creer la table capteur
				2) Modifier la liste deroulante creer dans setup et l'étendre 
					au capteur
				3) Demander au modele depuis la fonction ci dessous (BuildHistoricalGraphe)
					les valeurs associe au capteur,modifie ce dernier en conséquence
				4) Modifier le point d'entrée de relais "ssc_conn_point" pour qu'il accolle les mesure
					a la table capteur, cela comprends également de parser le json UPDATES
					jusqu'au capteur
*/
//  Charge le package "control" de l'api google charts
// Ce package donne la possibilité de créer des graphes en leur associant 
// un controlleur(reglage de la plage temporelle)
google.load('visualization', '1', {
    packages: ['controls']
});

function BuildHistoricalGraph() {
    //Recuperation du choix de relais effectuee
    var x = document.getElementById("mySelect").selectedIndex;
    var y = document.getElementById("mySelect").options;
    //Recuperation des donnees collectees associees au relais
    var json_data = $.ajax({
        type: "POST",
        url: "./ConsultHistoric/consult_historic_model.php",
        dataType: "json",
        data: "ssc_to_consult=" + y[x].text,
        async: false
    }).responseText;
    // Conversion du JSON en datatable
    var data = new google.visualization.DataTable(json_data);
    // Creation du container du panneau de consultation (graph+controlleur a associe ensuite) 
    var dash_container = document.getElementById('dashboard_div'),
        myDashboard = new google.visualization.Dashboard(dash_container);
    // Creation du controlleur de type Rangefilter,permet de regler la plage   
    var myDateSlider = new google.visualization.ControlWrapper({
        'controlType': 'ChartRangeFilter', //  Le type de controleur 
        'containerId': 'control_div', // La div ou il sera charger
        'options': {
            'filterColumnLabel': 'Date' // Le controlleur filtre la colonne 
                , // date du json contenant les mesures
            'ui': {
                'chartOptions': {
                    'height': '40',
                    'width': '660',
                    hAxis: {
                        format: 'dd.MMM.yyyy HH:mm'
                    }, // Format de la date affiche
                }
            }
        }
    });
    // Création d'un grah de type lineaire 
    var myLine = new google.visualization.ChartWrapper({
        'chartType': 'LineChart', // Type de graphique
        'containerId': 'line_div', // Div pour le graphique
        'options': {
            'width': '730',
            'height': '200',
            dateFormat: 'dd.MMM.yy HH:mm',
            // Active les interaction avec les graph-> dragToZoom en est une 
            // et permet de zoomer sur le graphique
            explorer: {
                actions: ['dragToZoom', 'rightClickToReset']
            },
            hAxis: {
                format: 'dd.MMM.yyyy HH:mm'
            }
        }
    });
    // On lit le controlleur et le graph (pas encore alimenté) au container
    // panneau de consultation (container genereal)	
    myDashboard.bind(myDateSlider, myLine);
    // On crée le panneau en alimentant avec les mesures reçus
    myDashboard.draw(data);
}