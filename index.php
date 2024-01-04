<!DOCTYPE html>
<html lang="es">
<html>
	<head>
		<meta charset="UTF-8">
		<title>PHP - index.php</title>
		<link rel="stylesheet" href="styles/default.css">
	</head>
	<body>
		<h3>Redireccionando a 
<?php
	$head = "Location: ".$_SERVER['REQUEST_URI'];
	if(true){
		echo "Login";
		header($head."login.php");
	}else{
		echo "Panel de control";
		header($head."dashboard.php");
	}
?>
		</h3>
	</body>
</html>