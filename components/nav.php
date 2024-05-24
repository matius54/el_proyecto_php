<?php
    //global $base;
    //$base = $base ?? "../php/";
    //require_once $base . "model/user.php";

    //$user_id = User::verify();
?>
<header class="nav">
    <?php if(!isset($hidden_back)): ?>
    <div class="options left">
        <a title="Volver" onclick="<?= isset($href) ? "window.location.replace('$href')" : "history.back()" ?>">
            <img class="icon button" id="accordion" src="icons/accordion.svg" style="transform: rotate(90deg);">
        </a>
    </div>
    <?php endif; ?>
    <h1><?= $title ?? "Titulo" ?></h1>
    <div class="options right">
        <a class="button small" target="_blank" href="https://github.com/matius54/el_proyecto_php/">Ver en GitHub</a>
    </div>
</header>