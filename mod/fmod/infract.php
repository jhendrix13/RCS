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
$rank = $user->getRank($username);

if($rank < 3) $base->redirect('../../index.php');
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html xmlns:IE>
<head>
<meta http-equiv="Expires" content="0">
<meta http-equiv="Pragma" content="no-cache">
<meta http-equiv="Cache-Control" content="no-cache">
<meta name="MSSmartTagsPreventParsing" content="TRUE">
<title><?php echo $data['wb_title']; ?></title>
<link href="../../css/basic-3.css" rel="stylesheet" type="text/css" media="all">
<link rel="shortcut icon" href="../../img/favicon.ico" />
<?php include('../../includes/google_analytics.html'); ?>
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
                            <b>Moderation</b><br> <a href="../../index.php" class=c>Main Menu</a>  - <a href="index.php">Back</a>
                        </div>
                        </div>

		<img class="widescroll-top" src="../../img/scroll/backdrop_765_top.gif" alt="" width="765" height="50" />
		<div class="widescroll">
			<div class="widescroll-bgimg">
				<div class="widescroll-content">
                                    <div id="black_fields">
                                        <div id="warning" style="display:none;border:2px dotted #8B1A1A;background-color:#FF3030;color:white;margin-left:auto;margin-right:auto;text-align:center;">
                                            
                                        </div>
                                        Username: <input id="username" type="text" name="username" autocomplete="off" class="button" value="<?php if(isset($_GET['target'])) echo htmlentities($_GET['target']); ?>"><label for="username"></label>&nbsp; | &nbsp;<button id="recent" class="button">View recent infractions</button>
                                        <hr>
                                        <div id="results">
                                            
                                        </div>
                                    </div>
				</div>
			</div>
		</div>
		<img class="widescroll-bottom" src="../../img/scroll/backdrop_765_bottom.gif" alt="" width="765" height="50" />	
		<div class="tandc"><?php echo $data['wb_foot']; ?></div>
                <script type="text/javascript" src="../../js/jquery.js"></script>
                <script type="text/javascript" src="../../js/infract.js"></script>
</body>
</html>
