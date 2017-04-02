<!--                             
Nom du fichier: add_ruche_form_modele.php
Date: 2.septembre.2014
Auteur: Gerstein Liam (gerstein.liam@hotmail.com)
-------------------------------------------------
Explication: Recupère les donnees du formulaire d'ajout de ruche
On va ici associer le materiel a un relais
--> 

<?php include 'connect_beemon_bd.php';?>

<?php
session_start();
error_reporting(0);
if(isset($_POST))
{
	$adr_mac =htmlentities(trim($_POST['adr_mac']));
	$desc =htmlentities(trim($_POST['description'])); 
	$relais_desc=htmlentities(trim($_POST['description_relais_responsable'])); 
	if($adr_mac&&$desc&&$relais_desc)
	{
	    //Creation ruche
		$query= "INSERT INTO `ruche_equipee`(`id_ruche`, `ruche_desc`, `adr_mac`, `frequence_collecte`, `frequence_proposition`) VALUES ('','" . $desc . "','" . $adr_mac. "','1','2')";
		$result = $mysqli->query($query) or die($mysqli->error.__LINE__);
		
		$query= 	"SELECT id_ruche FROM `ruche_equipee` WHERE adr_mac='" . $adr_mac . "'";
		$result = $mysqli->query($query) or die($mysqli->error.__LINE__);
		echo "ID RUCHE";
		
		if($result->num_rows > 0) {
			while($row = $result->fetch_assoc()) {
				$id_ruche=$row['id_ruche'];
			}
		}
		else 
		{ 
		}
		
		$query= 	"SELECT id_groupe FROM `relai_groupe` WHERE relaie_desc='" . $relais_desc . "'";
		$result = $mysqli->query($query) or die($mysqli->error.__LINE__);
		echo "GROUPE";
		
		if($result->num_rows > 0) {
			while($row = $result->fetch_assoc()) {
				$id_grp=$row['id_groupe'];
			}
		}
		else 
		{ 
		}
		//Association ruche-relais
		$query="INSERT INTO `relaye`(`fk_id_groupe`, `fk_id_ruche`) VALUES (" . $id_grp . "," . $id_ruche . ")";
		$result = $mysqli->query($query) or die($mysqli->error.__LINE__);
	        echo "ruche ajoute";	
	        }
	else
	{
		echo "SVP entrez tout les champs";
	}
}

?>
