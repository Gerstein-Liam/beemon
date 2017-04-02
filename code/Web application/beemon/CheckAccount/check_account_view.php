<!--                             
Nom du fichier: check_account_view.php
Date: 2.septembre.2014
Auteur: Gerstein Liam (gerstein.liam@hotmail.com)
-------------------------------------------------
Explication: Formulaire pour la connexion de l'utilisateur
			 Controlleur associe     CheckAccountController.js
--> 
<h1>Se connecter</h1>

<form name="logForm" method="post" action="">
	<p>Username</p>
	<input type="text" name="username">
	<p>Password</p>
	<input type="password" name="password">
	<button type="button" style="width:130px" onclick="LogUser('log_in')"  value="submit">Login</button>
	<button type="button" style="width:130px" onclick="LogUser('log_out')"  value="submit">Logout</button>
</form>

<div id="log_rsp"></div>
