<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="style.css">
    <script src="frontend_libs/theme.js"></script>
</head>
<body>
    <header class="nav">
        <h1>Iniciar sesión</h1>
    </header>
    <main>
        <form action="php/controller/user.php" method="post" autocomplete="off" class="card login hidden">
            <h1>Iniciar sesión</h1>
            <input type="text" name="username" placeholder="Nombre de usuario" match required>
            <input type="password" name="password" placeholder="Contraseña" required>
            <div class="options">
                <button type="submit">Iniciar sesión</button>
                <button type="reset">Limpiar</button>
            </div>
        </form>
    </main>
</body>
<script src="frontend_libs/validate.js">
</script>
<script>
    document.querySelector(".card.hidden").classList.remove("hidden");
</script>
<script>const THEME = new ThemeUI;</script>
</html>