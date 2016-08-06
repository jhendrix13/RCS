<?php
require('../includes/config.php');
require('../structure/database.php');
require('../structure/base.php');
require('../structure/user.php');

$database = new database($db_host, $db_name, $db_user, $db_password);
$base = new base($database);
$user = new user($database);
$user->updateLastActive();

$username = $user->getUsername($_COOKIE['user'], 2);
$rank = $user->getRank($username);

if($rank < 4) $base->redirect('../index.php');


?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html xmlns:IE>
<head>
<meta http-equiv="Expires" content="0">
<meta http-equiv="Pragma" content="no-cache">
<meta http-equiv="Cache-Control" content="no-cache">
<meta name="MSSmartTagsPreventParsing" content="TRUE">
<title><?php echo $data['wb_title']; ?></title>
<link href="../css/basic-3.css" rel="stylesheet" type="text/css" media="all">
<link rel="shortcut icon" href="../img/favicon.ico" />
<?php include('../includes/google_analytics.html'); ?>
<script type="text/javascript">
function goBack()
{
	window.history.back();
}	
</script>
</head>
	<div id="body">
        <?php $base->getNavBar($username, $path, $rank); ?>
        <br/><br/>
                <div style="text-align: center; background: none;">
                <div class="titleframe e">
                    <b>Escalated Threads</b><br>
                    <a href="../index.php" class=c>Main Menu</a> - <a href="index.php">Back</a>
                </div>
                </div>

		<img class="widescroll-top" src="../img/scroll/backdrop_765_top.gif" alt="" width="765" height="50" />
		<div class="widescroll">
			<div class="widescroll-bgimg">

				<div class="widescroll-content">
                                    <div id="black_fields">
                                        <?php
                                            $threads = $database->processQuery("SELECT `id`,`parent`,`title`,`reason` FROM `threads` WHERE `escalated` = 1 ORDER BY `date` ASC", array(), true);
                                            $num = $database->getRowCount();
                                        ?>
                                        
                                        <center><h2>Escalated Threads (<?php echo $num; ?>)</h2>
                                        
                                        <?php
                                            if(isset($_POST['solved']))
                                            {
                                                $i = 0;
                                                foreach($_POST['solved'] as $t)
                                                {
                                                    $i++;
                                                    $database->processQuery("UPDATE `threads` SET `escalated` = 0, `reason` = '' WHERE `id` = ? LIMIT 1", array($t), false);
                                                }
                                                
                                                echo '<font color="red">Marked '. $i .' thread(s) as solved.</font><br/><br/><a href="viewescalated.php">Back</a>';
                                            }
                                            else
                                            {
                                                if($num == 0)
                                                {
                                                    echo 'No threads have been escalated.';
                                                }
                                                else
                                                {
                                                    ?>
                                            
                                                    <form action="viewescalated.php" method="POST">
                                                    <table cellspacing="4" cellpadding="4">
                                                    <tr><th>&nbsp;</th><th>Title</th><th>Reason</th><th>View</th></tr>
                                                    <?php
                                                        foreach($threads as $thread)
                                                        {
                                                            ?>

                                                            <tr><td><input type="checkbox" name="solved[]" value="<?php echo $thread['id']; ?>"></td><td><?php echo htmlentities($thread['title']); ?></td><td><?php echo htmlentities($thread['reason']); ?></td><td><a href="../forums/viewthread.php?forum=<?php echo $thread['parent']; ?>&id=<?php echo $thread['id']; ?>&goto=start" target="_blank">Click to view thread...</a></td></tr>

                                                            <?php
                                                        }
                                                    ?>
                                                    <tr><td><input type="submit" value="Mark as Solved" class="button"></td></tr>
                                                    </table>
                                                    </form>
                                            
                                                    <?php
                                                }
                                            }
                                        ?>
                                        </center>
                                    </div>
				</div>
			</div>
		</div>
		<img class="widescroll-bottom" src="../img/scroll/backdrop_765_bottom.gif" alt="" width="765" height="50" />	

		<div class="tandc"><?php echo $data['wb_foot']; ?></div>
</body>
<script type="text/javascript" src="../js/jquery.js"></script>
<script type="text/javascript" src="../js/addcat.js"></script>
</html>
