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
        <h2><?= $user ? $user : ($first_name ? $first_name : $ci) ?></h2>
        <div class="options">
        <a title="Editar" href="./userRole.php?id=<?= $id ?>">
            <?php Template::render("icon",["icon" => "edit", "button" => 1]) ?>
        </a>
        <a title="Registrar Entrada - Salida" href="./enter.php?id=<?= $id ?>">
            <?php Template::render("icon",["icon" => "login", "button" => 1]) ?>
        </a>
        </div>
    </div>
    <div class="body">
        <?php Template::render("profile",   $template_file_data) ?>
    </div>    
    </a>
</li>
