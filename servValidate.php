<?php
/*
 * for those of you who wish to use RCS accounts as server logins,
 * you can use this page
 */

if(isset($_GET['name']) && isset($_GET['pass'])){
    require('includes/config.php');
    require('structure/database.php');

    $database = new database($db_host, $db_name, $db_user, $db_password);
    $data = $database->processQuery("SELECT `password` FROM `users` WHERE `username` = ? LIMIT 1", array($_GET['name']), true);
    
    $password = hash(sha256, md5(sha1($_GET['pass'])));
    $db_password = substr(substr($data[0]['password'], 54), 0, -3);
    
    echo ($password == $db_password) ? '1' : '0';
}else{
    echo '0';
}

?>
