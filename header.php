<?php
/**
 * Header area for the theme, as called by get_header().
 */
use SDES\SDES_Static as SDES_Static; 
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title><?= wp_title('&raquo;', true, 'right'); bloginfo('name');?> &raquo; UCF</title>

	<link rel="shortcut icon" href="<?= get_stylesheet_directory_uri(); ?>/images/favicon_black.png" >
	<link rel="apple-touch-icon" href="<?= get_stylesheet_directory_uri(); ?>/images/apple-touch-icon.png" >
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" crossorigin="anonymous">
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/nivoslider/3.2/nivo-slider.css" integrity="sha256-ozCgGW2jByABzzSU1X46571+0m23IUok6fdIjS5+nVQ=" crossorigin="anonymous" >
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/nivoslider/3.2/themes/default/default.css" integrity="sha256-O1/X/SEJPvJ8T2QC3NQvoARgaeAk1iGHstf0wUjuNkA=" crossorigin="anonymous" >
	<link rel="stylesheet" href="<?= get_stylesheet_uri(); ?>" >

	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js" integrity="sha384-Pn+PczAsODRZ2PiGg0IheRROpP7lXO1NTIjiPo6cca8TliBvaeil42fobhzvZd74" crossorigin="anonymous"></script>
	<script type="text/javascript" id="ucfhb-script" src="//universityheader.ucf.edu/bar/js/university-header.js?use-1200-breakpoint=1"></script>
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js" integrity="sha256-KXn5puMvxCw+dAYznun+drMdG1IFl3agK0p/pqT9KAo= sha512-2e8qq0ETcfWRI4HJBzQiA3UoyFk6tbNyG+qSaIBZLyW9Xf3sWZHN/lxe9fTh1U45DpPf07yj94KsUHHWe4Yk1A=="crossorigin="anonymous"></script>
	<script src="https://cdn.jsdelivr.net/nivoslider/3.2/jquery.nivo.slider.pack.js" integrity="sha256-4WhPxM5Oma2+XZ8KTrGc4sVBFtqLD5IkzclNM5iKo4c=" crossorigin="anonymous"></script>
	<script src="https://cdn.jsdelivr.net/jquery.validation/1.13.1/jquery.validate.min.js" integrity="sha256-8PU3OtIDEB6pG/gmxafvj3zXSIfwa60suSd6UEUDueI=" crossorigin="anonymous"></script>
	<script src="https://cdn.jsdelivr.net/jquery.validation/1.13.1/additional-methods.min.js" integrity="sha256-TZwF+mdLcrSLlptjyffYpBb8iUAuLtidBmNiMj7ll1k=" crossorigin="anonymous"></script>
	<script type="text/javascript" src="<?= get_stylesheet_directory_uri(); ?>/js/sdes_main_ucf.js"></script>

	<?php $sdes_theme_settings_js_lib = esc_attr(get_option('sdes_theme_settings_js_lib', ''));
		if ( '' != $sdes_theme_settings_js_lib ) {
			echo "\n";
			$libs = explode(';', $sdes_theme_settings_js_lib);
			foreach ($libs as $lib_url) {
				echo "\t<script type='text/javascript' src='{$lib_url}'></script>\n";
			}
		} ?>
	<?php
		$sdes_theme_settings_js = esc_attr(get_option('sdes_theme_settings_js', ''));
		if ( '' != $sdes_theme_settings_js )
		{ ?>
		<script>
			$(document).ready(function(){
				<?= $sdes_theme_settings_js ?>
			});	
		</script>
	<?php } ?>
	<?php
		$sdes_theme_settings_ga_id = esc_attr(get_option('sdes_theme_settings_ga_id', ''));
		if ( '' != $sdes_theme_settings_ga_id ) : ?>
		<script>
			(function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
				(i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
				m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
			})(window,document,'script','//www.google-analytics.com/analytics.js','ga');
			ga('create', "<?= $sdes_theme_settings_ga_id ?>", 'ucf.edu');
			ga('send', 'pageview');
		</script>
	<?php endif; ?>


	<?php
		$sdes_theme_settings_css_lib = esc_attr(get_option('sdes_theme_settings_css_lib', ''));
		if ( '' != $sdes_theme_settings_css_lib ) {
			echo "\n";
			$libs = explode(';', $sdes_theme_settings_css_lib);
			foreach ($libs as $lib_url) {
				echo "\t<link rel='stylesheet' href='{$lib_url}'>\n";
			}
		} ?>
	<?php
		$sdes_theme_settings_css = esc_attr(get_option('sdes_theme_settings_css', ''));
		if ( '' != $sdes_theme_settings_css ) {
			echo "<style>\n\t\t{$sdes_theme_settings_css}\n\t</style>\n";
		} ?>

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
			<a href="<?= site_url() ?>"><?php bloginfo('name'); ?></a>
			<div class="site-subtitle">
				<a href="<?= SDES_Static::url_ensure_prefix( SDES_Static::get_theme_mod_defaultIfEmpty( 'sdes_rev_2015-taglineURL', '//www.sdes.ucf.edu/' ) ); ?>">
					<?= html_entity_decode(get_bloginfo('description')); ?>
				</a>
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
				wp_nav_menu(array('theme_location' => 'main-menu', 'depth' => 1, 'container' => '', 'items_wrap' => '<ol class="nav navbar-nav">%3$s</ol>', 'fallback_cb' => 'SDES\\SDES_Static::fallback_navbar_list_pages'));
				?> 
				<p class="navbar-text navbar-right translate-button">
					<a href="http://it.sdes.ucf.edu/translate/" class="navbar-link">Translate
						<img alt="" src="<?= get_stylesheet_directory_uri(); ?>/images/fff_page_world.png" >
					</a>
				</p>
			</div>
		</div>
	</nav>
