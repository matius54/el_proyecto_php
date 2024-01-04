<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <?php
    require_once "libs/tablex.php";
    require_once "libs/database.php";
    require_once "libs/google2fa.php";
    /*
    $db = connectDB();
    $result = $db->query("select * from user");

    echo "<table>";
    foreach ($result as $asd => $value) {
        echo "<tr>";
        $asd += 1;
            echo "<th>".$asd.")</th>";
            for ($y = 0; $y < 7; $y++) {
                echo "<th>".$value[$y]."</th>";
            }
        echo "</tr>";
    }
    echo "</table><br><br>";
    */

    echo "<div>";
    if (isset($_POST["user"]) && isset($_POST["key"])){
        $token = DB::getSecretFromUsername($_POST["user"]);
        $key = $_POST["key"];
        if(!empty($token) && !empty($key) && Google2FA::verify_key($token,$key)){
            echo "totp verificado";
        }
    }

    //connectDB();
    DB::getSecretFromUsername("Admin");

?>
<h1>LOGIN</h1>
<form action="" method="post">
	<label for="user">username:</label><br>
	<input type="text" id="user" name="user"><br>

	<label for="key">totp key:</label><br>
	<input type="text" id="key" name="key">

	<button>Login</button>
</form> 
</div>
</body>
</html>