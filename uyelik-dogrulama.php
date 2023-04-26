<?php
require __DIR__ . '/dosyalar/dahili/header.php';
require __DIR__ . '/dosyalar/dahili/libs/class.phpmailer.php';
require __DIR__ . '/dosyalar/dahili/libs/class.smtp.php';
require __DIR__ . '/dosyalar/dahili/libs/class.pop3.php';
require __DIR__ . '/dosyalar/dahili/otp_function.php';

// mail gönderme fonksiyonu
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

// Session yoksa doğrulamasına izin verme
if ($_SESSION['dashboardUser']) {
    // veritabanından aktif olup olmadığını öğreniyoruz
    $db->where('email', $_SESSION['dashboardUser']);
    $results = $db->getOne('web_user');
    // kullanıcının zaten aktif olup olmadığını öğrenelim.
    $isActive = $results["status"];
    // kullanıcının isim ve mail bilgilerini çekelim
    $user_name = $results["fullname"];
    $user_mail = $results["email"];
    // eğer kullanıcı yeni kayıt değilse bu sayfaya geldiğinde otomatik olarak
    // kod oluşturup veritabanında güncelleyip mail atalım
    $user_code = crc16($user_mail . time() . mt_rand(9999, 99999));
    // zaten aktifse burada ne işi var?
    if ($isActive) {
        header("Location: https://www.okul.pwc.com.tr/");
        exit();
    }
	/*
    // yeni üye olup da mı geldi?
    // eğer yeni üyeyse zaten doğrulama kodu gönderdik
    // yeniden yollama
    $newUser = isset($_GET["otp"]);
    if(!$newUser){
        // üyenin kaydını güncelleyip yeni kodu veritabanına işleyelim
        $data = array('activation_code' => $user_code);
        $db->where('email', $_SESSION['dashboardUser']);
        $id = $db->update('web_user', $data);
        if($id){
            sendMail($user_name, $user_code, $user_mail);
        }
    }
	*/
    // yoksa güvenlik açısından emailin tamamını göstermeyelim
    $secure_mail = $_SESSION["dashboardUser"];
    ?>
    <section id="sayfaust" style="background-image:url(dosyalar/images/sayfaust-bg.jpg);">
        <div class="basliklar">
            <div class="baslik">HESAP DOĞRULAMA</div>
            <ol class="breadcrumb" itemscope itemtype="http://schema.org/BreadcrumbList">
                <li itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">
                    <a itemprop="item" href="#"><span itemprop="name">Anasayfa</span></a>
                    <meta itemprop="position" content="1"/>
                </li>
                <li itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">
                    <a itemprop="item" href="#"><span itemprop="name">Hesap Doğrulama</span></a>
                    <meta itemprop="position" content="2"/>
                </li>
            </ol>
        </div>
    </section>
    <section class="ortakisim">
        <div class="container">
            <div class="row">
                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                    <div class="sepet-div">
                        <div class="row">
                            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                                <center>
                                    <div class="bildirim"></div>
                                    <form method="post" class="girisform" id="verify_user_otp" onsubmit="javascript: void(0);">
                                        <div class="baslik">
                                            <b>Mail Doğrulama</b>
                                        </div>
                                        <p>Lütfen, <b><?php echo $secure_mail; ?></b> adresine yollanan şifreyi giriniz.<br />
										<small>E-posta adresinizi yanlış girdiyseniz, <a href="hatali-mail.php" onclick="return confirm('Emin misiniz?')">bu linke tıklayınız.</a></small>
										</p>
                                        <input type="hidden" name="redirect_url" id="redirect_url"
                                               value="turquality-ve-ihracat-tesvikleri-webcast-18022021-1400">
                                        <div class="label-div2">
                                            <label for="pass">Şifre*</label>
                                            <input type="text" name="otp" value="" id="pass" required="">
                                        </div>
                                        <div class="bilgial buton renk2 button13"><a href="javascript:;"
                                                                                     onclick="return verify_user_otp();"><span>Doğrula</span></a>
                                        </div>
                                        <div class="bilgial buton renk2 button13"><a href="javascript:;"
                                                                                     onclick="return verify_user_otp_resend();"><span>Tekrar şifre iste</span></a>
                                        </div>
                                    </form>
                                </center>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <?php
} else {
    header("Location: https://www.okul.pwc.com.tr/");
}
require __DIR__ . '/dosyalar/dahili/footer.php';