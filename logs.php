<?php
    $title = "Registros";

    $base = "php/";
    require_once "php/libs/template.php";
    require_once "php/libs/utils.php";
    require_once "php/model/log.php";

    $level = $_GET["level"] ?? "";
    $type = $_GET["type"] ?? "";

    Template::render("header",["title" => $title]);
    Template::render("navbar",[
        "back" => "./dashboard.php",
        "items" => [
            [
                "name" => "Informacion",
                "icon" => "record",
                "href" => URL::URIquery(["level"=>"info"]),
                "class" => $level === "info" ? "selected" : ""
            ],
            [
                "name" => "Registro",
                "icon" => "record",
                "href" => URL::URIquery(["level"=>"log"]),
                "class" => $level === "log" ? "selected" : ""
            ],
            [
                "name" => "Advertencia",
                "icon" => "record",
                "href" => URL::URIquery(["level"=>"warning"]),
                "class" => $level === "warning" ? "selected" : ""
            ],
            [
                "name" => "Error",
                "icon" => "record",
                "href" => URL::URIquery(["level"=>"error"]),
                "class" => $level === "error" ? "selected" : ""
            ],
            [
                "name" => "A;adir",
                "icon" => "new",
                "href" => URL::URIquery(["type"=>"add"]),
                "last" => true,
                "class" => $type === "add" ? "selected" : ""
            ],
            [
                "name" => "Modificar",
                "icon" => "edit",
                "href" => URL::URIquery(["type"=>"edit"]),
                "class" => $type === "edit" ? "selected" : ""
            ],
            [
                "name" => "Eliminar",
                "icon" => "delete",
                "href" => URL::URIquery(["type"=>"delete"]),
                "class" => $type === "delete" ? "selected" : ""
            ],
            [
                "name" => "Reestablecer",
                "icon" => "delete",
                "href" => URL::baseURI()
            ]
        ]
    ]);
    Template::render("nav",["title" => $title]);
    $all = Logger::getAll($level, $type);
    echo HTML::matrix2table($all->items);
    echo $all->htmlMenu();
    Template::render("footer");
?>