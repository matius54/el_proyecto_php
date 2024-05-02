<?php
    //global $base;
    //$base = $base ?? "../php/";
    //require_once $base . "model/user.php";

    //$user_id = User::verify();
?>
<header class="nav">
    <div class="options left">
        <?php if(isset($user_id)): ?>
        <a title="Cerrar sesion" href="<?= $base ?>/controller/user.php?a=logout">
            <img class="icon button" id="logout" src="icons/logout.svg">
        </a>
        <?php endif; ?>
    </div>
    <h1><?= $title ?? "Titulo" ?></h1>
    <div class="options right">

    </div>
</header>