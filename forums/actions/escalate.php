<?php
require('../../includes/config.php');
require('../../structure/database.php');
require('../../structure/forum.php');
require('../../structure/forum.thread.php');
require('../../structure/base.php');
require('../../structure/user.php');

$database = new database($db_host, $db_name, $db_user, $db_password);
$thread = new thread($database);
$base = new base($database);
$user = new user($database);
$user->updateLastActive();

//useful variables
$username = $user->getUsername($_COOKIE['user'], 2);
$rank = $user->getRank($username);
$id = $_GET['id'];

//check if already escalated
$escalated = $thread->isEscalated($id);

//take action then log it
if($thread->checkExistence($id) && $thread->canView($id, $username, $rank) && $rank > 2)
{
    if(!$escalated) $thread->escalate($id, $rank, $_GET['reason']); $base->appendToFile('../logs.txt', array($username.' escalated the thread '. $id));
    echo '1';
}else{
    echo $_GET['id'].' * '. $_GET['reason'];
}
?>