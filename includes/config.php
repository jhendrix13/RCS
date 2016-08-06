<?php
error_reporting(E_ALL ^ E_NOTICE);
define("IN_SCRIPT", true);

//domain
$domain = $_SERVER['HTTP_HOST'];

//path - DO !NOT! touch .... make sure there is a trailing slash! e.g: google.com/ <<<
$path = '{path}';

//version
$version = '1.5';

//check for installation file
if(is_dir('install')) die('<a href="install/">Install RCS '. $version .'</a><br/><br/><a href="http://www.moparscape.org/smf/index.php/topic,564369.0.html" target="_blank">Support for RCS</a> or alternatively, <a href="http://www.rune-server.org/programming/website-development/442699-v1-4-free-rcs-runescape-community-script-old-runescape-website-remake.html" target="_blank">here</a>');

//basic site configuration
$data['wb_name'] = '{wb_name}';
$data['wb_abbr'] = '{abbr}';
$data['wb_title'] = '{title}';
$data['wb_foot'] = 'This website and its contents are copyright &copy; 1999 - 2007 Jagex Ltd.<br/>
Use of this website is subject to our Terms+Conditions and Privacy policy<br/>Powered by RuneScape Community Script (RCS)';
$data['login_time'] = 50000; //SECONDS

//if you change $data['use_recaptcha'] to true, you must specify a private and public keycode
//^^^^^^^^^^^^
$data['use_recaptcha'] = false; //true = use google's recaptcha, false = don't
$data['public_key'] = ''; //public key given to you by google
$data['private_key'] = ''; //private key given to you by google

//database connection settings
$db_host = '{host}';
$db_user = '{user}';
$db_name = '{name}';
$db_password = '{pass}';

//running on localhost?
$localhost = (in_array($_SERVER['HTTP_HOST'], array('localhost', '127.0.0.1'))) ? true : false;
?>