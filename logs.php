<?php
    $title = "Registros";

    $base = "php/";
    require_once "php/libs/template.php";
    require_once "php/libs/utils.php";
    require_once "php/model/log.php";

    Template::render("header",["title" => $title]);
    Template::render("navbar",[
        "back" => "./dashboard.php",
        "items" => [
            [
                "name" => "Reestablecer",
                "icon" => "delete",
                "href" => URL::baseURI(),
                "class" => ""
            ],
            [
                "name" => "Informacion",
                "icon" => "record",
                "href" => URL::URIquery(["level"=>"info"]),
                "class" => ""
            ],
            [
                "name" => "Registro",
                "icon" => "record",
                "href" => URL::URIquery(["level"=>"log"]),
                "class" => ""
            ],
            [
                "name" => "Advertencia",
                "icon" => "record",
                "href" => URL::URIquery(["level"=>"warning"]),
                "class" => ""
            ],
            [
                "name" => "Error",
                "icon" => "record",
                "href" => URL::URIquery(["level"=>"error"]),
                "class" => ""
            ],
            [
                "name" => "A;adir",
                "icon" => "new",
                "href" => URL::URIquery(["type"=>"add"]),
                "last" => true,
                "class" => ""
            ],
            [
                "name" => "Modificar",
                "icon" => "edit",
                "href" => URL::URIquery(["type"=>"edit"]),
                "class" => ""
            ],
            [
                "name" => "Eliminar",
                "icon" => "delete",
                "href" => URL::URIquery(["type"=>"delete"]),
                "class" => ""
            ]
        ]
    ]);
    Template::render("nav",["title" => $title]);
    $all = Logger::getAll();
    echo HTML::matrix2table($all->items);
    echo $all->htmlMenu();
    Template::render("footer");
?>