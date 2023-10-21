<!DOCTYPE html>
<html>
	<head>
		<meta charset="UTF-8">
		<title>PHP - index.php</title>
		<link rel="stylesheet" href="style.css">
	</head>
	<body>

<?php
function connectDB()
{
	$servername = "localhost";
	$username = "root";
	$password = "";
	$dbname = "db1";

	try {
		$conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
		// set the PDO error mode to exception
		$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		echo "Connected successfully";
		return $conn;
	} catch (PDOException $e) {
		echo "Connection failed: " . $e->getMessage();
		return FALSE;
	}
}

echo "<table>";
for ($x = 0; $x <= 10; $x++) {
	echo "<tr>";
	for ($y = 0; $y <= 10; $y++) {
		if ($y % 2){
			continue;
		}
		echo "<th>Hola ".$x."-".$y."</th>";
	}
	echo "</tr>";
}
echo "</table>";

echo "<h1>gola</h1>";
?>
	</body>
</html>