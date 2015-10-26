<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title><?php wp_title(' | ', true, 'right'); ?></title>

	<link rel="stylesheet" href="<?php bloginfo('stylesheet_url'); ?>" >
	<link rel="shortcut icon" href="images/favicon_black.png" >
	<link rel="apple-touch-icon" href="images/apple-touch-icon.png" >

	<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
	<script type="text/javascript" id="ucfhb-script" src="https://universityheader.ucf.edu/bar/js/university-header.js"></script>
	<script type="text/javascript" src="<?php bloginfo('template_url'); ?>/bootstrap/js/bootstrap.min.js"></script>

	<script type="text/javascript" src="<?php bloginfo('template_url'); ?>/js/sdes_main_ucf.js"></script>
	<script type="text/javascript" src="<?php bloginfo('template_url'); ?>/nivoslider/jquery.nivo.slider.pack.js"></script>
	<script type="text/javascript" src="https://ajax.aspnetcdn.com/ajax/jquery.validate/1.13.1/jquery.validate.min.js"></script>
	<script type="text/javascript" src="http://ajax.aspnetcdn.com/ajax/jquery.validate/1.13.1/additional-methods.min.js"></script>

	<!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
	<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
	<!--[if lt IE 9]>
	<script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
	<script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
	<![endif]-->
	<?php wp_head(); ?>
</head>
<body>
	<!-- header -->
	<div class="container header">
		<div class="site-title">
			<a href="#"><?php bloginfo('name'); ?></a>
			<div class="site-subtitle">
				<a href="#">Student Development<br> and Enrollment Services</a>
			</div>
		</div>
	</div>

	<!-- WP Navi -->
	<nav class="site-nav navbar navbar-default">
		<div class="container">
			<div class="navbar-header">
				<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target=".navbar-collapse">
					<span class="sr-only">Toggle navigation</span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
				</button>
			</div>
			<div class="navbar-collapse collapse">
				<?php
				wp_nav_menu(array('theme_location' => 'main-menu', 'container' => '', 'depth' => 1, 'items_wrap' => '<ul class="nav navbar-nav">%3$s</ul>', 'fallback_cb' => 'SDES_Static::fallback_navbar_list_pages'));
				?> 
				<p class="navbar-text navbar-right translate-button">
					<a href="http://it.sdes.ucf.edu/translate/" class="navbar-link">Translate
						<img alt="translate icon" src="<?php bloginfo('template_url'); ?>/images/fff_page_world.png" >
					</a>
				</p>
			</div>
		</div>
	</nav>

	<!-- static billboard -->
	<div class="container site-billboard theme-default"> 
		<img src="bloginfo('template_url'); ?>/images/billboard2.jpg" alt="billboard image"> 
	</div>

	<!-- content area -->
	<div class="container site-content">