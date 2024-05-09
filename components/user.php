<?php 
    global $base;
    $base = $base ?? "../php/";
    require_once $base . "libs/template.php";
    $users = $users ?? [];
?>
<ul class="list">
    <?php foreach($users as $user){
        Template::render("user_li", $user);
    }
    ?>
</ul>