<?php
    $title = "Programa de supervision de usuarios";

    $base = "php/";
    require_once "php/model/user.php";
    require_once "php/model/access.php";
    require_once "php/libs/template.php";
    require_once "php/libs/utils.php";

    $user_id = User::verify();
    //if(!Access::test("user.register", $user_id)) die("No tienes acceso para ver esta pagina");
    Template::render("header",["title" => $title]);
    Template::render("nav",["title" => $title, "hidden_back" => true]);
    Template::render("error");
?>
<img class="icon fundacite-logo" src="./icons/fundacite_min.svg" id="fundacite">
<form action="php/controller/user.php?a=login" method="post" autocomplete="off" class="card login">
    <h1>Iniciar sesión</h1>
    <input type="text" name="username" placeholder="Nombre de usuario" match required>
    <input type="password" name="password" placeholder="Contraseña" required>
    <div class="options">
        <button type="submit">Iniciar sesión</button>
        <button type="reset">Limpiar</button>
    </div>
</form>
<?php
    Template::render("footer");
?>