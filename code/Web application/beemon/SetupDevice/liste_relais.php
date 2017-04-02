 <!--                            
Nom du fichier: liste_relais.php
Date: 2.septembre.2014
Auteur: Gerstein Liam (gerstein.liam@hotmail.com)
-------------------------------------------------
Explication: Charges tout les relais d'un utilisateur dans une liste
deroulange
-->

<?php
include 'connect_beemon_bd.php';
?>
<?php
session_start();
if ($_SESSION['username'] && isset($_POST)) {
    $id_user   = $_SESSION['id_user_s'];
    $next_step = htmlentities(trim($_POST['next_step']));
    $query     = "SELECT relaie_desc,no_gsm FROM relai_groupe AS p1, possede AS p2 ,utilisateur AS p3 WHERE p1.id_groupe = p2.fk_id_groupe AND p3.id_user = p2.fk_id_user AND p2.fk_id_user =$id_user";
    $result = $mysqli->query($query) or die($mysqli->error . __LINE__);
    echo '<select name="select" id="SetupRelaisSelect" onchange="' . $next_step . '" style="width:220px">';
    if ($result->num_rows > 0) {
        echo "<option>         </option><br/>";
        while ($row = $result->fetch_assoc()) {
            $desc = $row["relaie_desc"];
            echo "<option>$desc</option><br/>";
        
}

        echo '</select>';
    }
}
?>
