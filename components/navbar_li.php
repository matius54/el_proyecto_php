<?php
    global $base;
    $base = $base ?? "../php/";
    require_once $base . "libs/template.php";
    $icon = $icon ?? "";
    $class = $class ?? [];
    array_push($class, "option");
?>
<li class="<?= implode(" ", $class) ?>">
    <a href="<?= $href ?? "#" ?>">
        <?php Template::render("icon",["icon" => $icon]) ?>
        <span><?= $name ?? "" ?></span>
    </a>
</li>
