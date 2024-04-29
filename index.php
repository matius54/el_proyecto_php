<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Página principal</title>
    <link rel="stylesheet" href="style.css">
    <script src="frontend_libs/theme.js"></script>
    <script defer>const THEME = new ThemeUI;</script>
</head>
<body>
    <header class="nav">
        <h1>Página principal</h1>
    </header>
    <main>
        <div class="card padd">
            <h1>Opciones</h1>
            <a href="./login.html" class="button">Iniciar sesión</a>
            <a href="./register.html" class="button">Registrarse</a>
            <a href="./dashboard.html" class="button">Panel de control</a>
            <a href="#" class="button" onclick="toggleDarkMode()">Cambiar a modo claro</a>
        </div>
    </main>
</body>
<script>
    const button = document.querySelector("a[href=\"#\"]");
    var isDark = THEME.getValue("theme") === "dark";
    updateButton();
    function toggleDarkMode(){
        THEME.setTheme(isDark ? "light" : "dark");
        isDark = !isDark;
        updateButton();
    }
    function updateButton(){
        button.innerText = `Cambiar a modo ${isDark ? "claro" : "oscuro"}`;
    }
</script>
</html>