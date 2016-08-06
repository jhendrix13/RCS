<?php
require('../../includes/config.php');
require('../../structure/database.php');
require('../../structure/base.php');
require('../../structure/user.php');

$database = new database($db_host, $db_name, $db_user, $db_password);
$base = new base($database);
$user = new user($database);
$user->updateLastActive();

if($user->getRank($user->getUsername($_COOKIE['user'], 2)) > 2){
    $database->processQuery("DELETE FROM `infractions` WHERE `id` = ? LIMIT 1", array($_POST['id']), false);
    if($database->getRowCount() == 1){
        echo '1';
    }else{
        echo '0';
    }
}
?>