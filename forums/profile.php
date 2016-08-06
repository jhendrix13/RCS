<?php
require('../includes/config.php');
require('../structure/database.php');
require('../structure/base.php');
require('../structure/forum.php');
require('../structure/forum.thread.php');
require('../structure/user.php');

$database = new database($db_host, $db_name, $db_user, $db_password);
$base = new base($database);
$user = new user($database);
$forum = new forum($database);
$forum_thread = new thread($database);

$user->updateLastActive();

$username = $user->getUsername($_COOKIE['user'], 2);
$rank = $user->getRank($username);

if(isset($_GET['username'])){
    if($user->doesExist($_GET['username'])){
        $user_s = $_GET['username'];
    }
}else{
    if(!$user->isLoggedIn()){
        $base->redirect('../login.php');
    }else{
        $user_s = $username;
    }
}

//retrieve posts/threads
$posts = $database->processQuery("SELECT `id`,`thread`,`timestamp` FROM `posts` WHERE `username` = ? AND ". time() ." - `timestamp` < 1209600 ORDER BY `id` DESC", array($user_s), true);
$threads = $database->processQuery("SELECT `id`,`parent`,`timestamp` FROM `threads` WHERE `username` = ? AND ". time() ." - `timestamp` < 1209600 ORDER BY `id` DESC", array($user_s), true);

//type:id:forum:timestamp:(if post)thread
$list = array();

foreach($posts as $post){
    //get the thread's forum/parent
    $t = $database->processQuery("SELECT `parent` FROM `threads` WHERE `id` = ? LIMIT 1", array($post['thread']), true);
    
    $list[$post['timestamp']] = 'p:'.$post['id'].':'. $t[0]['parent'] .':'.$post['timestamp'].':'.$post['thread'];
}

//add threads
foreach($threads as $thread){
    $list[$thread['timestamp']] = 't:'.$thread['id'].':'.$thread['parent'].':'.$thread['timestamp'];
}

//now sort them
krsort($list, SORT_NUMERIC);

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html xmlns:IE>
<head>
<meta http-equiv="Expires" content="0">
<meta http-equiv="Pragma" content="no-cache">
<meta http-equiv="Cache-Control" content="no-cache">
<meta name="MSSmartTagsPreventParsing" content="TRUE">
<title><?php echo $data['wb_title']; ?></title>
<link href="../css/basic-3.css" rel="stylesheet" type="text/css" media="all" />
<link href="../css/forum-3.css" rel="stylesheet" type="text/css" media="all" />
<link href="../css/forummsg-1.css" rel="stylesheet" type="text/css" media="all" />
<link rel="shortcut icon" href="../img/favicon.ico" />
<!--[if IE 8]>
<link rel="stylesheet" type="text/css" href="../css/forummsg-ie-1.css" />
<![endif]-->
<?php
include('../includes/google_analytics.html');
?>
</head>
		<div id="body">
		<?php $base->getNavBar($username, $path, $rank); ?>
                <br /><br/>
		<div style="text-align: center; background: none;">
                        <div class="titleframe e">
                                <b>User Profile</b><br><a href="../index.php">Main Menu</a><br />
                        </div>
                </div>
                <br/><br/>
		<div class="frame e" style="overflow:auto;"><br>
		<center><font size="4">User: <?php echo htmlentities($user_s); ?></font></center>
		<br />
                <div id="divone1"><div id="divone2" style="width:717px; height:100px; border:2px solid #382418;"><br>&nbsp;This user has made <b><?php echo $user->postcount($user_s); ?></b> posts.
                    <?php
                        if($user_s == $username){
                            ?>
                        <br/><br/><input type="submit" id='smilies' value="Switch <?php echo ($user->smileyC()) ? 'off' : 'on'; ?> smilies" /><br />
                            <?php
                        }
                    ?>
                </div></div>
                <br />
		<div id="divone"><b>Threads posted in by <?php echo htmlentities($user_s); ?></b></div>
		<center>
		<table RULES=NONE FRAME=BOX style="border: 2px solid #382418; background-color:transparent" width="730" cellpadding="3" cellspacing="3">
                <tr>
                        <td><b>Status</b></td>
                        <td><b>Thread title</b></td>
                        <td><b>Forum</b></td>
                        <td><b>Last posted at</b></td>
                        <td><b>Posts</b></td>
                </tr>
                <?php
                    foreach($list as $item){
                        $details = explode(':', $item);
                        $thread_id = ($details[0] == 'p') ? $details[4] : $details[1];
                        
                        //get thread title
                        $x = $database->processQuery("SELECT `title` FROM `threads` WHERE `id` = ?", array($thread_id), true);
                        
                        //get the forum name
                        $f = $database->processQuery("SELECT `title`,`type` FROM `forums` WHERE `id` = ?", array($details[2]), true);
                        
                        //save time
                        $base = 'viewthread.php?forum='. $details[2] .'&id='. $thread_id;
                        if($f[0]['type'] == 4 || $f[0]['type'] == 5){
                            if($rank > 2){
                                ?>
                                    <tr>
                                        <td><?php echo $forum_thread->preTitle($thread_id, $rank); ?></td>
                                        <td><a href="<?php echo $base; ?>"><?php echo htmlentities(stripslashes($x[0]['title'])); ?></a></td>
                                        <td><a href="viewforum.php?forum=<?php echo $details[2]; ?>"><?php echo $f[0]['title']; ?></a></td>
                                        <td><?php echo date('Y-m-d h:m:s', $details[3]); ?></td>
                                        <td><a href="<?php echo $base.(($details[0] == 'p') ? '&highlight='. $details[1] .'#'. $details[1] : '&goto=start'); ?>">Show</a></td>
                                    </tr>
                                <?php
                            }
                        }else{
                            ?>
                                <tr>
                                    <td><?php echo $forum_thread->preTitle($thread_id, $rank); ?></td>
                                    <td><a href="<?php echo $base; ?>"><?php echo htmlentities(stripslashes($x[0]['title'])); ?></a></td>
                                    <td><a href="viewforum.php?forum=<?php echo $details[2]; ?>"><?php echo $f[0]['title']; ?></a></td>
                                    <td><?php echo date('Y-m-d h:m:s', $details[3]); ?></td>
                                    <td><a href="<?php echo $base.(($details[0] == 'p') ? '&highlight='. $details[1] .'#'. $details[1] : '&goto=start'); ?>">Show</a></td>
                                </tr>
                            <?php
                        }
                    }
                ?>
	</table>
<br />
<a href="index.php">Back to forums</a>
</center><br></div></div>

        <div class="tandc"><?php echo $data['wb_foot']; ?></div>
        <script type="text/javascript" src="../js/jquery.js"></script>
        <script type="text/javascript">
            $(document).ready(function(){
                var isOn = <?php echo ($user->smileyC()) ? 1 : 0; ?>;
                
                $('#smilies').click(function(){
                    $.ajax({
                        url: 'toggle.php'
                    }).done(function(){
                        if(isOn == 1){
                            isOn = 0;
                            $('#smilies').val('Switch on smilies');
                        }else{
                            isOn = 1;
                            $('#smilies').val('Switch off smilies');
                        }
                    });
                });
            });
        </script>
	</body>

</html>