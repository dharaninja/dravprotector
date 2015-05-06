<?php
function xencoder($input,$key) {
	$salt = '';
	$key = substr(hash('sha512', base64_encode($key.$salt)), 0, 32);
	$iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB);
	$iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
	$output = base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_256, $key, $input, MCRYPT_MODE_ECB, $iv));
	return $output;
}

function xdecoder($input,$key) {
   $salt = '';
   $key = substr(hash('sha512', base64_encode($key.$salt)), 0, 32);
   $iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB);
   $iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
   $output = mcrypt_decrypt(MCRYPT_RIJNDAEL_256, $key, base64_decode($input), MCRYPT_MODE_ECB, $iv);
   return $output;
}

function xhasher($input,$key) {
	$output = strtoupper(hash('sha512',xencoder($input,$key)));
	$output = substr($output,0,36);
	$output = "^XH".$output."^";
	return $output;
}

$username = "";
$password = "";
echo "<center><h1>XHASHER GENERATOR</h1></center>";
echo "<center><h3>Created By Dharaninja</h3></center><br>";
echo "<center>USERNAME : <strong>".xhasher($username,"NHGURAGVPNGVBA_HFREANZR")."</strong></center><br>";
echo "<center>PASSWORD : <strong>".xhasher($password,"NHGURAGVPNGVBA_CNFFJBEQ")."</strong></center><br>";

?>
