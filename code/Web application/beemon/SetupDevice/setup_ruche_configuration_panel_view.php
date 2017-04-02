<!--                            
Nom du fichier: setup_ruche_configuration_panel_view.php
Date: 2.septembre.2014
Auteur: Gerstein Liam (gerstein.liam@hotmail.com)
-------------------------------------------------
Explication: Charge un tableau modifiable pour la configuration
de la ruche choisie
--> 
<?php
include 'connect_beemon_bd.php';
?>

<?php
session_start();
error_reporting(0);
if (isset($_POST)) {
 $desc = htmlentities(trim($_POST['description']));
 if ($desc) {
  $query = "SELECT * FROM `ruche_equipee` WHERE ruche_desc='" . $desc . "'";
  $result = $mysqli->query($query) or die($mysqli->error . __LINE__);
  if ($result->num_rows > 0) {
   while ($row = $result->fetch_assoc()) {
    $description_info      = $row['ruche_desc'];
    $frequence_collecte    = $row['frequence_collecte'];
    $frequence_proposition = $row['frequence_proposition'];
    echo '        <table>
<tr>    
    <td>Description</td>
    <td>Frequence de collecte</td>
    <td>Frequence de proposition</td>
</tr>  
<tr>
    <td><input type="text" id="description_ruche" value="' . $description_info . '"></td>
    <td><input type="text" id="f_collecte" value="' . $frequence_collecte . '"></td>
    <td><input type="text" id="f_proposition" value="' . $frequence_proposition . '"></td>
</tr>  
</table>
<button type="button" style="width:130px" onclick="ChangeConfiguration()"  value="submit">Modifier</button>
    ';
   }
  } else {
  }
 } else {
  echo "Please fill all fields";
 }
}
?>