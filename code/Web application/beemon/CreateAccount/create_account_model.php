<!--                             
Nom du fichier: create_account_modele.php
Date: 2.septembre.2014
Auteur: Gerstein Liam (gerstein.liam@hotmail.com)
-------------------------------------------------
Explication: Recupere champs inscription et enregistre nouvelle utilisateur apres 
             quelques tests
--> 
<?php include 'connect_beemon_bd.php';?>

<?php
error_reporting(0);
if(isset($_POST))
{
	$user =htmlentities(trim($_POST['username']));
	$pass =htmlentities(trim($_POST['password'])); 
	$rpass =htmlentities(trim($_POST['repeated_password']));
	//Test tout les champs entrees
	if($user&&$pass&&$rpass)
	{
		//Test si les deux mots de passe correspondent
		if($pass==$rpass)
		{
		    //Hache en  MD5 le mots de passe saisi et l'enregistre dans la BD
			$pass = md5($pass);
			$query= "SELECT username FROM `utilisateur` WHERE username='" . $user . "'";
			$result = $mysqli->query($query) or die($mysqli->error.__LINE__);
			 //Test si nom utilisateur deja utilise
			if( $result->num_rows ==0)
			{    //Enregistre le nouveau membre
				$query= "INSERT INTO `utilisateur`(`id_user`, `username`, `password`) VALUES ('','" . $user . "','" . $pass . "')";
				$result = $mysqli->query($query) or die($mysqli->error.__LINE__);
				mysqli_close($mysqli);
				echo "registered";
			}

			else
			{
				echo "username already exist";
			}
		}
		else
		{
			echo "The passwords don't match, pls enter them again";
		}	
	}
	else
	{
		echo "Please fill all fields";
	}
}
?>
