<?php
	require_once("inc.php");

	$hata_mesaj =  '<div class="alert alert-danger"><strong>Uyarı!</strong> Bir hata oluştu! Lütfen tekrar deneyiniz.</div>'; 
	$tebrik_mesaj =  '<div class="alert alert-success"><strong>Bilgilendirme!</strong> Doğrulama kodunuz E-posta adrsinize yeniden gönderilmiştir.</div>'; 
	
	if ($_SESSION['dashboardUser']){
		
		$db->where('email', $_SESSION['dashboardUser']);
		$results = $db->get('web_user');
		foreach ($results as $value) {
			$dashboardUserStatus=$value['status'];
			$adsoyad=$value['fullname'];
			$email=$value['email'];
		}
		
		// Onay Kodu Oluşturuyoruz. activation_code
		$onayKodu = getUuid();
		$data = Array ('activation_code' => $onayKodu);
					$db->where ('email', $email);
					$id = $db->update ('web_user', $data);
					
					if ($id) {
						$getdata = http_build_query(
							array('adsoyad' => $adsoyad, 'code' => $onayKodu)
						);
						
						$opts = array('http' =>
						 array(
							'method'  => 'POST',
							'content' => $getdata
						)
						);
						

						$context  = stream_context_create($opts);

						$body = file_get_contents('https://www.okul.pwc.com.tr/dosyalar/dahili/template/dogrulama.php?', false, $context);
						
						require_once("libs/class.phpmailer.php");
						require("libs/class.smtp.php");
						require("libs/class.pop3.php");
						
						// Mail Gönderme 
						$mail = new PHPMailer;
			
						$mail->IsSMTP();
						$mail->Host = "10.9.18.5";

						// used only when SMTP requires authentication  
						//$mail->SMTPAuth = true;
						$mail->Username = 'egitim@pwc.com.tr';
						$mail->Password = '';
						$mail->SMTPAuth = false;
						$mail->SMTPAutoTLS = false; 
						$mail->Port = 25; 
					
						$mail->CharSet = 'utf-8'; 
						$mail->setFrom('egitim@pwc.com.tr', 'Business School');
						
						$mail->AddAddress($email);
						// Name is optional
						$mail->addReplyTo('egitim@pwc.com.tr', 'Business School');		   
						$mail->setLanguage('tr', '/language');
						
						// Set email format to HTML
						$mail->Subject = 'Business School - Hesabınızı Doğrulayın';
						$mail->msgHTML($body);
						if($mail->send()){
							
								echo $tebrik_mesaj;	
						} else {
							echo $hata_mesaj." - Mail Gönderilemedi." . $mail->ErrorInfo;
							
						}
						
					} else
						echo $hata_mesaj;
					
				
	} else {
		echo '<div class="alert alert-danger"><strong>Uyarı!</strong> Bir hata oluştu. Lütfen tüm bilgileri eksiksiz doldurunuz.</div>'; 
	}
?>
