<!--                            
Nom du fichier: liste_ruche.php
Date: 2.septembre.2014
Auteur: Gerstein Liam (gerstein.liam@hotmail.com)
-------------------------------------------------
Explication: Charges tout les ruche relaye par un relais
--> 
<?php
include 'connect_beemon_bd.php';
?>
<?php
session_start();
if ($_SESSION['username'] && isset($_POST)) {
 $id_user   = $_SESSION['id_user_s'];
 $desc      = htmlentities(trim($_POST['description']));
 $next_step = htmlentities(trim($_POST['next_step']));
 $query     = "SELECT id_groupe FROM `relai_groupe` WHERE relaie_desc='" . $desc . "'";
 $result = $mysqli->query($query) or die($mysqli->error . __LINE__);
 if ($result->num_rows > 0) {
  while ($row = $result->fetch_assoc()) {
   $id_grp = $row["id_groupe"];
  }
 }
 $query = "SELECT ruche_desc FROM ruche_equipee AS p1, relaye AS p2 ,relai_groupe AS p3 WHERE p1.id_ruche = p2.fk_id_ruche AND p3.id_groupe = p2.fk_id_groupe AND p2.fk_id_groupe = '" . $id_grp . "'";
 $result = $mysqli->query($query) or die($mysqli->error . __LINE__);
 if ($result->num_rows > 0) {
  echo '
  
<p>Choisissez la ruche a configurer</p>
<select name="select" id="SetupRucheSelect" onchange="' . $next_step . '" style="width:220px">
 <option>       </option><br/>';
  while ($row = $result->fetch_assoc()) {
   $desc = $row["ruche_desc"];
   echo "<option>$desc</option><br/>";
  }
  echo '</select>';
 }
}
?>