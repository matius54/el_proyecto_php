<?php 
    global $base;
    $base = $base ?? "../php/";
    require_once $base . "libs/template.php";
?>

<div class="card padd">
    <h1><?= $title ?? "" ?></h1>
    <?php foreach($items as $item){
            Template::render("card_btt", $item);
        }
        ?>
</div>