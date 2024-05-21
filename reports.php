<?php
    $title = "Reportes";

    $base = "php/";
    require_once "php/model/user.php";
    require_once "php/libs/template.php";
    Template::render("header",["title" => $title]);
    Template::render("nav",["title" => $title]);
    Template::render("card", [
        "title" => "Reportes",
        "items" => [
            [
                "href" => "./",
                "name" => "Reporte 1"
            ],
            [
                "href" => "./",
                "name" => "Reporte 2"
            ],
            [
                "href" => "./",
                "name" => "reporte 3"
            ]
        ]
    ]);
    //var_dump(User::verify());
    Template::render("footer");
?>