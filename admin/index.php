<!DOCTYPE html>

<?php

require_once '../dal/user.php';
require_once '../dal/session.php';
require_once '../includes/logger.php';


$error = '';
$userName = '';
$userPassword = '';

if ((isset($_POST["userName"])) && (isset($_POST["userPassword"]))) {
	if ((preg_match('/^[a-zA-Z]{1,20}$/',$_POST["userName"])) && (preg_match('/^[\s\S]{1,20}$/',$_POST["userPassword"]))) {

		$userName = $_POST["userName"];
		$userPassword = $_POST["userPassword"];
	}
	else {
		$error = 'Hibás felhasználó név vagy jelszó!';
	}

	if (strlen($userName .  $userPassword) != 0) {

		$result = User::login($userName, $userPassword);

		if ($result->isGood){
			$sessionId = Session::open($userName, $result->userRole);
			setcookie("sessionId", $sessionId, time() + (10 * 365 * 24 * 60 * 60), "/");
			$user = User::get($userName);
			if ($user->last_pwd_change == null){
					header('Location: content.php?menu=pwdChange&needNewPassword=true');
			}
			else {
					header('Location: content.php');
			}


		}
		else {
			$error = $result->error;
		}
	}
}

?>

<html>
<head>
<meta charset="UTF-8">
<title>Bejelentkezés az admin oldalra</title>
<style>
body{
	margin: 0px;
}

h1 {
	text-align:center;
}

#header {
	margin-bottom: 10px;
	height: 100px;
}

#header h1{
	margin-top: 0px;
	padding-top:20px;

}

#mainBox {
	border: solid 1px;
	width: 600px;
	margin-left: auto;
	margin-right: auto;
	border-radius: 10px;
}

#keyImage {
	display:inline-block;
	vertical-align: top;
	padding-top: 20px;"
}

#inputs{
	padding: 10px;
	display:inline-block;
}
.designColor{
	background-color: #4CAF50;
}

.fontColor{
	color: white;
}

.label{
	width: 100px;
	display: inline-block;
}

.centerPosition {
	text-align: center;
}
</style>

<?php include("includes/jslibs.php"); ?>
</head>
<body>
	<form action="index.php" method="post">
		<div class="designColor" id="header">
			<h1 class="fontColor" >Üdvözöljük az admin alkalmazásban!</h1>
		</div>
		<h1 >Bejelentkezés</h1>
		<div class="designColor fontColor" id="mainBox" >

			<div id="keyImage">
				<img src="images/key.png"/>
			</div>
			<div id="inputs">
				<span class="label">Felhasználó:</span>
				<input type="text" id="userName" name="userName" maxlength="20" value="<?php echo $userName ?>" />
				<br><br>
				<span class="label">Jelszó: </span>
				<input type="password" id="userPassword" maxlength="20" name="userPassword" />
				<br><br>
				<div class="centerPosition">
				<button id="send" >Bejelentkezés</button>
				<br>
				<span id="error" ><?php echo $error ?></span>
				</div>
			</div>
		</div>
	</form>
</body>

<script type="text/javascript">
var x = Util.getQueryVariable('x');
if (x == 'expired'){
  Util.removeUrlParams('x');
  Util.showErrorNotificationBar('Lejárt vagy nem található session!');
}
else if(x == 'error'){
  var msg = Util.getQueryVariable('msg');
  Util.removeUrlParams('x');
  Util.removeUrlParams('msg');
  Util.showErrorNotificationBar(msg);
}
else if(x == 'logout'){
  Util.removeUrlParams('x');
  Util.showNotificationBar('Sikeresen kijelentkezett', 2500, "gray", "white");
}
</script>
</html>
