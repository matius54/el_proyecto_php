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
    <input type="text" name="user" placeholder="Nombre de usuario" match>
    <input type="text" name="first_name" placeholder="Nombres">
    <input type="text" name="last_name" placeholder="Apellidos">
    <input type="password" name="password" placeholder="Contraseña">
    <input type="password" name="password2" placeholder="Confirmar contraseña" same="password">
    <input type="email" name="email" placeholder="Correo">
    <div class="ci">
        <select name="ci-type">
            <option>V</option>
            <option>E</option>
        </select>
        <input type="number" name="ci" placeholder="Cedula">
    </div>
    <textarea name="address" placeholder="Direccion"></textarea>
    <label class="date">Fecha de nacimiento</label>
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
<script src="./frontend_libs/ajax.js"></script>
<script>
    const registerInputs = {
        "register.username" : "input[name='user']",
        "register.firstname" : "input[name='first_name']",
        "register.lastname" : "input[name='last_name']",
        "register.password" : "input[type='password']",
        "register.email" : "input[name='email']",
        "register.ci" : "div.ci select , div.ci input",
        "register.address" : "textarea[name='address']",
        "register.birthday" : "input[name='birthday'] , label.date",
        "register.photo" : "button[type='button']"
    }
    const updateInputs = () => {
        const element = document.querySelector("select[name='role_id']");
        const id = parseInt(element.value);
        ajax("./php/controller/access.php?a=getInputsForm", {id : id})
        .then(json => {
            for(const rinputs in registerInputs){
                const active = json[rinputs];
                const input = document.querySelectorAll(registerInputs[rinputs]);
                if(!input) continue;
                if(active){
                    input.forEach(e => e.removeAttribute("hidden"));
                }else{
                    input.forEach(e => e.setAttribute("hidden","hidden"));
                }
            }
        });

    };
    document.querySelector("select[name='role_id']").addEventListener("input", updateInputs);
    updateInputs();
</script>