<?php
require('../../includes/config.php');
require('../../structure/database.php');
require('../../structure/base.php');
require('../../structure/user.php');

$database = new database($db_host, $db_name, $db_user, $db_password);
$base = new base($database);
$user = new user($database);
$user->updateLastActive();

$username = $user->getUsername($_COOKIE['user'], 2);

if($user->getRank($username) > 2){
    if(isset($_POST['id']) && isset($_POST['worth']) && isset($_POST['reason']) && isset($_POST['length'])){
        $target = $user->getNameById($_POST['id']);
        if(!($user->getRank($target) > 1)){
            $length = explode('-', $_POST['length']);
            if(ctype_digit($length[0]) && ctype_digit($length[1])  && ctype_digit($length[2]) && ctype_digit($length[3]) && ($_POST['worth'] >= 0 && $_POST['worth'] <= 10)){
                $message = 'Dear '. $target .', <br/><br/>I have added an infraction <i>(worth '. $_POST['worth'] .' points)</i> to your account for the following reason: <br/><br/><b>'. $_POST['reason'] .'</b>. <br/><br/>If your account receives enough infractions to put your account over the limit of 10 infraction points, you will be automatically banned until your infractions expire to bring you back under 10. Please read the rules and be sure to stay out of trouble.<br/><br/>If you feel this is a mistake, you can create a message in the message centre and wait for an admin\'s response.<br/><br/>Thanks,<br/>'. $username .'.';
                $database->processQuery("INSERT INTO `messages` VALUES (null, ?, ?, ?, ?, ?, NOW(), '0', '0', ?, ?)", array($username, $target, 'You have received an infraction.', $message, $_SERVER['REMOTE_ADDR'], $username, time()), false);
                $database->processQuery("INSERT INTO `infractions` VALUES (null, ?, ?, ?, CURRENT_DATE() + INTERVAL {$length[0]} YEAR + INTERVAL {$length[1]} MONTH + INTERVAL {$length[2]} WEEK + INTERVAL {$length[3]} DAY, ?, CURRENT_DATE())", array($_POST['id'], $_POST['worth'], nl2br($_POST['reason']), $username), false);
                if($database->getRowCount() > 0){
                    $inf = $database->processQuery("SELECT `id`,`expiration`,`issued_by`,`date`,`worth`,`reason` FROM `infractions` WHERE `id` = ? LIMIT 1", array($database->getInsertId()), true);
                    echo '<tr id="'. $inf[0]['id'] .'"><td>'. $inf[0]['id'] .'</td><td>'. $inf[0]['worth'] .'</td><td>'. htmlentities($inf[0]['issued_by']) .'</td><td>'. $inf[0]['date'] .'</td><td>'. $inf[0]['expiration'] .'</td><td>'. htmlentities($inf[0]['reason']) .'</td><td><button id="delete-'. $inf[0]['id'] .'" class="button">Delete</button></td></tr>'; 
                }else{
                    echo 'fail';
                }
            }
        }else{
            echo 'staff';
        }
    }
}
?>