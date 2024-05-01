<?php
    global $base;
    $base = $base ?? "../php/";
    require_once $base . "libs/template.php";
    $icon = $icon ?? "";
?>
<li class="<?= $last ?? false ? "last" : "option" ?><?= " ".$class ?? "" ?>">
    <a href="<?= $href ?? "#" ?>">
        <?php Template::render("icon",["icon" => $icon]) ?>
        <span><?= $name ?? "" ?></span>
    </a>
</li>
