<?php
//echo $role["name"];
echo "<ul class=\"navigator\">";
foreach($roles as $role){
    $selected = false;
    $userRoles_id = array_map(function ($e){return $e["id"] ?? 0;}, $userRoles);
    if(in_array($role["id"], $userRoles_id)) $selected = true;
    ?>
    <li>
        <a <?= $selected ? "class=selected" : "" ?> onclick="window.location.replace('./php/controller/access.php?a=setRole&role_id=<?= $role['id'] ?>&user_id=<?= $id ?>&set=<?= $selected ? '0' : '1' ?>')"><?= $role["name"] ?></a>
    </li>
    <?php
}
echo "</ul>";
?>