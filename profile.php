<?php
    $title = "Usuario";

    $base = "php/";
    require_once "php/model/user.php";
    require_once "php/libs/template.php";
    require_once "php/libs/utils.php";

    $user_id = User::verify();

    Template::render("header",["title" => $title]);
    Template::render("nav",["title" => $title]);
    
    Template::render("profile", User::get($user_id));
    Template::render("card", [
        "title" => "Opciones",
        "items" => [
            [
                "href" => "./",
                "name" => "Eliminar del sistema"
            ],
            [
                "href" => "./",
                "name" => "Cambiar datos"
            ],
            [
                "href" => "./",
                "name" => "Cambiar contraseña"
            ]
        ]
    ]);
    
    Template::render("footer");
?>