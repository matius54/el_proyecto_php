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
    echo "<main class='viewer'>";
    ?>
    <form>
        <input type="text" placeholder="buscar" name="search">
        <button class="button">Buscar</button>
    </form>
    <?php
    $search = URL::decode("search");
    if($search){
        $users = User::search($search);
    }else{
        $users = User::getAll();
    }
    Template::render("user",["users"=>$users->items]);
    echo $users->htmlMenu();
    echo "</main>";
    Template::render("footer");
?>
<script src="./frontend_libs/list.js"></script>