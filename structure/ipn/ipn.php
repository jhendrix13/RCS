<?php

/*
 * CREDITS GO TO: https://github.com/Quixotix/PHP-PayPal-IPN
 */

// tell PHP to log errors to ipn_errors.log in this directory
ini_set('log_errors', true);
ini_set('error_log', dirname(__FILE__).'/ipn_errors.log');

include('../../includes/config.php');
include('../database.php');
include('../user.php');
$database = new database($db_host, $db_name, $db_user, $db_password);
$user = new user($database);

// intantiate the IPN listener
include('ipnlistener.php');
$listener = new IpnListener();

// tell the IPN listener to use the PayPal test sandbox
//$listener->use_sandbox = true;

// try to process the IPN POST
try {
    $listener->requirePostMethod();
    $verified = $listener->processIpn();
} catch (Exception $e) {
    error_log($e->getMessage());
    exit(0);
}

//Handle IPN Response
if ($verified) {

    $errmsg = '';   // stores errors from fraud checks
    
    // 1. Make sure the payment status is "Completed" 
    if ($_POST['payment_status'] != 'Completed') { 
        // simply ignore any IPN that is not completed
        exit();
    }

    // 2. Make sure seller email matches your primary account email.
    if ($_POST['receiver_email'] != 'jaw1788@comcast.net') {
        $errmsg .= "'receiver_email' does not match: ";
        $errmsg .= $_POST['receiver_email']."\n";
    }
    
    // 3. Make sure the amount(s) paid match
    if ($_POST['mc_gross'] < 2.00) {
        $errmsg .= "'mc_gross' doesn't meet minimum: ";
        $errmsg .= $_POST['mc_gross']."\n";
    }
    
    // 4. Make sure the currency code matches
    if ($_POST['mc_currency'] != 'USD') {
        $errmsg .= "'mc_currency' does not match: ";
        $errmsg .= $_POST['mc_currency']."\n";
    }

    // TODO: Check for duplicate txn_id
    
    if (!empty($errmsg)) {
    
        // manually investigate errors from the fraud checking
        $body = "IPN failed fraud checks: \n$errmsg\n\n";
        $body .= $listener->getTextReport();
        mail('REMOVED', 'IPN Fraud Warning', $body);
        
    } else {
        $txn_id = $_POST['txn_id'];
        $r = $database->processQuery("SELECT COUNT(*) FROM `orders` WHERE `txn_id` = ?", array($txn_id), true);
        
        //order not already processed
        if($r[0]['COUNT(*)'] == 0){
            
            //insert order
            $database->processQuery("INSERT INTO `orders` VALUES (null, ?, ?, ?, ?)", array($txn_id, $_POST['payer_email'], $_POST['mc_gross'], $_POST['custom']), false);
            
            //give donator status
            if(!$user->isDonator($_POST['custom'])) $database->processQuery("UPDATE `users` SET `donator` = 1 WHERE `username` = ? LIMIT 1", array($_POST['custom']), false);
            
            mail('REMOVED', 'Successful Donation', $listener->getTextReport());
        }
    }
    
} else {
    // manually investigate the invalid IPN
    mail('REMOVED', 'Invalid IPN', $listener->getTextReport());
}
?>