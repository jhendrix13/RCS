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

if(!isset($_POST['cat_title']))
{
    $content .= '
    <h2>Add Custom Page Category</h2>
    <form action="addpagecat.php" method="POST">
        <table>
            <tr><td>Title of Category</td><td><input type="text" name="cat_title"></td></tr>
            <tr><td>Done</td><td><input type="submit" value="Add"></td></tr>
        </table>
    </form>
    ';
}
else
{
    //save typing time
    $cat_title = htmlentities($_POST['cat_title']);
    
    if(strlen($cat_title) == 0)
    {
        $content = 'Please make the title is at least one character. <input type="button" class="button" value="Back" onclick="goBack()" />';
    }
    else
    {
        //add the new category
        $database->processQuery("INSERT INTO `page_categories` VALUES (null, ?)", array($cat_title), false);
        
        $content = 'The custom page category <b>'. $cat_title .'</b> has been added. <a href="addpage.php">Add a page to it</a> | <a href="addpagecat.php">Add another</a>';
    }
}

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
                        <b>Add Custom Category</b><br>
                        <a href="../index.php" class=c>Main Menu</a> - <a href="index.php">ACP</a>
                    </div>
                </div>

		<img class="widescroll-top" src="../img/scroll/backdrop_765_top.gif" alt="" width="765" height="50" />
		<div class="widescroll">
			<div class="widescroll-bgimg">

				<div class="widescroll-content">
                                    <div style="text-align: justify;color: #402706">
                                        <div id="black_fields">
                                            <?php
                                                echo $content;
                                            ?>
                                        </div>
                                    </div>
				</div>
			</div>
		</div>
		<img class="widescroll-bottom" src="../img/scroll/backdrop_765_bottom.gif" alt="" width="765" height="50" />	

		<div class="tandc"><?php echo $data['wb_foot']; ?></div>
</body>
</html>
