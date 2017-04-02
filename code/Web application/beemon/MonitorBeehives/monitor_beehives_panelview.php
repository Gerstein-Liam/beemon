

<!--                             
Nom du fichier: monitor_beehives_panelview.php
Date: 2.septembre.2014
Auteur: Gerstein Liam (gerstein.liam@hotmail.com)
-------------------------------------------------
Explication: Permet de de notifier a la base de données
, puis au relais que l'utilisateur souhaite entrer en mode
de supervision direct.Une fois le relais choisi et la procedure lancer
le contenu de la page est rafraichie regulierement

Remarque:Il y a encore bcp de travail  et les codes
de ce dossier "monitorBeehives" ne represente pas la bonne approche
.
Pour le moment l'approche faite dans "SetupDevice" est celle retenue 
et qui sera appliquee a tout les fonctions qui la suivent 
(ordre d'apres interface)

Il s'agit donc ici d'un test de rafraichissement regulier de contenu
plus que d'un code aboutie			  
--> 
<?php include 'connect_beemon_bd.php';?>
<?php
if ($_SESSION['username']){
	$id_user=$_SESSION['id_user_s'];
    // Creation de la liste des relais en fonction de l'utilisateur engistrer dans la variable de session
	$query=   "SELECT relaie_desc,no_gsm FROM relai_groupe AS p1, possede AS p2 ,utilisateur AS p3 WHERE p1.id_groupe = p2.fk_id_groupe AND p3.id_user = p2.fk_id_user AND p2.fk_id_user =$id_user";
	$result = $mysqli->query($query) or die($mysqli->error.__LINE__);
	if($result->num_rows > 0) {
		echo "<select name=\"select\" id=\"mySelect\">";
		while($row = $result->fetch_assoc()) {
			$desc=$row["relaie_desc"];
			echo "<option>$desc</option><br/>";
		}
		echo "</select>  
		<button type=\"button\" onclick=\"CommunicationWithRelais_Controller('join')()\">Join it</button> 
		<button type=\"button\" onclick=\"CommunicationWithRelais_Controller('disconnect')()\">Disconnect it</button>
		
		<div id=\"action_selected\"> <div id=\"conn_status\"> " ;
	}}
?>



