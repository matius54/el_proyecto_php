<?php
    $title = "Registrar entrada o salida";

    $base = "php/";
    require_once "php/model/user.php";
    require_once "php/model/crm.php";
    require_once "php/libs/template.php";
    Template::render("header",["title" => $title]);
    Template::render("nav",["title" => $title, "href" => "./users.php"]);
    $id = intval($_GET["id"] ?? "0");
    $user = User::get($id);

    if(Event::access($id)){
        $isOut = Event::isOut($id);
    }else{
        $isOut = true;
    }

    //Event::register($id, true);
    Template::render("card", [
        "title" => "Confirmar",
        "items" => [
            [
                "name" => "Registrar " . ($isOut ? "entrada" : "salida"),
                "href" => "./php/controller/crm.php".URL::query(["a"=>"enter","exit" => $isOut ? "0" : "1"], keep: true)
            ]
        ]
    ]);
    
    //var_dump(User::verify());
    Template::render("footer");
?>