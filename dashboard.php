<?php
    $title = "Panel de control";
    $base = "php/";
    require_once "php/model/user.php";
    require_once "php/model/access.php";
    require_once "php/libs/template.php";

    
    if(!$user_id = User::verify()) URL::redirect("./login.php");
    if(!Access::test("dashboard.access", $user_id)) die("No tienes acceso para ver esta pagina");
    
    Template::render("header",["title" => $title]);
    Template::render("navbar",[
        "items" => [
            [
                "name" => "Auditoria",
                "icon" => "record",
                "href" => "./logs.php"
            ],
            [
                "name" => "Perfil",
                "icon" => "profile",
                "href" => "#"
            ],
            [
                "name" => "Roles",
                "icon" => "edit",
                "href" => "./role.html"
            ],
            [
                "name" => "Usuarios",
                "icon" => "profile2",
                "href" => "./users.php"
            ],
            [
                "name" => "Reporte",
                "icon" => "pdf",
                "href" => "#"
            ],
            [
                "name" => "Cerrar sesion",
                "icon" => "logout",
                "href" => "php/controller/user.php?a=logout",
                "class" => ["last"]
            ],
        ]
    ]);
    Template::render("nav",["title" => $title]);
    ?>
    <img class="icon fundacite-logo" src="./icons/fundacite_min.svg" id="fundacite">
    <?php
    Template::render("footer");
?>