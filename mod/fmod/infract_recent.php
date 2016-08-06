<table  cellspacing="10">
<tr><th>ID</th><th>Worth</th><th>Issued To</th><th>Issued By</th><th>Issued On</th><th>Expires</th><th>Reason</th></tr>
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
    $infractions = $database->processQuery("SELECT `id`,`userid`,`expiration`,`issued_by`,`date`,`worth`,`reason` FROM `infractions` WHERE `expiration` >= CURRENT_DATE() ORDER BY `id` DESC LIMIT 25", array(), true);
    
    if($database->getRowCount() > 0){
        foreach($infractions as $inf){
            echo '<tr id="'. $inf['id'] .'"><td>'. $inf['id'] .'</td><td>'. $inf['worth'] .'</td><td>'. htmlentities($user->getNameById($inf['userid'])) .'</td><td>'. htmlentities($inf['issued_by']) .'</td><td>'. $inf['date'] .'</td><td>'. $inf['expiration'] .'</td><td>'. stripslashes(htmlentities($inf['reason'])) .'</td><td><button id="delete-'. $inf['id'] .'" class="button">Delete</button></td></tr>';
        }
    }
}
?>
</table>