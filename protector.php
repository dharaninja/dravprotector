<?php
// DRAV Admin Login Page Protector
// Created by a.k.a Dharaninja
// Email : dhara.s3curity@gmail.com

error_reporting(0);
session_name(strtoupper(sha1(xencoder($_SERVER['HTTP_HOST'],"FRFFVBA_ANZR".date("l, d F Y")))));
session_start();
session_regenerate_id();
date_default_timezone_set("Asia/Jakarta");

$db_host = "localhost";
$db_user = "username";
$db_pass = "password";
$db_name = "protector";
$db_con = mysqli_connect($db_host,$db_user,$db_pass);

// Create  Database
$new_db = "CREATE DATABASE IF NOT EXISTS ".$db_name;
$create_db = mysqli_query($db_con,$new_db);
$select_db = mysqli_select_db($db_con,$db_name);

if(!$db_con) {
	die('<h1>Could not connect to MySQL Server</h1> <strong>Description :</strong> '.mysqli_connect_error());
}

function xencoder($input,$key) {
	$salt = ''; // put salt here
	$key = substr(hash('sha512', base64_encode($key.$salt)), 0, 32);
	$iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB);
	$iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
	$output = base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_256, $key, $input, MCRYPT_MODE_ECB, $iv));
	return $output;
}

function xhasher($input,$key) {
	$output = strtoupper(hash('sha512',xencoder($input,$key)));
	$output = substr($output,0,36);
	$output = "^XH".$output."^";
	return $output;
}

function ip()
{ 
	if(isset($_SERVER['HTTP_CLIENT_IP']) && $_SERVER['HTTP_CLIENT_IP']) return $_SERVER['HTTP_CLIENT_IP'];
	if(isset($_SERVER['HTTP_X_FORWARDED_FOR']) && $_SERVER['HTTP_X_FORWARDED_FOR']) return $_SERVER['HTTP_X_FORWARDED_FOR']; 
	if(isset($_SERVER['HTTP_X_FORWARDED']) && $_SERVER['HTTP_X_FORWARDED']) return $_SERVER['HTTP_X_FORWARDED']; 
	if(isset($_SERVER['HTTP_FORWARDED_FOR']) && $_SERVER['HTTP_FORWARDED_FOR']) return $_SERVER['HTTP_FORWARDED_FOR']; 
	if(isset($_SERVER['HTTP_FORWARDED']) && $_SERVER['HTTP_FORWARDED']) return $_SERVER['HTTP_FORWARDED']; 
	if(isset($_SERVER['HTTP_X_COMING_FROM']) && $_SERVER['HTTP_X_COMING_FROM']) return $_SERVER['HTTP_X_COMING_FROM']; 
	if(isset($_SERVER['HTTP_COMING_FROM']) && $_SERVER['HTTP_COMING_FROM']) return $_SERVER['HTTP_COMING_FROM'];
	if(isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR']) return $_SERVER['REMOTE_ADDR']; 
	return '';
}

function xsalt() {
    $alphabet = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()_-=+;:,.?';
    $pass = array(); 
    $alphaLength = strlen($alphabet) - 1; 
    for ($i = 0; $i < 26; $i++) {
        $n = mt_rand(0, $alphaLength);
        $pass[] = $alphabet[$n];
    }
    return implode($pass); 
}

// Database Table Setup
$attacktab = "CREATE TABLE IF NOT EXISTS drav_attack_log (ip_adress varchar(255),date varchar(255),user_agent varchar(255))";
$authkeytab = "CREATE TABLE IF NOT EXISTS drav_auth (username varchar(255),password varchar(255))";
$authlogtab = "CREATE TABLE IF NOT EXISTS drav_auth_log (username varchar(255), ip_adress varchar(255), date varchar(255),user_agent varchar(255))";
$attacktab = mysqli_query($db_con,$attacktab);
$authkeytab = mysqli_query($db_con,$authkeytab);
$authlogtab = mysqli_query($db_con,$authlogtab);

if(!isset($_SESSION['protector_salt'])) {
	$_SESSION['protector_salt'] = xsalt();
}

if(isset($_GET['u']) && isset($_GET['p'])) { 
	$checkinput = "SELECT * FROM drav_auth WHERE username = '".xhasher($_GET['u'],"NHGURAGVPNGVBA_HFREANZR")."' AND password = '".xhasher($_GET['p'],"NHGURAGVPNGVBA_CNFFJBEQ")."'";
	$checklock = "SELECT * FROM drav_attack_log WHERE ip_adress='".mysqli_real_escape_string($db_con,ip())."'";
	$checkinput = mysqli_query($db_con,$checkinput);
	$checklock = mysqli_query($db_con,$checklock);
	if(mysqli_num_rows($checkinput) > 0 && mysqli_num_rows($checklock) <= 3 ) { // authentication process
		session_regenerate_id();
		if(!isset($_SESSION['protector_ip']) || !isset($_SESSION['protector_agent']) || !isset($_SESSION['protector_file'])) {
			$authlog = "INSERT INTO drav_auth_log VALUES ('".xhasher($_GET['u'],"NHGURAGVPNGVBA_HFREANZR")."','".mysqli_real_escape_string($db_con,ip())."','".mysqli_real_escape_string($db_con,date("l, d F Y h:i:s A"))."','".mysqli_real_escape_string($db_con,$_SERVER['HTTP_USER_AGENT'])."')";
			$authlog = mysqli_query($db_con,$authlog);
		}
		$_SESSION['protector_ip'] = xhasher(ip(),$_SESSION['protector_salt']."FRFFVBA_VC".date("l, d F Y"));
		$_SESSION['protector_agent'] = xhasher($_SERVER['HTTP_USER_AGENT'],$_SESSION['protector_salt']."FRFFVBA_NTRAG".date("l, d F Y"));
		$_SESSION['protector_file'] = xhasher($_SERVER['SCRIPT_NAME'],$_SESSION['protector_salt']."FRFFVBA_FPEVCG".date("l, d F Y"));
	}	
}

if($_SESSION['protector_ip'] != xhasher(ip(),$_SESSION['protector_salt']."FRFFVBA_VC".date("l, d F Y")) || $_SESSION['protector_agent'] != xhasher($_SERVER['HTTP_USER_AGENT'],$_SESSION['protector_salt']."FRFFVBA_NTRAG".date("l, d F Y")) || $_SESSION['protector_file'] != xhasher($_SERVER['SCRIPT_NAME'],$_SESSION['protector_salt']."FRFFVBA_FPEVCG".date("l, d F Y"))) { 
	session_destroy();
	$attacklog = "INSERT INTO drav_attack_log VALUES ('".mysqli_real_escape_string($db_con,ip())."','".mysqli_real_escape_string($db_con,date("l, d F Y h:i:s A"))."','".mysqli_real_escape_string($db_con,$_SERVER['HTTP_USER_AGENT'])."')";
	$attacklog = mysqli_query($db_con,$attacklog);
	header('location:https://wordpress.com/wp-login.php');
}

if(isset($_GET['loggedout'])) { // log out process
	session_destroy();
	header('location:index.php?loggedout=true');
}

mysqli_close($db_con);
?>
