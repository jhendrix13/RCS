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

if(!isset($_POST['page_title']) || !isset($_POST['content']))
{
    $content .= '
    <h2>Add Custom Page</h2>
    <form action="addpage.php" method="POST">
    <table>
        <tr><td>Page Title/Name</td><td><input type="text" name="page_title"></td></tr>
        <tr>
            <td>Access Level</td>
            <td>
            <select name="access_level">
                <option value="0">Guest+</option>
                <option value="1">Members+</option>
                <option value="2">PMods+</option>
                <option value="3">FMods+</option>
                <option value="4">Admin Only</option>
            </select>
            </td>
        </tr>
        <tr><td>Parent</td><td><select name="parent">';
    
    //extract existing page cats
    $cats = $database->processQuery("SELECT `id`,`cat_title` FROM `page_categories` ORDER BY `id` ASC", array(), true);
    
    foreach($cats as $cat)
    {
        $content .= '<option value="'. $cat['id'] .'">'. $cat['cat_title'] .'</option>';
    }
    
    $content .= '</select></td></tr>
        <tr><td>Content</td><td><textarea class="ckeditor" name="content"></textarea></td></tr>
        <tr><td>Done?</td><td><input type="submit" value="Add Page"></td></tr>
    </table>
    </form>
    ';
}
else
{
    //save typing time
    $page_title = htmlentities($_POST['page_title']);
    $content = $_POST['content'];
    
    if(strlen($page_title) == 0)
    {
        $content = 'Please make the title is at least one character. <input type="button" class="button" value="Back" onclick="goBack()" />';
    }
    elseif(strlen($content) < 10)
    {
        $content = 'Please include at least ten characters for your new page. <input type="button" class="button" value="Back" onclick="goBack()" />';
    }
    elseif(!in_array($_POST['access_level'], array(0,1,2,3,4)))
    {
        $content = 'Incorrect access level specified. <input type="button" class="button" value="Back" onclick="goBack()" />';
    }
    else
    {
        //add the new category
        $database->processQuery("INSERT INTO `pages` VALUES (null, ?, ?, ?, ?)", array($_POST['parent'], $page_title, $content, $_POST['access_level']), false);
        
        $content = 'The custom page <b>'. $page_title .'</b> has been added. <a href="addpagecat.php">Add another</a>';
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
<script type="text/javascript" src="../js/ckeditor/ckeditor.js"></script>
</head>
	<div id="body">
        <?php $base->getNavBar($username, $path, $rank); ?>
        <br/><br/>
                <div style="text-align: center; background: none;">
                    <div class="titleframe e">
                        <b>Add Custom Page</b><br>
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
