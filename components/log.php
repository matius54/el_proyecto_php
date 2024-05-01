<?php 
    global $base;
    $base = $base ?? "../php/";
    require_once $base . "libs/template.php";
    $items = $items ?? [];
?>
<nav class="log">
    <ul>
        <li class="selected">
            <a href="./" class="logo" title="volver a la pagina principal" name="logo">
                <h3 class="icon">TCS CRM WEB</h3>
                <span>Pagina principal</span>
            </a>
        </li>
        <?php foreach($items as $item){
            Template::render("navbar_li", $item);
        }
        ?>
    </ul>
</nav>