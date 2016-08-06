<table cellspacing="10">
<tr><td>Click the user you wish to infract.</td></tr>
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
    $found = $database->processQuery("SELECT `username`,`id` FROM `users` WHERE `username` LIKE ? ORDER BY `username` ASC LIMIT 50", array('%'.$_POST['username'].'%'), true);
    foreach($found as $user){
        echo '<tr><td><a name="'. $user['id'] .'"></a>'. $user['username'] .'</td><td><button id="user-'. $user['id'] .'" class="button">Manage user</button></td></tr>';
    }
}
?>
</table>