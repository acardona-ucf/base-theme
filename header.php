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
	<script type="text/javascript">
		$(window).load(function() {
			$('#slider').nivoSlider({
				slices: 10,
				pauseTime: 5000,
				controlNav: false,
				captionOpacity: 0.7
			});
		});

	</script>

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
				<a href="<?= SDES_Static::get_theme_mod_defaultIfEmpty( 'sdes_rev_2015-taglineURL', '#' ); ?>">
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

	<!-- nivo slider -->
	<div class="container site-billboard theme-default">
		<div id="slider" class="nivoSlider">
		  <?php
			/* If using the WP Nivo Plugin, use the following code instead: */
			// if ( function_exists('show_nivo_slider') ) { show_nivo_slider(); } 
			$post = array( 'post_type' => 'billboard' );
			$billboards = new WP_Query( $post );
			while ( $billboards->have_posts() ) : $billboards->the_post();
				if ( has_post_thumbnail() ) :
					the_post_thumbnail( 'post-thumbnail', array('title'=>'#nivo-caption-'.get_the_id(),) );
				endif;
			endwhile;
			wp_reset_query();
			?>
		</div>
		  <?php 
			while ( $billboards->have_posts() ) : $billboards->the_post();
				if ( has_post_thumbnail() ) :
		  ?>
					<div id="nivo-caption-<?= the_id(); ?>" class="nivo-html-caption">
						<?= the_content(); ?>
					</div>
		  <?php
				endif;
			endwhile;
			wp_reset_query();
		  ?>
	</div>

	<!-- content area -->
	<div class="container site-content">		
		<?php !is_page('Home') ? the_title( '<h1 class="page-header">', '</h1>' ) : false ?>
