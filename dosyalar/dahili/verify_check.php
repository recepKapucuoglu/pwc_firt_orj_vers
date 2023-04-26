<?php
if(!isset($db)){
	include __DIR__ . "/_db.php";
}
$actual_link = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
$path = parse_url($actual_link, PHP_URL_PATH);
$validUrls = ["/uyelik-dogrulama.php", "/dosyalar/dahili/verify_user_otp.php", "/dosyalar/dahili/verify_user_otp_resend.php"];
if(isset($_SESSION["dashboardUser"])){
  $db->where('email', $_SESSION['dashboardUser']);
	$results = $db->get('web_user');
	foreach ($results as $value) {
		$dashboardUserStatus=$value['status'];
	}
  if($dashboardUserStatus<>1 && !in_array($path, $validUrls)){
    header("Location: https://www.okul.pwc.com.tr/uyelik-dogrulama.php");
  }
}