<?php include('dosyalar/dahili/header.php');

$page_url = $_SERVER['REQUEST_URI'];
$page_url = explode("/",$page_url);
$page_url = end($page_url);
if (strpos($page_url, '?') !== false) {
		
		$page_url = explode("?",$page_url);
		$page_url = $page_url[0];
	} 
$page_url = t_decode($page_url);
$db->where('seo_url', $page_url);
$results = $db->get('categories');
foreach ($results as $value) {
	$id=$value['id'];
	$kategori_seo_url=$value['seo_url'];
	$banner=$value['banner'];
	$aciklama=t_decode($value['aciklama']);
	$resim_alt_etiket=$value['resim_alt_etiket'];
	$kategori_adi=$value['baslik'];
}

?>
	<section id="sayfaust" style="background-image:url(<?php echo $site_url.$banner; ?>);">
		<div class="basliklar">
			<div class="baslik"><?php echo $kategori_adi; ?></div>
			<ol class="breadcrumb" itemscope itemtype="http://schema.org/BreadcrumbList">
				<li itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">
					<a itemprop="item" href="/"><span itemprop="name">Anasayfa</span></a>
					<meta itemprop="position" content="1" />
				</li>
				<li itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">
					<a itemprop="item" href="egitimlerimiz"><span itemprop="name">Eğitimlerimiz</span></a>
					<meta itemprop="position" content="2" />
				</li>
				<li itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">
					<a itemprop="item" href="javascript:;"><span itemprop="name"><?php echo $kategori_adi; ?></span></a>
					<meta itemprop="position" content="2" />
				</li>
			</ol>
		</div>
	</section>
	<section class="ortakisim">
		<div class="container">
			<div class="row">
				<div class="col-xs-12 col-sm-9 col-md-9 col-lg-9">
					<div class="listeleme">
						<div class="filtreler">
							<div class="siralama">
								<select name="orderby" id="orderbyCate" class="select2add" data-placeholder="Sıralama">
									<option <?php if($_GET['id']==0) echo "selected"; ?> value="0">Önerilen</option>
									<option <?php if($_GET['id']==1) echo "selected"; ?> value="1">Fiyat Artan</option>
									<option <?php if($_GET['id']==2) echo "selected"; ?> value="2">Fiyat Azalan</option>
									<option <?php if($_GET['id']==3) echo "selected"; ?> value="3">A-Z</option>
									<option <?php if($_GET['id']==4) echo "selected"; ?> value="4">Z-A</option>
								</select>
							</div>
							<div class="arama">
							<input type="hidden" id="categories" name="categories" value="<?php echo $page_url; ?>"/>
								<form id="education_list_form_cate" method="post">
									<input type="text" name="arama" value="" placeholder="Eğitim Araması"/>
									<input type="hidden" id="listType" name="listType" value="2"/>
									
									<input type="submit" value=""/>
								</form>
							</div>
						</div>
						<div class="listeler gridlistele">
							<h1><?php echo $kategori_adi; ?></h1>
							<?php echo $aciklama; ?>
							<div class="row">
							<!-- Tarihi belli olan eğitimler -->
							<?php 	
								if ($_SESSION['dashboardUser']){
									$db->where('user_id', $_SESSION['dashboardUserId']);
									$resultsFavorite = $db->get('web_user_favorite');
									$favori_array = array();
									foreach ($resultsFavorite as $valueFavorite) {
										$favori_array[] = $valueFavorite['edu_id'];
									}
								}
							
							
								//Sayfa hesaplama
								
								$more = intval($_GET['page']);
								if($more>0)
									$more = $more - 1;
								$closed = intval($_GET['closed']);
								// 10 kayıt için
								$list = $more * 15;
								$next = $more + 1;
								$prev = $more - 1;
								
								$sort = intval($_GET['id']);
								
								$egitimCheck = $_GET['egitimCheck'];
								$listType = $_GET['listType'];
							
								
								switch($sort) {
									case "0": $sortName="egitim_tarih"; $ascDsc="asc"; $sortNameGenel = "id";  $ascDscGenel="desc"; break;
									case "1": $sortName="ucret"; $ascDsc="asc"; $sortNameGenel = "id";  $ascDscGenel="desc"; break;
									case "2": $sortName="ucret"; $ascDsc="desc"; $sortNameGenel = "id";  $ascDscGenel="desc"; break;
									case "3": $sortName="egitim_adi"; $ascDsc="asc"; $sortNameGenel = "baslik";  $ascDscGenel="asc"; break;
									case "4": $sortName="egitim_adi"; $ascDsc="desc"; $sortNameGenel = "baslik";  $ascDscGenel="desc"; break;
									default : $sortName="egitim_tarih"; $ascDsc="asc"; $sortNameGenel = "id";  $ascDscGenel="desc"; break;
								}
								$egitim_search = valueClear($_GET['arama']);
							
							
								// Açık Eğitim Sorgusu
							
								$dateToday = date("Y-m-d");
								$acik_egitimler[] = "";
								$egitimSayac = 0;
							
								$db->where('egitim_tarih',$dateToday,'>=');
								$db->where('durum', 1);
								$db->where('kategoriler', '%'.$kategori_adi.'%', 'LIKE');
								$db->orderBy($sortName,$ascDsc);
								$resultsCalender = $db->get('education_calender_list');
								$acikEgitimResult = array_slice($resultsCalender, $list, 15);
								
								// Kapanacak eğitimleri buluyoruz.
								foreach ($resultsCalender as $valueCalender) { 
									$acik_egitimler[]=$valueCalender['edu_id'];
								}
								
								$kapaliEgitimGeriyeKalan = 15 - count($acikEgitimResult);
								
								
								// Kapalı Eğitim Sorgusu Geriye kalan
								$db->where('durum', 1);
								$db->where('kategoriler', '%'.$kategori_adi.'%', 'LIKE');
								$db->where('id', $acik_egitimler, 'NOT IN');
								$db->orderBy($sortNameGenel,$ascDscGenel);
								$resultsEducation = $db->get('education_list');
								
								
								$kapaliEgitimResult = array_slice($resultsEducation, $closed, $kapaliEgitimGeriyeKalan);
								
								$toplamEgitimSayisi = count($resultsCalender) + count($resultsEducation);
								
								$toplamKayit = ceil($toplamEgitimSayisi / 15);
								
								$i=0;
								
								foreach ($acikEgitimResult as $valueCalender) { 
									$acik_egitimler[]=$valueCalender['edu_id'];
									$egitimSayac++;
									$i++;
									
							?>
							<div class="col-xs-12 col-sm-6 col-md-4 col-lg-4">
								<div class="egitimkutu-tip2">
									<figure>
										<?php if ($_SESSION['dashboardUser']){ if (in_array($valueCalender['edu_id'], $favori_array)) $aktifEdu = "aktif"; else $aktifEdu = ""; ?><div id="<?php echo $valueCalender['edu_id']; ?>" class="favori <?php echo $aktifEdu; ?> hint--left hint--rounded" aria-label="Favorilere Ekle!"></div><?php } ?>
										<a href="egitimlerimiz/<?php echo $kategori_seo_url."/".$valueCalender['seo_url']; ?>"><img src="<?php echo $site_url.$valueCalender['resim']; ?>" alt="<?php echo $valueCalender['resim_alt_etiket']; ?>" /></a>
									</figure>
									<div class="basliklar">
										<div class="favoriiptal">Favorilerimden Çıkar!</div>
										<a href="egitimlerimiz/<?php echo $kategori_seo_url."/".$valueCalender['seo_url']; ?>">
											<h4><?php echo $valueCalender['egitim_adi']; ?></h4>
											<p><?php echo $valueCalender['kisa_aciklama']; ?></p>
											<div class="bilgiler">
												<time><?php echo date2Human($valueCalender['egitim_tarih']); ?></time><br/>
												<lokasyon style="margin-bottom:-25px; position:relative; margin-left:-180px;"><?php if($valueCalender['webex']==1) echo "Webex"; else echo $valueCalender['sehir_adi']; ?></lokasyon>
												<div class="fiyat">
													<!--<del>1.673,99 TL</del>-->
													<b style='font-size:14px; '><?php if($valueCalender['webex']==1) echo "Ücretsiz"; else echo number_format($valueCalender['ucret'], 2, ',', '.')." TL + KDV"; ?> </b>
												</div>
												
											</div>
										</a>
									</div>
								</div>
							</div>
							<?php $modClear=$egitimSayac%3; if($modClear==0) echo "<div class=\"clearfix\"></div>"; } ?>
							
							
							<!-- Açık tarihi bulunmayan eğitimler -->
							<?php 	
								$closedEdu = 0;
								foreach ($kapaliEgitimResult as $valueCalender) { 
									$egitimSayac++;
									$i++;
									$closedEdu++;
							?>
							<div class="col-xs-12 col-sm-6 col-md-4 col-lg-4">
								<div class="egitimkutu-tip2">
									<figure>
										<?php if ($_SESSION['dashboardUser']){ if (in_array($valueCalender['id'], $favori_array)) $aktifEdu = "aktif"; else $aktifEdu = ""; ?><div id="<?php echo $valueCalender['id']; ?>" class="favori <?php echo $aktifEdu; ?> hint--left hint--rounded" aria-label="Favorilere Ekle!"></div><?php } ?>
										<a href="egitimlerimiz/<?php echo $kategori_seo_url."/".$valueCalender['seo_url']; ?>"><img src="<?php echo $site_url.$valueCalender['resim']; ?>" alt="<?php echo $valueCalender['resim_alt_etiket']; ?>" /></a>
									</figure>
									<div class="basliklar">
										<div class="favoriiptal">Favorilerimden Çıkar!</div>
										<a href="egitimlerimiz/<?php echo $kategori_seo_url."/".$valueCalender['seo_url']; ?>">
											<h4><?php echo $valueCalender['baslik']; ?></h4>
											<p><?php echo $valueCalender['kisa_aciklama']; ?></p>
											<div class="bilgiler">
												<time>Bu eğitim için açık bir tarih bulunmamaktadır. <br/>* Bilgi al formunu doldurarak ilgili eğitim takvimi planlandığında sizinle iletişime geçmemizi sağlayabilirsiniz.</time>
											</div>
										</a>
									</div>
								</div>
							</div>
							
							<?php $modClear=$egitimSayac%3; if($modClear==0) echo "<div class=\"clearfix\"></div>"; } ?>
							
							<?php 
							
								if($egitimSayac==0) 
									echo "<div class=\"alert alert-danger\">
										<button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-hidden=\"true\">×</button>
										<strong>Bilgilendirme!</strong> Aradığınız kritere uygun eğitim bulunmamaktadır.
									</div>"; 
							?>
							
							
							<?php 
							//Sayfalama
							echo "<div class=\"clearfix\"></div>";
							if($toplamEgitimSayisi>$i) {
								echo "<div style='position: absolute; width: 100%;'>";
								if($prev<0) 
									$disabledStle = " opacity: 0.5; cursor: default; pointer-events: none;";
								else
									$disabledStle = "";
								
								echo "<div id=\"dahafazla\" onclick=\"return education_more_cate(".$prev.",".$closedEdu.");\" class=\"dahafazla\" style=\"float:left; width:112px;".$disabledStle."\">GERİ</div>"; 
								echo "<div style=\"float: left; padding-top: 13px; padding-left: 20px; text-align: center;\">Sayfa No:<br/>".$next." / ".$toplamKayit."</div>";
								if($toplamKayit<=$next)
									$disabledStle = " opacity: 0.5; cursor: default; pointer-events: none;";
								else
									$disabledStle = "";
								echo "<div id=\"dahafazla\" onclick=\"return education_more_cate(".$next.",".$closedEdu.");\" class=\"dahafazla\" style=\"float:left; width:112px; margin-left:20px;".$disabledStle."\">İLERİ</div>
								</div>";
							}
							
							?>
							
							
							</div>
						</div>
						
					</div>
				</div>
				<div class="col-xs-12 col-sm-3 col-md-3 col-lg-3">
					<aside>
						<div class="bilesen">
							<div class="baslik">Eğitimler</div>
							<div class="bilesenic">
								<div class="check-list">
									<?php
										$i=0;
										$db->where('durum', 1);
										$db->orderBy("sira","asc");
										$results = $db->get('categories');
										foreach ($results as $value) {
											$i++;
											
											if($value['id']==$id)
												$checked = "checked";
											else
												$checked = "";
									?>
									<span>
										<label for="check<?php echo $value['id']; ?>"><a href="<?php echo $value['seo_url']; ?>">&squf; <?php echo $value['baslik']; ?></a></label>
									</span>
									<?php } ?>
								</div>
							</div>
						</div>
						<!-- Özel Eğitim Banner -->
						<section class="ozelbilgial">
							<a href="bilgi-formu/">
								<img src="dosyalar/images/bilgi-al.png" alt="Bilgi Al" />
							</a>
						</section>
					</aside>
				</div>
			</div>
		</div>
	</section>
<?php include('dosyalar/dahili/footer.php');?>