<!--                             
Nom du fichier: consult_historic_modele.php
Date: 2.septembre.2014
Auteur: Gerstein Liam (gerstein.liam@hotmail.com)
-------------------------------------------------
Explication: Modele pour la récuperation des mesures 
sur la base de donnée .L'envoie se fait en JSON et est recupérer
par BuildHistoricalGraph
--> 

<?php include 'connect_beemon_bd.php';?>

<?php
session_start();
error_reporting(0);
if(isset($_POST))
{

    // On recupere l'id du relais selectionne  
	$ssc_to_consult =htmlentities(trim($_POST['ssc_to_consult']));
	$query=   "SELECT id_groupe FROM relai_groupe WHERE relaie_desc = '" .  $ssc_to_consult  . "'" ;
	$result = $mysqli->query($query) or die($mysqli->error.__LINE__);
	while($row = $result->fetch_assoc()) {
		$id_grp=$row["id_groupe"];
	}
	
	  // Definitions du JSON
	echo "{
		  \"cols\":[
			  {\"id\":\"Date\",\"label\":\"Date\",\"type\":\"datetime\"},
			  {\"id\":\"Echantillons\",\"label\":\"Values\",\"type\":\"number\"}
		  ]
		  ,
		  \"rows\":[
		  ";
		  
	//On récupere les valeurs et on remplit le JSON avec
	
	$query=   "SELECT valeur_echantillon,date_echantillon FROM echantillon_mesure WHERE fk_id_groupe = '" .  $id_grp . "' ORDER BY date_echantillon" ;
	$result = $mysqli->query($query) or die($mysqli->error.__LINE__);
	while($row = $result->fetch_assoc()) {
				//echo "--3";
		$date_echantillon=$row["date_echantillon"];
		$valeur_echantillon=$row["valeur_echantillon"];
		$format = 'Y-m-d H:i:s';
		$date = DateTime::createFromFormat($format,$date_echantillon);
		
		$seconde=$date->format('s');					
		$minute=$date->format('i');
		$heure=$date->format('H');   
		$jour=$date->format('d'); 
		$mois=$date->format('m');   
		$annee=$date->format('Y'); 
		$minute+=0;
		$heure+=0;   
		$jour+=0; 
		$mois-=1;
   
		$annee+=0; 
	  
		
		
		  $date_formate_googlechart= 'Date('.$annee.','.$mois .','.$jour .','.$heure .','.$minute .','.$seconde.')';
		
	       echo ' { "c" : [ { "v" :"'. $date_formate_googlechart.'"},
                            { "v" : '.$valeur_echantillon.' }
                   ] } ';
		   echo ' , '; 
	}
	echo ' ]}' ;
  }	
?>
