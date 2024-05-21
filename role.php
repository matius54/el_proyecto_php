<?php 
    $title = "role";

    $base = "php/";
    require_once "php/libs/template.php";
?>
<!DOCTYPE html>
<html lang="es">
<?php Template::render("header",["title" => $title]) ?>
<body>
    <nav class="navbar">
        <ul>
            <li class="selected">
                <a href="./" class="logo" title="volver a la pagina principal" name="logo">
                    <h3 class="icon">TCS CRM WEB</h3>
                    <span>Pagina principal</span>
                </a>
            </li>
        </ul>
    </nav>
    <nav class="left-panel">
        <ul>
            <li>
                <a title="Nuevo rol" onclick="DIALOG.new()">
                    <img class="icon button" id="new" src="icons/new_dark.svg">
                </a>
            </li>
            <li>
                <a title="Editar rol" onclick="DIALOG.edit()">
                    <img class="icon button" id="edit" src="icons/edit_dark.svg">
                </a>
            </li>
            <li>
                <a title="Borrar rol" onclick="DIALOG.delete()">
                    <img class="icon button" id="delete" src="icons/delete_dark.svg">
                </a>
            </li>
        </ul>
    </nav>
    <main class="viewer">
    <?php Template::render("nav",["title" => $title]) ?>
    </main>
    <dialog class="role new">
        <div>
            <h2>Nuevo rol</h2>
            <input name="name" placeholder="Nombre">
            <textarea name="description" placeholder="Descripcion"></textarea>
            <input name="level" type="number" placeholder="Nivel">
            <div class="icons"></div>
            <div class="options">
                <button type="button" class="button close">Cerrar</button>
                <button type="button" class="button submit" onclick="DIALOG.submit()">Guardar</button>
            </div>
        </div>
    </dialog>
    <dialog class="role edit">
        <div>
            <h2>Editar rol</h2>
            <input name="name" placeholder="Nombre">
            <textarea name="description" placeholder="Descripcion"></textarea>
            <input name="level" type="number" placeholder="Nivel">
            <div class="icons"></div>
            <div class="options">
                <button type="button" class="button close">Cerrar</button>
                <button type="button" class="button submit" onclick="DIALOG.submit()">Actualizar</button>
            </div>
        </div>
    </dialog>
    <dialog class="role delete">
        <div>
            <h2>Borrar rol</h2>
            <i>Esta accion es permanente<br>y no se puede revertir</i>
            <div class="options">
                <button type="button" class="button">Cancelar</button>
                <button type="button" class="button close" onclick="DIALOG.submit()">Confirmar</button>
            </div>
        </div>
    </dialog>
</body>
<script>const THEME = new ThemeUI;</script>
<script src="frontend_libs/ajax.js"></script>
<script src="frontend_libs/list.js"></script>
<script src="frontend_libs/role.js"></script>
</html>