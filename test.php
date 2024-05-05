<?php
    $title = "test";

    $base = "php/";
    require_once "php/model/user.php";
    require_once "php/libs/template.php";
    Template::render("header",["title" => $title]);
    Template::render("nav",["title" => $title]);
    Template::render("card", [
        "title" => "quetal",
        "items" => [
            [
                "href" => "./",
                "name" => "hmm1"
            ],
            [
                "href" => "./",
                "name" => "hmm2"
            ],
            [
                "href" => "./",
                "name" => "hmm3"
            ]
        ]
    ]);
    //var_dump(User::verify());
    Template::render("footer");
?>