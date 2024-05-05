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
                "name" => "Nuevo usuario",
                "icon" => "new",
                "href" => "./register.php"
            ]
        ]
    ]);
    Template::render("nav",["title" => $title]);
    $users = User::getAll();
    echo HTML::matrix2table($users->items);
    echo $users->htmlMenu();
    Template::render("footer");
?>