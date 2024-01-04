<!DOCTYPE html>
<html lang="es">
<head>
    <style>
:root {
    --main-color: black;
}
* {
    color-scheme: dark;
}
body {
    background-color: var(--main-color);
    display: grid;
    margin:0;
    padding:0;
    width: 100%;
    height: 100vh;
    font-family: sans-serif;
    grid-template-columns: min-content auto;
    grid-template-rows: 80px auto;
}
header {
    
}
.frame {
    height: 100%;
    width: 100%;
}
ul {
    display: inline;
}
    </style>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
</head>
<body>
    <div>

    </div>

    <header>
        <nav>
            <ul>
                <li><a href="pag1.html" target="frame">Pagina1</a></li>
                <li><a href="pag2.html" target="frame">Pagina2</a></li>
                <input id="color" type="color" onchange="updateColor()">
            </ul>
        </nav>
    </header>
    <div>
        
    </div>
    <main>
        <iframe class="frame" name="frame"></iframe>
    </main>
</body>
<script>
    let color = document.getElementById("color")
    function updateColor(){
        console.log(color.value)
        document.documentElement.style.setProperty('--main-color',color.value);
    }
</script>
</html>