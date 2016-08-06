<?php
require('includes/config.php');
require('structure/database.php');
require('structure/base.php');
require('structure/user.php');

$database = new database($db_host, $db_name, $db_user, $db_password);
$base = new base($database);
$user = new user($database);

//get config settings from db
$config = $base->loadConfig();

//set some basic vars
$username = $user->getUsername($_COOKIE['user'], 2);
$rank = $user->getRank($username);

$user->updateLastActive();

if(!$user->isLoggedIn()){
    $base->redirect('login.php');
}else{
    //get their infraction information
    $infractions = $database->processQuery("SELECT `worth`,`reason`,`expiration`,`date`,`issued_by` FROM `infractions` WHERE `userid` = ? AND (`expiration` >= CURRENT_DATE()) ORDER BY `id` DESC", array($user->getIdByName($username)), true);
    $num_infractions = $database->getRowCount();
    
    //their infraction "points level"
    $points = 0;
    
    foreach($infractions as $infraction){
        $points += $infraction['worth'];
        
        $i .= '';
    }
    
    if($points >= 7){
        $zone = 'redzone';
    }elseif($points >= 3){
        $zone = 'yellowzone';
    }else{
        $zone = 'greenzone';
    }
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html xmlns:IE>

<!-- LeeStrong Runescape Website Source --!>
<!-- Added by HTTrack --><meta http-equiv="content-type" content="text/html;charset=ISO-8859-1"><!-- /Added by HTTrack -->
<head>
<meta http-equiv="Expires" content="0">
<meta http-equiv="Pragma" content="no-cache">
<meta http-equiv="Cache-Control" content="no-cache">
<meta name="MSSmartTagsPreventParsing" content="TRUE">
<title><?php echo $data['wb_title']; ?></title>
<link href="css/basic-3.css" rel="stylesheet" type="text/css" media="all">
<link href="css/main/title-5.css" rel="stylesheet" type="text/css" media="all">
<link href="css/kbase/kbase-2.css" rel="stylesheet" type="text/css" media="all" />
<link rel="shortcut icon" href="img/favicon.ico" />
<?php include('includes/google_analytics.html'); ?>
</head>
<style type="text/css">
div.questionbox {
	text-align: left;
	margin-bottom: 10px;
}

div.questiontext {
	border: medium none;
	background: transparent none repeat scroll 0pt 0pt;
	color: rgb(219, 198, 143);
	font-size: 12px;
	padding-left: 5px;
	padding-right: 5px;
	padding-top: 3px;
}

div.lineboxtitle {
	margin-bottom: 5px;
	font-weight: bold;
}

div.linebox {
	margin-bottom: 5px;
}

form td {
	vertical-align: middle;
}
</style>
</head>
    <div id="body">
            <?php $base->getNavBar($username, $path, $rank); ?>
            <br/><br/>
            <div style="text-align: center; background: url('../../img/offence/header.gif'); width: 756px; height: 69px; magin: 0 0 0 0; padding-top: 40px;">
                    <div class="titleframe e" style="width: 170px;">
                            <b>View Offence History</b>
                    </div>
            </div>
            <div style="text-align: center;">
                    <p>
                            There are currently <b><?php echo $num_infractions; ?></b> infractions on this account, with an infraction points level of <b><?php echo $points; ?></b> of <b>10</b>
                            <br/>
                    </p>
            </div>
            <br />
            <img src="../../img/offence/<?php echo $zone; ?>4.gif" style="float: left;">
            <img class="widescroll-top" src="../../img/offence/med-top.png" alt="" width="580" height="50" style="float: left;" />
            <div class="widescroll" style="width: 550px; float: left;">
                    <div class="widescroll-bgimg" style="background-image: url('../../img/offence/backdrop_563.png'); width: 563px;">
                            <div class="widescroll-content">
                                    <div style="text-align: center; width: 583px; margin: auto;">
                                    <br/>
                                        <?php
                                            if($num_infractions == 0){
                                                echo 'Congratulations! There are no offences on this account.';
                                            }else{
                                                ?> <table cellpadding="7"><tr><th>Worth</th><th>Issued By</th><th>Issued On</th><th>Expires</th><th>Reason</th></tr> <?php
                                                
                                                foreach($infractions as $inf){
                                                    ?>
                                                        <tr><td><?php echo $inf['worth']; ?></td><td><?php echo htmlentities($inf['issued_by']); ?></td><td><?php echo $inf['date']; ?></td><td><?php echo $inf['expiration']; ?></td><td><?php echo htmlentities($inf['reason']); ?></td></tr>
                                                    <?php
                                                }
                                                
                                                ?> </table> <?php
                                            }
                                        ?>
                                    </span><br/><br/>					
                                    </div>
                                    <div style="clear: both;"></div>
                            </div>
                    </div>
            </div>
            <img class="widescroll-bottom" src="../../img/offence/med-bottom.png" alt="" width="583" height="50" />
            <div style="clear: both"></div>
            <div class="tandc">
                <?php echo $data['wb_foot']; ?>
            </div>
    </div>
</body>
</html>