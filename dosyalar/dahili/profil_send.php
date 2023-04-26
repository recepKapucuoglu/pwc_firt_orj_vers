<?php
require_once("inc.php");
require "otp_function.php";
require __DIR__ . '/libs/class.phpmailer.php';
require __DIR__ . '/libs/class.smtp.php';
require __DIR__ . '/libs/class.pop3.php';
function sendMail($adsoyad, $onaykodu, $email)
{
    $getdata = http_build_query(
        array('adsoyad' => $adsoyad, 'code' => $onaykodu)
    );

    $opts = array('http' =>
        array(
            'method' => 'POST',
            'content' => $getdata
        )
    );

    $context = stream_context_create($opts);

    $body = file_get_contents('https://www.okul.pwc.com.tr/dosyalar/dahili/template/uyelik_dogrulama.php?', false, $context);

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
    $mailSent = $mail->send();
    if (!$mailSent) {
        return false;
    }
    return true;
}
	$hata_mesaj =  '<div class="alert alert-danger"><strong>Uyarı!</strong> Bir hata oluştu! Lütfen tekrar deneyiniz.</div>'; 
	$tebrik_mesaj =  '<div class="alert alert-success"><strong>Bilgilendirme!</strong> Bilgileriniz başarıyla güncellenmiştir.'; 
	
	if($_POST['adsoyad']) 
	{
		$adsoyad = valueClear($_POST['adsoyad']);
		$email = valueClear($_POST['email']);
		$telefon = valueClear($_POST['telefon']);
		$sifre = valueClear($_POST['sifre']);
		$sifre2 = valueClear($_POST['sifre2']);
		$firma = valueClear($_POST['firma']);
		$unvan = valueClear($_POST['unvan']);
		$adres = valueClear($_POST['adres']);
		$mevcut_sifre = valueClear($_POST['mevcutsifre']);
		$notification = valueClear($_POST['notification']);
		$mail_changed = (strval($_SESSION['dashboardUser']) == strval($email)) ? 1 : 0;
		if($mail_changed == 0)
		{
			$db->where("email", $email);
			$nofusers = $db->get("web_user");
			if($db->count > 0)
			{
				echo '<div class="alert alert-danger"><strong>Uyarı!</strong> Bu email adresi başka kullanıcı tarafından kullanılmaktadır.</div>'; 
				die();
			}
		}
		// Şifre değişikliği var
		if($mevcut_sifre <> "")
		{
			$db->where('email', $_SESSION['dashboardUser']);
			$total = $db->getValue('web_user', "count(id)");
			if($total>0) 
			{
				if($sifre<>$sifre2)
				{
					echo '<div class="alert alert-danger">Girmiş olduğunuz şifreler birbirleriyle eşleşmiyor. </div>'; 
					die();
				} 
				else 
				{
					if (preg_match("#.*^(?=.{8,20})(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9]).*$#", $sifre))
					{
						// Check if this password used before 
						if(webUserUsedThisPasswordBefore($_SESSION['dashboardUser'], $sifre))
						{
							echo '<div class="alert alert-danger">Şifreniz son 10 şifrenizden farklı olmalıdır. </div>'; 
							die();
						}
						
						$data = Array (
								'fullname' 		=> $adsoyad,
								'email' 		=> $email, 
								'company' 		=> $firma,
								'title' 		=> $unvan,
								'phone' 		=> $telefon,
								'password' 		=> webPasswordHash($sifre), 
								'address' 		=> $adres,
								'status' 		=> $mail_changed,
								'notification' 	=> $notification
						);

						$db->where('email', $_SESSION['dashboardUser']);
						$id = $db->update('web_user', $data);
						if($mail_changed == 0)
						{
							$randomHash = crc16(time() . $email . mt_rand(10, 10000));
							$data["activation_code"] = $randomHash;
							sendMail($adsoyad, $randomHash, $email);
							$tebrik_mesaj .= '	<script>setTimeout(function(){
								window.location.href = "https://www.okul.pwc.com.tr/uyelik-dogrulama.php";
							}, 2000);</script>';
						}
						if($id) 
						{
							$sph_id = savePasswordHistory($_SESSION['dashboardUser'], $sifre);
							$_SESSION['dashboardUser'] = $email;
							echo $tebrik_mesaj;
						}
						else
						{
                            echo $hata_mesaj;
                        }
					} 
					else 
					{
						echo '<div class="alert alert-danger">• Şifreniz en az 8 karakter uzunluğunda olmalı, <br/>• Büyük/küçük harf, rakam veya özel karakter içermelidir.</div>';
					}
				}
				
			} 
			else 
			{
				echo '<div class="alert alert-danger">Girmiş olduğunuz eski şifre yanlış. Lütfen tekrar deneyiniz. </div>'; die();
			}
		} 
		else 
		{
			// Şifre değişikliği yok	
			$data = Array (
					'fullname' 		=> $adsoyad,
					'email' 			=> $email, 
					'company' 		=> $firma,
					'title' 		=> $unvan,
					'phone' 			=> $telefon,
					'address' 			=> $adres,
					'status' => $mail_changed,
					'notification' 			=> $notification
					
			);
			if($mail_changed == 0)
			{
				$randomHash = crc16(time() . $email . mt_rand(10, 10000));
				$data["activation_code"] = $randomHash;
				sendMail($adsoyad, $randomHash, $email);
				$tebrik_mesaj .= '	<script>setTimeout(function(){
					window.location.href = "https://www.okul.pwc.com.tr/uyelik-dogrulama.php";
				}, 1000);</script>';
			}
			$db->where('email', $_SESSION['dashboardUser']);
			$id = $db->update ('web_user', $data);
			if ($id)  
			{
				$_SESSION['dashboardUser'] = $email;
				echo $tebrik_mesaj;
			}
			else
			{
                echo $hata_mesaj;
            }
			
		}
		
	} 
	else 
	{
		echo '<div class="alert alert-danger"><strong>Uyarı!</strong> Bir hata oluştu. Lütfen tüm bilgileri eksiksiz doldurunuz.</div>'; 
	}

