<?php
    $title = "Usuarios y roles";
    $base = "php/";
    require_once "php/model/user.php";
    require_once "php/model/access.php";
    require_once "php/libs/template.php";
    require_once "php/libs/utils.php";

    $user_id = intval(URL::decode("id") ?? "0");
    if(!$user_id) $user_id = User::verify();
    if(!$user_id) URL::redirect("./");

    Template::render("header",["title" => $title]);
    Template::render("nav",["title" => $title, "href" => "./users.php"]);

    $roles = Role::getAll_u();
    //echo HTML::matrix2table($roles);

    $user = User::get($user_id);
    if($user){
        Template::render("profile", $user);
        Template::render("roleSelect", ["id" => $user_id, "roles" => $roles, "userRoles"=> $user["roles"]]);
    }else{
        echo "<i>Usuario no encontrado</i>";
    }

    Template::render("footer");
?>
<script src="./frontend_libs/list.js"></script>