<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TCM-CRM-WEB dashboard</title>
    <link rel="stylesheet" href="./style.css">
    <script src="./theme.js"></script>
    <script defer>const THEME = new ThemeUI;</script>
</head>
<body>
    <nav class="navbar">
        <ul>
            <li class="selected">
                <a href="./" class="logo" title="volver a la pagina principal" name="logo">
                    <h3 class="icon">TCS CRM WEB</h3>
                    <span>Pagina principal</span>
                </a>
            </li>
            <li class="option">
                <a href="#">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 8l0 4l2 2" /><path d="M3.05 11a9 9 0 1 1 .5 4m-.5 5v-5h5" /></svg>
                    <span>Registros</span>
                </a>
            </li>
            <li class="option"> 
                <a href="#">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0" /><path d="M12 10m-3 0a3 3 0 1 0 6 0a3 3 0 1 0 -6 0" /><path d="M6.168 18.849a4 4 0 0 1 3.832 -2.849h4a4 4 0 0 1 3.834 2.855" /></svg>
                    <span>Mi perfil</span>
                </a>
            </li>
            <li class="option"> 
                <a href="#">
                    <svg class="icon" width="24" height="24"></svg>
                    <span>Roles</span>
                </a>
            </li>
            <li class="option"> 
                <a href="#">
                    <svg class="icon" width="24" height="24"></svg>
                    <span>Usuarios</span>
                </a>
            </li>
            <li>
                <a href="#">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M14 8v-2a2 2 0 0 0 -2 -2h-7a2 2 0 0 0 -2 2v12a2 2 0 0 0 2 2h7a2 2 0 0 0 2 -2v-2" /><path d="M9 12h12l-3 -3" /><path d="M18 15l3 -3" /></svg>
                    <span>Cerrar sesion</span>
                </a>
            </li>
        </ul>
    </nav>
    <main>
        <div class="viewer"></div>
    </main>
    <!--
    <div class="notif"><ul></ul></div>
    <div class="screen"></div>
    -->
</body>
<!--
<script src="./json.js"></script>
<script src="./notif.js"></script>
<script src="./dash.js"></script>
-->
</html>