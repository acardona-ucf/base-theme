<?php
/**
 * Footer area for the theme, as called by get_footer().
 */

namespace SDES\BaseTheme;
?>
	<div class="blank"></div>
</div>

	<!-- repeated navigation, social media -->
	<div class="container site-content-end">
		<nav class="navbar navbar-default site-nav-repeated">
			<div class="container-fluid">
				<?php
				wp_nav_menu( array( 'theme_location' => 'main-menu', 'depth' => 1, 'container' => '', 'items_wrap' => '<ol class="nav navbar-nav">%3$s</ol>', 'fallback_cb' => 'SDES\\BaseTheme\\SDES_Helper::fallback_navbar_list_pages' ) );
				?> 
				
				<p class="nav navbar-text navbar-right icons">
				<?php
					use SDES\SDES_Static as SDES_Static;
					$url_facebook = SDES_Static::url_ensure_prefix( SDES_Static::get_theme_mod_defaultIfEmpty( 'sdes_rev_2015-facebook', '' ) );
					$url_twitter = SDES_Static::url_ensure_prefix( SDES_Static::get_theme_mod_defaultIfEmpty( 'sdes_rev_2015-twitter', '' ) );
					$url_youtube = SDES_Static::url_ensure_prefix( SDES_Static::get_theme_mod_defaultIfEmpty( 'sdes_rev_2015-youtube', '' ) );
				if ( '' !== $url_facebook && 'http://' !== $url_facebook ) {
					?>
					<a href="<?= $url_facebook ?>">
					<img src="<?= get_stylesheet_directory_uri(); ?>/images/facebook.png" class="icon" alt="facebook icon" title="Facebook">
					</a>
				<?php }
				if ( '' !== $url_twitter && 'http://' !== $url_twitter ) { ?>
					<a href="<?= $url_twitter ?>">
						<img src="<?= get_stylesheet_directory_uri(); ?>/images/twitter.png" class="icon" alt="twitter icon" title="Twitter">
					</a>
				<?php }
				if ( '' !== $url_youtube && 'http://' !== $url_youtube ) {?>
					<a href="<?= $url_youtube ?>">
						<img src="<?= get_stylesheet_directory_uri(); ?>/images/youtube.png" class="icon" alt="youtube icon" title="YouTube">
					</a>
				<?php } ?>
					<a href="http://get.adobe.com/reader/"><img src="<?= get_stylesheet_directory_uri(); ?>/images/content-end-pdf.jpg" alt="adobe acrobat icon" title="Get Adobe Reader"></a>
				</p>
			</div>
		</nav>
	</div>

<?php
	require_once( 'functions/class-sdes-helper.php' );
		use SDES\BaseTheme\SDES_Helper;
	require_once( 'functions/class-render-template.php' );
		use SDES\BaseTheme\Render_Template;

	/**
	 * Logic for displaying footer elements.
	 */
class Footer {
	// TODO: extract Footer class to a separate file - with other logic classes? or its own file?
	
	public static function get_header( $position = 'center', $default_header = 'UCF Today News', $template_args = null ) {
		$ctx_header['header'] = SDES_Static::get_theme_mod_defaultIfEmpty( "sdes_rev_2015-footer_header-{$position}", $default_header );
		return Render_Template::footer_header( $ctx_header, $template_args );
	}

	public static function get_feed_links( $position = 'center', $ctx_links = null, $template_args = null ) {
		/*
		TODO: consider caching ['anchors'] with WP Transients, or a general php alternative
		 * (libraries like C#'s memorycache, not servers like memcached, redis).
		 * Maybe desarrolla2/cache, doctrine/cache, or something under cache/cache on Packagist.org
		 */
		$rss_url = SDES_Static::get_theme_mod_defaultIfEmpty( "sdes_rev_2015-footer_feed-{$position}", 'http://today.ucf.edu/feed/' );
		$rss_url = SDES_Static::url_ensure_prefix( $rss_url );
		$default_anchors = SDES_Static::get_rss_links_and_titles( $rss_url );
		SDES_Static::set_default_keyValue( $ctx_links, 'anchors', $default_anchors );
		return Render_Template::footer_links( $ctx_links, $template_args );
	}

	public static function get_nav_menu( $position = 'left' ) {
		return
			wp_nav_menu( array(
				'theme_location' => "footer-{$position}-menu",
				'container' => '',
				'depth' => 1,
				'items_wrap' => '<ol>%3$s</ol>',
				'fallback_cb' => 'SDES\\BaseTheme\\SDES_Helper::fallback_navbar_list_pages',
				'links_cb' => array(
				'Footer::get_feed_links',
				array( $position, array( 'echo' => false ) ),
				),
			) );
	}

	public static function get_static_content( $position = 'left' ) {
		SDES_Static::set_default_keyValue( $args, 'echo', true );

		$output = wp_kses(
			get_option( "sdes_rev_2015-footer_content-{$position}", '' ),
			wp_kses_allowed_html( 'post' ),
		null);

		if ( $args['echo'] ) { echo $output;
		} else { return $output; }
	}

	public static function should_show_static_content( $position = 'center' ) {
		$content = get_option( "sdes_rev_2015-footer_content-{$position}", '' );
		return '' !== $content && ! ctype_space( $content );
	}

	public static function should_show_nav_menu( $position = 'center' ) {
		return SDES_Static::get_theme_mod_defaultIfEmpty( "sdes_rev_2015-footer_showLinks-{$position}", false );
	}

	public static function is_feed_set( $position = 'left' ) {
		return '' !== SDES_Static::get_theme_mod_defaultIfEmpty( "sdes_rev_2015-footer_feed-{$position}", '' );
	}
}
?>
	<!-- footers -->
	<footer class="site-footer-container">

		<!-- main footer -->
		<div class="site-footer">
			<div class="container"> 
				<div class="row">
					<div class="col-md-4">
						<?php
						if ( Footer::should_show_static_content( 'left' ) ) {
							Footer::get_static_content( 'left' );
						} else {
							Footer::get_header( 'left', 'Site Hosted by SDES' );
							if ( Footer::should_show_nav_menu( 'left' ) ) {
								Footer::get_nav_menu( 'left' );
							} elseif ( Footer::is_feed_set( 'left' ) ) {
								Footer::get_feed_links( 'left' );
							} else {
								?>
								<ol>
								<li><a href="http://www.sdes.ucf.edu/">SDES Home</a></li>
								<li><a href="http://www.sdes.ucf.edu/about">What is SDES? / About</a></li>
								<li><a href="http://www.sdes.ucf.edu/departments">SDES Departments</a></li>
								<li><a href="http://www.sdes.ucf.edu/events">Division Calendar</a></li>
								<li><a href="http://www.sdes.ucf.edu/contact">Contact Us</a></li>
								<li><a href="http://www.sdes.ucf.edu/staff">SDES Leadership Team</a></li>
								<li><a href="http://creed.sdes.ucf.edu/">The UCF Creed</a></li>
								<li><a href="http://it.sdes.ucf.edu/">SDES Information Technology</a></li>
								</ol>
								<?php
							}
						}
							?>
					</div>
					<div class="col-md-4">
						<?php
						if ( Footer::should_show_static_content( 'center' ) ) {
								Footer::get_static_content( 'center' );
						} else {
							Footer::get_header( 'center' );
							if ( Footer::should_show_nav_menu( 'center' ) ) {
								Footer::get_nav_menu( 'center' );
							} else {
								Footer::get_feed_links( 'center' );
							}
						}
						?>
					</div>
					<div class="col-md-4">
						<?php
						if ( Footer::should_show_static_content( 'right' ) ) {
							Footer::get_static_content( 'right' );
						} else {
							// TODO: refactor search form to match template-logic-layout separation implemented above.
							// TODO: move contact info to function in Footer class.
						?>
						<h2>Search</h2>
						<span id="footer-search">
						<form action="http://google.cc.ucf.edu/search">
							<label for="footer-search-field">Search UCF</label>
							<input type="hidden" name="output" value="xml_no_dtd">
							<input type="hidden" name="proxystylesheet" value="UCF_Main">
							<input type="hidden" name="client" value="UCF_Main">
							<input type="hidden" name="site" value="UCF_Main">
							<div class="input-group">
								<input type="text" id="footer-search-field" class="form-control">
								<span class="input-group-btn">
									<input type="submit" class="btn" value="Search">
								</span>
							</div>
						</form>
						</span>
						<?php
						// TODO: should directory CMS feed be more granular?
						require_once( 'functions/class-sdes-helper.php' );
						$directory_cms_acronym = esc_attr( get_option( 'sdes_theme_settings_dir_acronym' ) );
						$dept_feed = SDES_Helper::get_sdes_directory_department( $directory_cms_acronym,
							array( 'name' => get_bloginfo( 'name' ), 'email' => 'sdes@ucf.edu' )
						);

						$ctx_contact['departmentName'] = SDES_Static::get_theme_mod_defaultIfEmpty( 'sdes_rev_2015-departmentName', $dept_feed['name'] );
						$ctx_contact['phone'] = SDES_Static::get_theme_mod_defaultIfEmpty( 'sdes_rev_2015-phone', $dept_feed['phone'] );
						$ctx_contact['email'] = SDES_Static::get_theme_mod_defaultIfEmpty( 'sdes_rev_2015-email', $dept_feed['email'] );
						$ctx_contact['buildingNumber'] = SDES_Static::get_theme_mod_defaultIfEmpty( 'sdes_rev_2015-buildingNumber', $dept_feed['location']['buildingNumber'] );
						$ctx_contact['buildingName'] = SDES_Static::get_theme_mod_defaultIfEmpty( 'sdes_rev_2015-buildingName', $dept_feed['location']['building'] );
						$ctx_contact['roomNumber'] = SDES_Static::get_theme_mod_defaultIfEmpty( 'sdes_rev_2015-roomNumber', $dept_feed['location']['roomNumber'] );
						Render_Template::footer_contact( $ctx_contact );
						?>
						<?php } ?>
					</div>
				</div>
			</div>
		</div>

		<!-- sub footer -->
		<div class="site-sub-footer">
			<div class="container">
				<div class="row">
					<div class="col-md-8">
						Copyright &copy; 2015 <a href="http://www.sdes.ucf.edu/">Student Development and Enrollment Services</a> &bull;
						Designed by <a href="http://it.sdes.ucf.edu/">SDES Information Technology</a>
					</div>
					<div class="col-md-4 text-right">
						<a href="http://validator.w3.org/check?uri=referer">Valid HTML 5</a> &bull;
						<a href="http://jigsaw.w3.org/css-validator/check/referer?profile=css3">Valid CSS 3</a>
					</div>
				</div>
			</div>
		</div>
	</footer>
	
	<?php wp_footer(); ?>

</body>
</html>
