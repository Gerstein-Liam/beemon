<!--                             
Nom du fichier: setup_ruche_configuration_panel_modele.php
Date: 2.septembre.2014
Auteur: Gerstein Liam (gerstein.liam@hotmail.com)
-------------------------------------------------
Explication: Modele pour la configuration de la ruche.
Son role est de recuperer les nouvelles configurations et de les enregistrer
--> 
<?php include 'connect_beemon_bd.php';?>

<?php
session_start();
error_reporting(0);
if(isset($_POST))
{
	$old_desc =htmlentities(trim($_POST['old_description_ruche'])); 
	$new_desc =htmlentities(trim($_POST['new_description_ruche'])); 
	$new_f_collecte =htmlentities(trim($_POST['new_f_collecte'])); 
	$new_f_proposition=htmlentities(trim($_POST['new_f_proposition'])); 
	
    // On recupere l'ancienne description pour trouver la table
	// Ensuite on met a jour la table
	$query= 	"SELECT id_ruche FROM `ruche_equipee` WHERE ruche_desc='" . $old_desc. "' ";
	$result = $mysqli->query($query) or die($mysqli->error.__LINE__);

	if($result->num_rows > 0) {
		while($row = $result->fetch_assoc()) {
			$id_ruche=$row['id_ruche'];
		}
	}
	$query= "UPDATE `ruche_equipee` SET `ruche_desc`='" . $new_desc. "' ,`frequence_collecte` = " . $new_f_collecte. "  ,`frequence_proposition`= " . $new_f_proposition ."   WHERE id_ruche= " . $id_ruche. "  ";
	$result = $mysqli->query($query) or die($mysqli->error.__LINE__);
}

?>
