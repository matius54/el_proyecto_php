<?php
    $title = "Usuarios";
    $base = "php/";
    require_once "php/model/user.php";
    require_once "php/libs/template.php";

    $user_id = User::verify();
    
    Template::render("header",["title" => $title]);
    Template::render("navbar",[
        "items" => [
            [
                "name" => "Registros",
                "icon" => "record",
                "href" => "./logs.php"
            ],
            [
                "name" => "Cerrar sesion",
                "icon" => "logout",
                "href" => "#",
                "last" => true
            ],
        ]
    ]);
    Template::render("nav",["title" => $title]);
    Template::render("footer");
?>