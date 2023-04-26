<?php include('_header.php'); ?>				
<section role="main" class="content-body">
	<header class="page-header">
		<h2>Satın Alma Talepleri</h2>
		<div class="right-wrapper pull-right">
			<ol class="breadcrumbs">
				<li>
					<a href="index.php">
						<i class="fa fa-home"></i>
					</a>
				</li>
				<li><span>Talep İşlemleri</span></li>
			</ol>
		</div>
	</header>
	<section class="card card-featured card-featured-primary">
		<header class="card-header">
			<h2 class="card-title">
				Satın Alma Görüntüleme
			</h2>
		</header>
		<div class="card-body">
		<?php
			
			$db->where('id', intval($_GET["id"]));
			$results = $db->get('education_order_form_list');
			foreach ($results as $value) {
				$fatura_tipi		=	$value['fatura_tipi'];
				$fatura_adi		=	$value['fatura_adi'];
				$vergi_dairesi			=	$value['vergi_dairesi'];
				$vergi_no			=	$value['vergi_no'];
				$adres			=	$value['adres'];
				$kayit_tarihi	=	$value['kayit_tarihi'];
				$egitim_adi		=	$value['egitim_adi'];
				$egitim_tarih	=	$value['egitim_tarih'];
				$tutar		=	$value['tutar'];
				$katilimci_sayisi			=	$value['katilimci_sayisi'];
				$edu_cal_id			=	$value['edu_cal_id'];
				$order_id			=	$value['id'];
				
			}

			
		?> 
		<div class="row">
			<div class="col-md-12">
				<form action="talep_detay.php" method="post" class="form-horizontal form-bordered" enctype="multipart/form-data">
							<div class="form-body">
								<div class="form-group row">
									<label class="control-label col-md-3 text-lg-right pt-2" for="inputDefault">Fatura Tipi</label>
									<div class="col-md-6">
										<div class="input-group input-group-icon">
											<span class="input-group-addon">
												<span class="icon"><i class="fa fa-user"></i></span>
											</span>
											<input class="form-control" value="<?php echo $fatura_tipi; ?>" readonly type="text">
										</div>
									</div>
								</div>
								<div class="form-group row">
									<label class="control-label col-md-3 text-lg-right pt-2" for="inputDefault">Fatura Adı</label>
									<div class="col-md-6">
										<div class="input-group input-group-icon">
											<span class="input-group-addon">
												<span class="icon"><i class="fa fa-map-marker"></i></span>
											</span>
											<input class="form-control" value="<?php echo $fatura_adi; ?>" readonly type="text">
										</div>
									</div>
								</div>
								<div class="form-group row">
									<label class="control-label col-md-3 text-lg-right pt-2" for="inputDefault">Vergi Dairesi</label>
									<div class="col-md-6">
										<div class="input-group input-group-icon">
											<span class="input-group-addon">
												<span class="icon"><i class="fa fa-envelope"></i></span>
											</span>
											<input class="form-control" value="<?php echo $vergi_dairesi; ?>" readonly type="text">
										</div>
									</div>
								</div>
								<div class="form-group row">
									<label class="control-label col-md-3 text-lg-right pt-2" for="inputDefault">Vergi No</label>
									<div class="col-md-6">
										<div class="input-group input-group-icon">
											<span class="input-group-addon">
												<span class="icon"><i class="fa fa-phone"></i></span>
											</span>
											<input class="form-control" value="<?php echo $vergi_no; ?>" readonly type="text">
										</div>
									</div>
								</div>
								<div class="form-group row">
									<label class="control-label col-md-3 text-lg-right pt-2" for="inputDefault">Adres Bilgiler</label>
									<div class="col-md-8">
										<textarea id="maxlength_textarea" class="form-control" readonly maxlength="10000" rows="2"><?php echo $adres; ?></textarea>
									</div>
								</div>
								<div class="form-group row">
									<label class="control-label col-md-3 text-lg-right pt-2" for="inputDefault">Kayıt Tarihi</label>
									<div class="col-md-6">
										<div class="input-group input-group-icon">
											<span class="input-group-addon">
												<span class="icon"><i class="fa fa-calendar"></i></span>
											</span>
											<input class="form-control" value="<?php echo date2Human($kayit_tarihi); ?>" readonly type="text">
										</div>
									</div>
								</div>
								<div class="form-group row">
									<label class="control-label col-md-3 text-lg-right pt-2" for="inputDefault">Eğitim Adı</label>
									<div class="col-md-6">
										<span class="label label-default"><?php echo $egitim_adi; ?></span>
									</div>
								</div>
								<div class="form-group row">
									<label class="control-label col-md-3 text-lg-right pt-2" for="inputDefault">Eğitim Tarihi</label>
									<div class="col-md-6">
										<span class="label label-warning"><?php echo date2Human($egitim_tarih); ?></span>
									</div>
								</div>
							</div>
					</form>
			</div>
		</div>
		<br/>
		<div class="row">
			<div class="col-md-12">
				<section class="card">
					<form name="slideForm" action="siparis_list.php" method="post">
					<header class="card-header">
						<div class="card-actions">
							<a href="#" class="fa fa-caret-down"></a>
							<a href="#" class="fa fa-times"></a>
						</div>
						<h2 class="card-title">Katılımcı Listesi</h2>
						
					</header>
					<div class="card-body">
						<div id="islemList">
							<div class="table-responsive">
								<table class="table table-striped mb-none">
									<thead>
										<tr>
											<th>#</th>
											<th>Ad Soyad</th>
											<th>Telefon</th>
											<th>Email</th>
											<th>Firma</th>
											<th>Unvan</th>
											<th>Katılımcı Notu</th>
											
										</tr>
									</thead>
									<tbody>
										<?php
											$i=0;
											$db->where("order_id", $order_id);
											$db->orderBy("id","desc");
											$results = $db->get('education_order_person',array(0,50));
											foreach ($results as $value) {
												$i++;
										?>
										<tr> 
											<td><?php echo $i; ?></td>
											<td><?php echo $value['adsoyad']; ?></td>
											<td><?php echo $value['telefon']; ?></td>
											<td><?php echo $value['email']; ?></td>
											<td><?php echo $value['firma']; ?></td>
											<td><?php echo $value['unvan']; ?></td>
											<td><?php echo $value['not']; ?></td>
										</tr>
										<?php } ?>
										
									</tbody>
								</table>
							</div>
						</div>
					</div>
					</form>
				</section>
			</div>
		</div>
	</section>
</section>
<?php include('_footer.php'); ?>			
			
		