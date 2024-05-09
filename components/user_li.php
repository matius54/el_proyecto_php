<?php
    global $base;
    $base = $base ?? "../php/";
    require_once $base . "libs/template.php";
?>
<li id="<?= $id ?? "" ?>">
    <div class="head">
        <a title="Expandir">
            <?php Template::render("icon",["icon" => "accordion", "button" => 1]) ?>
        </a>
        <h2><?= $user ?? "" ?></h2>
        <div class="options">
        <a title="Borrar">
            <?php Template::render("icon",["icon" => "delete", "button" => 1]) ?>
        </a>
        <a title="Editar">
            <?php Template::render("icon",["icon" => "edit", "button" => 1]) ?>
        </a>
        </div>
    </div>
    <div class="body">
        <i>Nombre de usuario: <?= $user ?></i>
        <i>Nombre: <?= $first_name ?></i>
        <i>Apellido: <?= $last_name ?></i>
        <i>Cedula: <?= $ci ?></i>
        <i>Fecha de nacimiento: <?= $birthday ?></i>
    </div>    
    </a>
</li>
