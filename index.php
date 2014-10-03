<?php require "php/global_config.php" ?>
<?php /* change to true if website goes live */ checkOnlineState(false); ?>
<html>
<head>
	<title><?php getTitle(); ?></title>
	<script src="<?php getScript("jquery") ?>"></script>
	<script src="<?php getScript("jquery.slides") ?>"></script>
	<script src="<?php getScript("jquery.easing.1.3") ?>"></script>
	<script src="<?php getScript("ajax.requests") ?>"></script>
	<link href="<?php getImage("favicon", "png") ?>" rel="shortcut icon">
	<link rel="stylesheet" type="text/css" href="<?php getStylesheet("main") ?>" />
</head>
<body>
	<div class="wrapper">
		<div class="header home_target">
			<div class="logo_slogan">
				<img class="logo_image" src="<?php getImage("favicon", "png"); ?>" alt="logo" />
				<h2 class="title"><?php getHeadline(); ?>
				<br><span class="subTitle"><?php getSubtitle(); ?></span>
				</h2>
				
				<div class="clear"></div>
			</div>
			<div class="divide"></div>
			<div class="nav_container">
				<ul>
					<li id="home" class="active">Startseite</li>
					<li id="prax" class="">Praxis</li>
					<li id="leis" class="">Leistungen</li>
					<li id="team" class="">Das Team</li>
					<li id="cont" class="">Kontakt</li>
				</ul>
			</div>
			<div class="slideshow_container">
				<div id="slideshow_images">
					<?php loadSlideshowImages() ?>
				</div>
			</div>
			<div class="clear"></div>
		</div>

		<div class="divide"></div>
		<div class="block prax">
			<h3><span>Praxis</span></h3>
			<div>
				<?php getBlocks("leistungen") ?>
			</div>
			<div class="clear"></div>
		</div>
		<div class="clear"></div>
		<div class="divide"></div>
		<div class="block leis">
			<h3><span>Leistungen</span></h3>
			<div>
				<?php getBlocks("leistungen") ?>
			</div>
			<div class="clear"></div>
		</div>
		<div class="clear"></div>
		<div class="divide"></div>
		<div class="block team">
			<h3><span>Das Team</span></h3>
			<div>
				<?php getBlocks("leistungen") ?>
			</div>
			<div class="clear"></div>
		</div>
		<div class="clear"></div>
		<div class="divide"></div>
		<div class="block cont">
			<h3><span>Kontakt</span></h3>
			<div>
				<div class="block_center">
					<button class="show_maps">Karte anzeigen</button>
					<button class="show_contact">Kontakt anzeigen</button>
					<img src="<?php getImage("contact1", "jpg"); ?>">
				</div>
				
			</div>
			<div class="clear"></div>
		</div>
		<div class="clear"></div>
		<div class="footer">
			<div class="media_slogan">
				<p>Designed by <a href="https://www.green-fox-media.com">GreenF<span class="green">O</span>X Media</a></p>
			</div>
			<div class="impressum">Impressum</div>
		</div>
		<div data-id="home_target">Top</div>
	</div>

	<div class="block_maps">
		<iframe src="https://www.google.com/maps/embed?pb=!1m14!1m8!1m3!1d1312.4329832882288!2d8.200191155819002!3d48.86076621418391!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x47971d8ff518a92b%3A0x9e44b004ea9dffce!2sKaiserstra%C3%9Fe+58%2F1%2C+76437+Rastatt!5e0!3m2!1sde!2sde!4v1412168602298" width="400" height="300" frameborder="0" style="border:0"></iframe>
	</div>
	<div class="block_contact_form">
		<?php 
			loadContactForm();
		?>
	</div>
	<div class="image_lightbox">
		<p class="image-title"></p>
		<img src="" >
		<p class="image-description"></p>
	</div>
	<div class="overlay"></div>
</body>
</html>
