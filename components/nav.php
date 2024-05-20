<?php
    //global $base;
    //$base = $base ?? "../php/";
    //require_once $base . "model/user.php";

    //$user_id = User::verify();
?>
<header class="nav">
    <div class="options left">
        <a title="Volver" onclick="history.back()">
            <img class="icon button" id="accordion" src="icons/accordion.svg" style="transform: rotate(90deg);">
        </a>
    </div>
    <h1><?= $title ?? "Titulo" ?></h1>
    <div class="options right">
        <a class="button small" target="_blank" href="https://github.com/matius54/el_proyecto_php/tree/31a1a2f0a11724aac4021bc0654ceda10d71e26d">Ver en GitHub</a>
    </div>
</header>