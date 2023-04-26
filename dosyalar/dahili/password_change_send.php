<?php
	require_once("inc.php");

	$hata_mesaj =  '<div class="alert alert-danger"><strong>Uyarı!</strong> Bir hata oluştu! Lütfen tekrar deneyiniz.</div>'; 
	$tebrik_mesaj =  '<div class="alert alert-success"><strong>Teşekkürler!</strong> Şifreniz başarıyla güncellenmiştir, giriş ekranına yönlendiriliyorsunuz. </div><script>document.getElementById("password_form").style.display = "none";					     
	setTimeout(function(){
            window.location.href = "https://www.okul.pwc.com.tr/uyelik";
         }, 1000);</script>'; 
	
	if($_POST['reset_code']) 
	{
		$reset_code = valueClear($_POST['reset_code']);
		
		$sifre = valueClear($_POST['sifre']);
		$sifre2 = valueClear($_POST['sifre2']);
		
		// Get the current user 
		$db->where('reset_code', $reset_code);
		$currentUser = $db->getOne("web_user");
		
		if($sifre <> $sifre2)
		{
			echo '<div class="alert alert-danger">Girmiş olduğunuz şifreler birbirleriyle eşleşmiyor. </div>'; 
			die();
		}
		elseif(webUserUsedThisPasswordBefore($currentUser["email"], $sifre))
		{
			echo '<div class="alert alert-danger">Şifreniz son 10 şifrenizden farklı olmalıdır. </div>';
			die(); 
		}
		else 
		{	
			if (preg_match("#.*^(?=.{8,20})(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9]).*$#", $sifre))
			{
				$db->where('reset_code', $reset_code);
				$total = $db->getValue('web_user', "count(id)");
				if($total<1) 
				{
					echo '<div class="alert alert-danger">Şifre doğrulama kodu hatası. Lütfen tekrar deneyiniz.</div>'; 
					die();
				} 
				else 
				{
					// Onay Kodu Oluşturuyoruz.
					$onayKodu = getUuid();
					$data = Array(
						'reset_code' 			=> '', 
						'password' 			=> webPasswordHash($sifre)
					);
					$db->where('reset_code', $reset_code);
					$id = $db->update('web_user', $data);
					
					if ($id) 
					{
						$sph_id = savePasswordHistory($currentUser["email"], $sifre);
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
				echo '<div class="alert alert-danger">• Şifreniz en az 8 karakter uzunluğunda olmalı, <br/>• Büyük/küçük harf, rakam veya özel karakter içermelidir.</div>'; 
				die();
			}
		}
	} 
	else 
	{
		echo '<div class="alert alert-danger"><strong>Uyarı!</strong> Bir hata oluştu. Lütfen tüm bilgileri eksiksiz doldurunuz.</div>'; 
	}
