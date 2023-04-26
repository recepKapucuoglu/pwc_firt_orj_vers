<?php
	require_once("inc.php");

	$hata_mesaj =  '<div class="alert alert-danger"><strong>Uyarı!</strong> Bir hata oluştu! Lütfen tekrar deneyiniz.</div>'; 
	$tebrik_mesaj =  '<div class="alert alert-success"><strong>Teşekkürler!</strong> Şifre sıfırlama talebiniz alınmıştır. E-posta adresine gönderilen doğrulama linkini kullanarak şifrenizi güncelleyebilirsiniz. </div><script>document.getElementById("password_form").style.display = "none";</script>'; 
	
	if($_POST['email']) {
		$email = valueClear($_POST['email']);
		
		
		
				
			
				$db->where('email', $email);
				$total = $db->getValue ('web_user', "count(id)");
				if($total<1) {
					echo '<div class="alert alert-danger">Girmiş olduğunuz bilgiler ile sistemde kayıtlı kullanıcı bulunmamaktadır.  </div>'; die();
				} else {
					// Onay Kodu Oluşturuyoruz.
					$onayKodu = getUuid();
					$data = Array ('reset_code' => $onayKodu);
					$db->where ('email', $email);
					$id = $db->update ('web_user', $data);
					
					if ($id) {
						$getdata = http_build_query(
							array('code' => $onayKodu)
						);
						
						$opts = array('http' =>
						 array(
							'method'  => 'POST',
							'content' => $getdata
						)
						);

						$context  = stream_context_create($opts);

						$body = file_get_contents('http://www.socialthinks.com/website/pwc/dosyalar/dahili/template/sifre_sifirlama.php?', false, $context);
						
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
						$mail->Subject = 'Business School - Şifrenizi Sıfırlayın';
						$mail->msgHTML($body);
						if($mail->send()){
							
							echo $tebrik_mesaj;	
						} else {
							echo $hata_mesaj." - Mail Gönderilemedi.";
							
						}
						
					} else
						echo $hata_mesaj;
					
				}
				
			
	} else {
		echo '<div class="alert alert-danger"><strong>Uyarı!</strong> Bir hata oluştu. Lütfen tüm bilgileri eksiksiz doldurunuz.</div>'; 
	}
?>
