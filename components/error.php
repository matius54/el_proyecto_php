<?php
    global $base;
    $base = $base ?? "../php/";
    require_once $base . "libs/utils.php";
    $error_key = "error";
    SESSION::start();
    $text = $_SESSION[$error_key] ?? "";
    if($text){
        unset($_SESSION[$error_key]);
?>
<div class="card" style="background-color: red;">
    <h2>Ha ocurrido un error</h2>
    <p><?= $text ?></p>
</div>
<?php
    }
?>