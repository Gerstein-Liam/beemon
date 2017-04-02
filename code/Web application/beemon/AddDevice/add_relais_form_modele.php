<!--                             
Nom du fichier: add_relais_form_modele.php
Date: 2.septembre.2014
Auteur: Gerstein Liam (gerstein.liam@hotmail.com)
-------------------------------------------------
Explication: Recupère les donnees du formulaire d'ajout de relais.
On va ici associer le materiel a l'utilisateur
--> 
<?php include 'connect_beemon_bd.php';?>

<?php
session_start();
error_reporting(0);
if(isset($_POST))
{
	$no_gsm =htmlentities(trim($_POST['no_gsm']));
	$desc =htmlentities(trim($_POST['description'])); 
    // Si tout les champs entree enregistrement (pas de vérification doublon)
	if($no_gsm&&$desc)
	{   // Creation relai
		$query= "INSERT INTO `relai_groupe`(`id_groupe`, `relaie_desc`, `no_gsm`, `credit`) VALUES ('','" . $desc . "','" . $no_gsm . "',3)";
		$result = $mysqli->query($query) or die($mysqli->error.__LINE__);
		
		$query= 	"SELECT id_groupe FROM `relai_groupe` WHERE no_gsm='" . $no_gsm . "'";
		$result = $mysqli->query($query) or die($mysqli->error.__LINE__);
		
		if($result->num_rows > 0) {
			while($row = $result->fetch_assoc()) {
				$id_grp=$row['id_groupe'];
			}
		}
		else 
		{ 
		}
		// Association relai-utilasateur
		$user=$_SESSION['username'];
		$query= 	 "SELECT id_user FROM `utilisateur` WHERE username='" . $user . "'";
		$result = $mysqli->query($query) or die($mysqli->error.__LINE__);
		
		if($result->num_rows > 0) {
			while($row = $result->fetch_assoc()) {
				$id_user=$row['id_user'];
				$_SESSION['id_user_s']=$id_user;
			}
		}
		else 
		{ 
		}
		$query="INSERT INTO `possede`(`fk_id_user`, `fk_id_groupe`) VALUES (" . $id_user . "," . $id_grp . ")";
		$result = $mysqli->query($query) or die($mysqli->error.__LINE__);
		echo "Materiel ajoutee";	
	}
	else
	{
		echo "SVP entrez tout les champs";
	}
}

?>
