<!--                            
Nom du fichier: monitor_beehives_beedatas_model.php
Date: 2.septembre.2014
Auteur: Gerstein Liam (gerstein.liam@hotmail.com)
-------------------------------------------------
Explication: Modele pour la supervision de ruche en direct

Meme remarque: Pas aboutie
--> 
<?php
include 'connect_beemon_bd.php';
?>

<?php
session_start();
error_reporting(0);
if (isset($_POST)) {
 $ssc_to_join = htmlentities(trim($_POST['ssc_to_join']));
 $id_user     = $_SESSION['id_user_s'];
 // Recupere les l'id de relais selectionne
 $query       = "SELECT id_groupe FROM relai_groupe WHERE relaie_desc = '" . $ssc_to_join . "'";
 $result = $mysqli->query($query) or die($mysqli->error . __LINE__);
 while ($row = $result->fetch_assoc()) {
  $id_grp = $row["id_groupe"];
 }
 // Indique au relais de se rester connecté et de faire du polling la prochaine fois 
 //qu'il se connecte
 if ($_POST['command'] == "join") {
  $query = "INSERT INTO `communications`(`fk_id_user`, `fk_id_groupe`,`conn_status`,`id_msg_for_user`,`id_msg_for_relaie`) VALUES ('" . $id_user . "','" . $id_grp . "','waiting','needu','')";
  $result = $mysqli->query($query) or die($mysqli->error . __LINE__);
  echo "You want join";
 }
 // Indique au relais de se deconnecte
 if ($_POST['command'] == "disconnect") {
  $query = " DELETE  FROM `communications` WHERE fk_id_user = '" . $id_user . "' AND fk_id_groupe='" . $id_grp . "'";
  $result = $mysqli->query($query) or die($mysqli->error . __LINE__);
  echo "You want disconnect";
 }
}
// Rafraichie le statut de communication dans le panneau de supervision
if ($_POST['command'] == "refresh") {
 $query = "SELECT conn_status FROM communications WHERE fk_id_user = '" . $id_user . "'";
 $result = $mysqli->query($query) or die($mysqli->error . __LINE__);
 while ($row = $result->fetch_assoc()) {
  $conn_status = $row["conn_status"];
  echo $conn_status;
 }
}
?>