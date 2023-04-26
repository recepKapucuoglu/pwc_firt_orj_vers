<?php include('dosyalar/dahili/header.php');
$code = valueClear($_GET['code']);
$db->where('reset_code', $code);
$total = $db->getValue ('web_user', "count(id)");
if($total>0) {
?>
	<section id="sayfaust" style="background-image:url(dosyalar/images/sayfaust-bg.jpg);">
		<div class="basliklar">
			<div class="baslik">ŞİFRE SIFIRLAMAA</div>
			<ol class="breadcrumb" itemscope itemtype="http://schema.org/BreadcrumbList">
				<li itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">
					<a itemprop="item" href="#"><span itemprop="name">Anasayfa</span></a>
					<meta itemprop="position" content="1" />
				</li>
				<li itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">
					<a itemprop="item" href="#"><span itemprop="name">Şifre Sıfırlama</span></a>
					<meta itemprop="position" content="2" />
				</li>
			</ol>
		</div>
	</section>
	<section class="ortakisim">
		<div class="container">
			<div class="row">
				<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
					<?php
						$db->where('reset_code', $code);
						$results = $db->get('web_user');
						foreach ($results as $value) {
							$fullname=$value['fullname'];
							$id=$value['id'];
						}
						
						
					?>
					<div class="passwordFormList">
					</div>
					<form id="password_form" method="post" class="girisform" onsubmit="return password_change_send();">
						<div class="baslik">
							<b>Şifre Güncelleme</b>
							
						</div>
						<input type="hidden" name="reset_code" id="reset_code" value="<?php echo $code; ?>" />
						<div class="label-div2">
							<label for="sifre">Yeni Şifre*</label>
							<input type="password" name="sifre" id="sifre" value="" required />
						</div>
						<div class="label-div2">
							<label for="sifre2">Şifre Tekrar*</label>
							<input type="password" name="sifre2" id="sifre2" value="" required />
						</div>
						
						
						<div class="bilgial buton renk2 button13"><a href="javascript:;" onclick="return password_change_send();"><span>Şifremi Güncelle</span></a></div>
						
					</form>
					
				</div>
			</div>
		</div>
	</section>
<?php 
} else
		header("Location: https://www.okul.pwc.com.tr/dashboard.php");
include('dosyalar/dahili/footer.php');
?>