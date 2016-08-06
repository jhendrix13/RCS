<?php
require('../includes/config.php');
require('../structure/database.php');
require('../structure/base.php');
require('../structure/user.php');
require('../structure/forum.php');

$database = new database($db_host, $db_name, $db_user, $db_password);
$base = new base($database);
$user = new user($database);
$forum = new forum($database);
$user->updateLastActive();

$username = $user->getUsername($_COOKIE['user'], 2);
$rank = $user->getRank($username);

if($rank > 3){
    $database->processQuery("UPDATE `users` SET `signature` = ? WHERE `username` = ? LIMIT 1", array(nl2br($_POST['signature']), $username), false);
}
?>