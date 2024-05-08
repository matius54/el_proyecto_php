<?php
    $title = "Registrar";

    $base = "php/";
    require_once "php/model/user.php";
    require_once "php/model/access.php";
    require_once "php/libs/template.php";
    require_once "php/libs/utils.php";

    $user_id = User::verify();
    if(!Access::test("user.register", $user_id)) die("No tienes acceso para ver esta pagina");
    Template::render("header",["title" => $title]);
    Template::render("nav",["title" => $title]);
?>
<img class="icon fundacite-logo" src="./icons/fundacite_min.svg" id="fundacite">
<form action="php/controller/user.php?a=register" method="post" autocomplete="off" class="card register">
    <h1>Registrarse</h1>
    <?php
        echo HTML::array2list(Role::getAllkv(), ["name"=>"role_id"]);
    ?>
    <input type="text" name="user" placeholder="Nombre de usuario" match required>
    <input type="text" name="first_name" placeholder="Nombres" required>
    <input type="text" name="last_name" placeholder="Apellidos" required>
    <input type="password" name="password" placeholder="Contraseña" required>
    <input type="password" name="password2" placeholder="Confirmar contraseña" required same="password">
    <input type="email" name="email" placeholder="Correo" required>
    <div class="ci">
        <select name="ci-type">
            <option>V</option>
            <option>E</option>
        </select>
        <input type="number" name="ci" placeholder="Cedula" required>
    </div>
    <textarea name="address" placeholder="Direccion"></textarea>
    <label>Fecha de nacimiento</label>
    <input type="date" name="birthday">
    <div class="options">
        <button type="submit">Registrarse</button>
        <button type="reset">Limpiar campos</button>
        <button type="button">Añadir Foto</button>
    </div>
</form>

<?php
    Template::render("footer");
?>