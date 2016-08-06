<?php
require('includes/config.php');
require('structure/database.php');
require('structure/user.php');

$database = new database($db_host, $db_name, $db_user, $db_password);
$user = new user($database);
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html xmlns:IE>

<head>
<meta http-equiv="Expires" content="0">
<meta http-equiv="Pragma" content="no-cache">
<meta http-equiv="Cache-Control" content="no-cache">
<meta name="MSSmartTagsPreventParsing" content="TRUE">
<title>Upgrade RCS</title>
<link href="css/basic-3.css" rel="stylesheet" type="text/css" media="all">
<link href="css/main/title-5.css" rel="stylesheet" type="text/css" media="all">
<link href="css/kbase-2.css" rel="stylesheet" type="text/css" media="all" />
<link rel="shortcut icon" href="img/favicon.ico" />
</head>

		<div id="body">
		<div style="text-align: center; background: none;">
				<div class="titleframe e">
					<b>Upgrade RuneScape Community Script</b>
				</div>
			</div>

			
			<img class="widescroll-top" src="img/scroll/backdrop_765_top.gif" alt="" width="765" height="50" />
			<div class="widescroll">
			<div class="widescroll-bgimg">
			<div class="widescroll-content">
                            <div style="margin-left:auto;margin-right:auto;text-align:left;width:325px;">
                            <h2>Upgrade</h2>
                            <?php
                                if(!isset($_GET['upgrade']))
                                {
                                    echo 'Are you sure you wish to upgrade? <a href="?upgrade=1">Yes!</a>';
                                }
                                else
                                {
                                    $file = 'includes/config.php';
                                    $data = file_get_contents($file);

                                    //write data
                                    $written = str_replace('1.3', '1.4', $data);

                                    $f = fopen($file, 'w');
                                    fwrite($f, $written);
                                    fclose($f);
                                    
                                    $database->processQuery("ALTER TABLE  `config` ADD  `bbcode_members` INT NOT NULL AFTER  `reportforum` ,
                                    ADD  `play_url` TINYTEXT NOT NULL AFTER  `bbcode_members`", array(), false);
                                    $database->processQuery("ALTER TABLE `users` DROP `messages`", array(), false);
                                    $database->processQuery("ALTER TABLE  `users` ADD  `lastip` VARCHAR( 15 ) NOT NULL", array(), false);
                                    $database->processQuery("ALTER TABLE  `threads` ADD  `escalated` INT NOT NULL AFTER  `autohiding`", array(), false);
                                    $database->processQuery("CREATE TABLE IF NOT EXISTS `pages` (
                                    `id` int(11) NOT NULL AUTO_INCREMENT,
                                    `parent` int(11) NOT NULL,
                                    `page_title` varchar(40) NOT NULL,
                                    `content` text NOT NULL,
                                    `access_level` int(11) NOT NULL,
                                    PRIMARY KEY (`id`)
                                    ) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=4 ;", array(), false);
                                    $database->processQuery("CREATE TABLE IF NOT EXISTS `page_categories` (
                                    `id` int(11) NOT NULL,
                                    `cat_title` varchar(40) NOT NULL
                                    ) ENGINE=InnoDB DEFAULT CHARSET=latin1;", array(), false);
                                    
                                    echo 'Upgrade successful! Now deleting this file. <a href="index.php">Return home...</a>';
                                    
                                    //write site URL to official RCS upgraded list
                                    $ch = curl_init('http://rcscript.net/add.php?url='. $path);
                                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                                    curl_exec($ch);
                                    curl_close($ch);
                                    
                                    unlink('upgrade.php');
                                }
                            ?>
                            </div>
			<div style="clear: both;"></div>
			</div>
			</div>
			</div>
			<img class="widescroll-bottom" src="img/scroll/backdrop_765_bottom.gif" alt="" width="765" height="50" />
		<div class="tandc">This website and its contents are copyright &copy; 1999 - 2007 Jagex Ltd.<br/>
Use of this website is subject to our Terms+Conditions and Privacy policy<br/>Powered by RuneScape Community Script (RCS)</div>
	</div>
	</body>
</html>