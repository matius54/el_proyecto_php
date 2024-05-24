<?php
if(isset($roles) and is_array($roles)){
    $roles = array_map(function ($e){return $e["name"] ?? "";}, $roles);
}else{
    $roles = [];
}
?>
<ul id="<?= $id ?>">
    <li><strong>Nombre de usuario</strong>: <span><?= $user ?></span>.</li>
    <li><strong>Cedula de identidad</strong>: <span><?= $ci ?? "<i>No establecida</i>" ?></span>.</li>
    <li><strong>Nombre</strong>: <span><?= $first_name ?? "<i>No establecido</i>" ?></span>.</li>
    <li><strong>Apellido</strong>: <span><?= $last_name ?? "<i>No establecido</i>" ?></span>.</li>
    <li><strong>Fecha de cumpleaños</strong>: <span><?= $birthday ?? "<i>No establecida</i>" ?></span>.</li>
    <li><strong>Roles</strong>: <span><?= $roles ? implode(", ", $roles) : "<i>Ninguno</i>"?></span>.</li>
</ul>