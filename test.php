<?php
    $title = "test";

    $base = "php/";
    require_once "php/model/user.php";
    require_once "php/libs/template.php";
    Template::render("header",["title" => $title]);
    Template::render("nav",["title" => $title]);
    var_dump(User::verify());
    Template::render("footer");
?>