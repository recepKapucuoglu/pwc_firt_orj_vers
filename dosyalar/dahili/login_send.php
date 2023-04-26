<?php
	require_once("inc.php");
	
	
	$hata_mesaj =  '<div class="alert alert-danger"><strong>Uyarı!</strong> Girmiş olduğunuz <b>Kullanıcı Email</b> veya <b>Şifre</b> bilgileriniz hatalı.</div>'; 
	
	if($_POST['email_login']) {
		$email = valueClear($_POST['email_login']);
		$password = valueClear($_POST['password']);
		$redirect_url = valueClear($_POST['redirect_url']);
		$rememberme = valueClear($_POST['rememberme']);
		
		$db->where('email', $email);
		$logged_user = $db->getOne('web_user');
		if(webUserPasswordVerify($password, $logged_user["password"])) {
			$_SESSION['dashboardUser'] = $email;
			if($rememberme==1) {
				setcookie("usernameCerez",$email, time()+86400*30, '/');
				setcookie("passwordCerez",$password, time()+86400*30, '/');
			} else {
				setcookie("usernameCerez","", time()+86400*30, '/');
				setcookie("passwordCerez","", time()+86400*30, '/');
			}
			$db->where('email', $_SESSION['dashboardUser']);
			$results = $db->get('web_user');
			foreach ($results as $value) {
				$_SESSION['dashboardUserName']=$value['fullname'];
				$_SESSION['dashboardUserId']=$value['id'];
				$_SESSION['dashboardUserStatus']=$value['status'];
				
			}
			
			$dataLogon = Array ('last_login_date' => $db->now());
			$db->where ('email', $_SESSION['dashboardUser']);
			$idLogon = $db->update ('web_user', $dataLogon);
			if($redirect_url<>"")
				echo "<script language=\"JavaScript\">location.href=\"/katilimcilar/".$redirect_url."\";</script>";
			else
				echo "<script language=\"JavaScript\">location.href=\"/dashboard.php\";</script>";
		} else { 
			echo $hata_mesaj;
		}
		
		
	
	} else {
		echo '<div class="alert alert-danger"><strong>Uyarı!</strong> Bir hata oluştu. Lütfen tüm bilgileri eksiksiz doldurunuz.</div>'; 
	}
?>
