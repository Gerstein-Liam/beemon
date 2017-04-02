<!--                             
Nom du fichier: create_account_view.php
Date: 2.septembre.2014
Auteur: Gerstein Liam (gerstein.liam@hotmail.com)
-------------------------------------------------
Explication: Formulaire d'inscription d'un nouveau membre 
             Controlleur associe: create_account_controller.js
--> 
<h1>Please Sign in</h1>

<form name="myForm" method="post" action="">
	<p>Username</p>
	<input type="text" name="username">
	<p>Password</p>
	<input type="password" name="password">
	<p>Repeat password</p>
	<input type="password" name="repeated_password">
	<button type="button" style="width:130px" onclick="AddUser()"  value="submit">Register</button>
</form>

<div id="reg_rsp"></div>

