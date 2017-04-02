<!--                             
Nom du fichier: check_account_modele.php
Date: 2.septembre.2014
Auteur: Gerstein Liam (gerstein.liam@hotmail.com)
-------------------------------------------------
Explication: Recupere donnee de connexion et crée une session php
si le compte est bon
--> 
<?php include 'connect_beemon_bd.php';?>

<?php
session_start();
error_reporting(0);
//Verifie si a recu du contenu POST
if(isset($_POST))
{
	$user =htmlentities(trim($_POST['username']));
	$pass =htmlentities(trim($_POST['password'])); 
    //Verifie tout les champs entres
	if($user&&$pass)
	{   
	    //Hache mot de passe en MD5, compare ce hache a celui dans la BD
		$pass = md5($pass);
		$query= "SELECT * FROM `utilisateur` WHERE username='" . $user . "'&&password='" . $pass . "'";
		$result = $mysqli->query($query) or die($mysqli->error.__LINE__);
		//Le hache du mot de passe fourni correpondant au hache dans la BD->Mots de passe correcte
		if($result->num_rows > 0) {
		    //Connexion:Creation d'une session PHP
			while($row = $result->fetch_assoc()) {
				$id_user=$row['id_user'];
				$_SESSION['id_user_s']=$id_user;
				$username=$row['username'];
				$_SESSION['username']=$username;
				echo "bienvenue  ".$_SESSION['username'];
			}
		}
		else
		{
			session_destroy();
			echo "Mot de passe ou utilisateur faux";
		}		
	}
	else
	{
		echo "SVP entrez tout les champs";
	}
}

?>
