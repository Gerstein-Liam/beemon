<!--                            
Nom du fichier: setup_relais_configuration_panel_view.php
Date: 2.septembre.2014
Auteur: Gerstein Liam (gerstein.liam@hotmail.com)
-------------------------------------------------
Explication: Charge un tableau ayant les informations du relais
choisie
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
  $query = "SELECT * FROM `relai_groupe` WHERE relaie_desc='" . $desc . "'";
  $result = $mysqli->query($query) or die($mysqli->error . __LINE__);
  if ($result->num_rows > 0) {
   while ($row = $result->fetch_assoc()) {
    $description_info = $row['relaie_desc'];
    $credit_info      = $row['credit'];
    echo '        <table>
					<tr>   
						<td>Description</td>
						<td>' . $description_info . '</td>
    
					</tr>  
					<tr>
						<td>Credit</td>
						<td>' . $credit_info . '</td></tr>
					</table>
					';
   }
  } else {
  }
 } else {
  echo "Please fill all fields";
 }
}
?>