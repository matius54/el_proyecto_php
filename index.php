<?php
    $base = "php/";
    require_once "php/model/user.php";
    require_once "php/model/access.php";
    require_once "php/libs/utils.php";
    $user_id = User::verify();
    if(!$user_id) URL::redirect("./login.php");
    if(Access::test("dashboard.access", $user_id)) URL::redirect("./dashboard.php");
    if(Access::test("dashboard.access", $user_id));
    User::logout();
?>