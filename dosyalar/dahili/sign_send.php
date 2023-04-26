<?php
require_once("inc.php");
require __DIR__ . "/otp_function.php";

$hata_mesaj = '<div class="alert alert-danger"><strong>Uyarı!</strong> Bir hata oluştu! Lütfen tekrar deneyiniz.</div>';
$tebrik_mesaj = '<div class="alert alert-success"><strong>Teşekkürler!</strong> Üyeliğiniz başarıyla gerçekleşmiştir. E-posta adrsine gönderilen doğrulama kodunu onaylayınız. <a href="/uyelik">Sisteme Giriş Yapmak için tıklayınız.</a> </div><script>document.getElementById("sign_form").reset();</script>';

if ($_POST['adsoyad']) {
    $adsoyad = valueClear($_POST['adsoyad']);
    $email = valueClear($_POST['email']);
    $redirect_url = valueClear($_POST['redirect_url']);
    $telefon = valueClear($_POST['telefon']);
    $sifre = valueClear($_POST['sifre']);
    $sifre2 = valueClear($_POST['sifre2']);
    $firma = valueClear($_POST['firma']);
    $unvan = valueClear($_POST['unvan']);
    $notification = valueClear($_POST['notification']);
    $redirect_after_verify = (isset($_POST["redirect_url_after_verify"])) ? valueClear($_POST["redirect_url_after_verify"]) : "https://www.okul.pwc.com.tr";
    $redirect_after_verify = base64_encode($redirect_after_verify);
    if ($sifre <> $sifre2) {
        echo '<div class="alert alert-danger">Girmiş olduğunuz şifreler birbirleriyle eşleşmiyor. </div>';
        die();
    } else {
        if (preg_match("#.*^(?=.{8,20})(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9]).*$#", $sifre)) {
            $db->where('email', $email);
            $total = $db->getValue('web_user', "count(id)");
            if ($total > 0) {
                echo '<div class="alert alert-danger">Girmiş olduğunuz bilgiler ile sistemde kayıtlı kullanıcı bulunmaktadır. Şifrenizi unuttuysanız, şifremi unuttum bölümünden sıfırlayabilirsiniz. </div>';
                die();
            } else {
                // Onay Kodu Oluşturuyoruz.
                $onayKodu = crc16($email . $sifre . time());
                $data = array(
                    'id' => NULL,
                    'fullname' => $adsoyad,
                    'email' => $email,
                    'password' => webPasswordHash($sifre),
                    'company' => $firma,
                    'title' => $unvan,
                    'activation_code' => $onayKodu,
                    'status' => 0,
                    'phone' => $telefon,
                    'notification' => $notification,
                    "redirect_after_verify" => $redirect_after_verify,
                    'created_date' => $db->now()
                );
                $id = $db->insert('web_user', $data);
                if ($id) {
                    $sph_id = savePasswordHistory($email, $sifre);
                    $getdata = http_build_query(
                        array('adsoyad' => $adsoyad, 'code' => $onayKodu)
                    );

                    $opts = array('http' =>
                        array(
                            'method' => 'POST',
                            'content' => $getdata
                        )
                    );

                    $context = stream_context_create($opts);

                    $body = file_get_contents('https://www.okul.pwc.com.tr/dosyalar/dahili/template/uyelik_dogrulama.php?', false, $context);

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
                    if ($mail->send()) {
                        $_SESSION['dashboardUser'] = $email;
                        $db->where('email', $_SESSION['dashboardUser']);
                        $results = $db->get('web_user');
                        foreach ($results as $value) {
                            $_SESSION['dashboardUserName'] = $value['fullname'];
                            $_SESSION['dashboardUserId'] = $value['id'];
                            $_SESSION['dashboardUserStatus'] = $value['status'];
                        }
                        $dataLogon = array('last_login_date' => $db->now());
                        $db->where('email', $_SESSION['dashboardUser']);
                        $idLogon = $db->update('web_user', $dataLogon);
                        $random = crc16(time());
                        $dogrula = "https://www.okul.pwc.com.tr/uyelik-dogrulama.php?otp=$random";
                        if ($idLogon)
                            echo "<script language='JavaScript'>location.href='$dogrula'</script>";
                        else
                            echo $hata_mesaj;
                    } else {
                        echo $hata_mesaj . " - Mail Gönderilemedi.";

                    }

                } else
                    echo $hata_mesaj;

            }

        } else {
            echo '<div class="alert alert-danger">• Şifreniz en az 8 karakter uzunluğunda olmalı, <br/>• Büyük/küçük harf, rakam veya özel karakter içermelidir.</div>';
            die();
        }
    }
} else {
    echo '<div class="alert alert-danger"><strong>Uyarı!</strong> Bir hata oluştu. Lütfen tüm bilgileri eksiksiz doldurunuz.</div>';
}
