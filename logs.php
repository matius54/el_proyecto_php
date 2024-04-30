<?php
    $title = "Registros";

    $base = "php/";
    require_once "php/libs/template.php";
    require_once "php/libs/utils.php";
    require_once "php/model/log.php";

    Template::render("header",["title" => $title]);
    Template::render("nav",["title" => $title]);
    $all = Logger::getAll();
    echo HTML::matrix2table($all->items);
    echo $all->htmlMenu();
    Template::render("footer");
?>