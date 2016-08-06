<?php 
include('includes/config.php');
include('structure/database.php'); 
include('structure/base.php');
include('structure/user.php');

$database = new database($db_host, $db_name, $db_user, $db_password);
$base = new base($database);
$user = new user($database);

//set some basic vars
$username = $user->getUsername($_COOKIE['user'], 2);
$rank = $user->getRank($username);
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html xmlns:IE>

<head>
<meta http-equiv="Expires" content="0">
<meta http-equiv="Pragma" content="no-cache">
<meta http-equiv="Cache-Control" content="no-cache">
<meta name="MSSmartTagsPreventParsing" content="TRUE">
<title><?php echo $data['wb_title']; ?></title>
<link href="css/basic-3.css" rel="stylesheet" type="text/css" media="all">
<link href="css/main/title-5.css" rel="stylesheet" type="text/css" media="all">
<link href="css/kbase-2.css" rel="stylesheet" type="text/css" media="all" />
<style>
.donorbox
{
width:550px;
text-align:left;
}

#rbox_r
{
	width:550px;
	border:1.5px solid #900000;
	background-color:#500000;
	padding:5px;
} 
</style>
<?php include('includes/google_analytics.html'); ?>
</head>

		<div id="body">
                <?php $base->getNavBar($username, $path, $rank); ?>
                <br/><br/>
		<div style="text-align: center; background: none;">
				<div class="titleframe e">
				<b>Donate</b><br>
				<a href="index.php" class=c>Main Menu</a>
				</div>
			</div>

			
			<img class="widescroll-top" src="img/scroll/backdrop_765_top.gif" alt="" width="765" height="50" />
			<div class="widescroll">
			<div class="widescroll-bgimg">
			<div class="widescroll-content">
			<center>
			<div class="donorbox">
			<div id="rbox_r"><font color="white">We do not force or require you to donate. It is optional and out of your own free will.</font></div>
			<br/>
			Donating any amount of money to support the <?php echo $data['wb_name']; ?> project is a wonderful thing. As of right now, the money to pay for hosting is coming out of the developers' pockets, so any sort of donation is a big weight lifted off our backs. <b>Donating is NOT required. If someone donates, it's their own decision.</b>
			<br/><br/>
			<font size="1">When donating, you should automatically receive your donor status.</font>
			</div>
			<br/>
			<br/>
                        <form action="https://www.paypal.com/cgi-bin/webscr" method="post">
                        <input type="hidden" name="cmd" value="_s-xclick">
                        <input type="hidden" name="business" value="jaw1788@comcast.net">
                        <input type="hidden" name="return" value="http://www.runecentre.com/donation_success.php">
                        <input type="hidden" name="notify_url" value="http://runecentre.com/structure/ipn/ipn.php">
                        <table>
                        <tr><td>Username</td><td><input type="text" name="custom"></td></tr>
                        <tr><td><input type="hidden" name="on0" value="Donation Amount">Donation Amount</td><td><select name="os0">
                        <option value="Donator">Donator $2.00 USD</option>
                        <option value="Donator2">Donator2 $5.00 USD</option>
                        <option value="Donator3">Donator3 $10.00 USD</option>
                        <option value="Donator4">Donator4 $15.00 USD</option>
                        <option value="Donator5">Donator5 $20.00 USD</option>
                        <option value="Donator6">Donator6 $50.00 USD</option>
                        </select> </td></tr>
                        </table>
                        <input type="hidden" name="currency_code" value="USD">
                        <input type="hidden" name="encrypted" value="-----BEGIN PKCS7-----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-----END PKCS7-----">
                        <input type="image" src="https://www.paypalobjects.com/en_US/i/btn/btn_donateCC_LG.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">
                        <img alt="" border="0" src="https://www.paypalobjects.com/en_US/i/scr/pixel.gif" width="1" height="1">
                        </form>
			<br/>
			</center>
			<div style="clear: both;"></div>
			</div>
			</div>
			</div>
			<img class="widescroll-bottom" src="img/scroll/backdrop_765_bottom.gif" alt="" width="765" height="50" />
			<div class="tandc"><?php echo $data['wb_foot']; ?></div>
	</div>
	</body>
</html>