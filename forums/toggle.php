<?php
include('../includes/config.php');

if($_COOKIE['smiley'] == 'true'){
    setcookie('smiley', 'false', time()+60*60*30, '/', '.'.$domain);
}else{
    setcookie('smiley', 'true', time()+60*60*30, '/', '.'.$domain);
}

?>
