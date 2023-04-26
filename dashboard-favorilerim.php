<?php include('dosyalar/dahili/header.php');
if ($_SESSION['dashboardUser']){?>
	<section class="ortakisim">
		<div class="container">
			<div class="row">
				<div class="hidden-xs col-sm-3 col-md-3 col-lg-3">
					<?php include('dosyalar/dahili/dashboard-menu.php');?>
				</div>
				<div class="col-xs-12 col-sm-9 col-md-9 col-lg-9">
					<section class="dashboard-detay favorilerim-sayfasi">
						<div class="sectionbaslik">Favorilerim</div>
						<?php
							$db->where('email', $_SESSION['dashboardUser']);
							$results = $db->get('web_user');
							foreach ($results as $value) {
								$dashboardUserStatus=$value['status'];
							}
							if($dashboardUserStatus<>1) { 
								echo "<div class=\"alert alert-danger\">Hesabınız ile ilgili detaylara ulaşabilmek için e-mail adresinize gelen onay mailini doğrulamanız gerekmektedir.</div>";
							} else {
						?>
						<!--<div class="alert alert-danger">Favorilere eklenen eğitim bulunmamaktadır.</div>-->
						<div class="dbkutuicerik scrollbar-rail">
							<div class="listeler">
								<?php
								$db->where('user_id', $_SESSION['dashboardUserId']);
							$favorilerim = $db->getValue('web_favorite_list', "count(id)");
							
							if($favorilerim<1){
								echo "<div class=\"alert alert-danger\">Favorilere eklenen eğitim bulunmamaktadır.</div>";
							} else {
									$db->where('user_id', $_SESSION['dashboardUserId']);
									
									$resultsCalender = $db->get('web_favorite_list');
									foreach ($resultsCalender as $valueCalender) { 
								?>
								<div class="egitimkutu-tip2 egitimkutu-tip-liste">
									<figure>
										<div id="<?php echo $valueCalender['id']; ?>" class="favorireset aktif hint--left hint--rounded" aria-label="Favorilerimiden Çıkar!"></div>
										<a href="<?php echo $valueCalender['seo_url']; ?>"><img src="<?php echo $site_url.$valueCalender['resim']; ?>"/></a>
									</figure>
									<div class="basliklar">
										
										<a href="<?php echo $valueCalender['seo_url']; ?>">
											<h4><?php echo $valueCalender['baslik']; ?></h4>
											<p><?php echo $valueCalender['kisa_aciklama']; ?></p>
											
										</a>
									</div>
								</div>
								
							<?php } } ?>
							</div>
						</div>
					<?php } ?>
					</section>
				</div>
			</div>
		</div>
	</section>
<?php } else {

echo "<script language=\"JavaScript\">location.href=\"/login.php\";</script>";

}	include('dosyalar/dahili/footer.php');?>
<script>$('body').addClass('dashboard');</script>