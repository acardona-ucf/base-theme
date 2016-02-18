</div>
<!-- repeated navigation, social media -->
	<div class="container site-content-end">
		<nav class="navbar navbar-default site-nav-repeated">
			<div class="container-fluid">
				<?php
				wp_nav_menu(array('theme_location' => 'main-menu', 'depth' => 1, 'container' => '', 'items_wrap' => '<ul class="nav navbar-nav">%3$s</ul>', 'fallback_cb' => 'SDES_Static::fallback_navbar_list_pages'));
				?> 
				
				<p class="nav navbar-text navbar-right icons">
				<?php
					$url_facebook = SDES_Static::get_theme_mod_defaultIfEmpty('sdes_rev_2015-facebook', '');
					$url_twitter = SDES_Static::get_theme_mod_defaultIfEmpty('sdes_rev_2015-twitter', '');
					$url_youtube = SDES_Static::get_theme_mod_defaultIfEmpty('sdes_rev_2015-youtube', '');
					if( '' != $url_facebook) { 
					?>
					<a href="<?= $url_facebook ?>">
						<img src="<?php bloginfo('template_url'); ?>/images/facebook.png" alt="icon" title="Facebook">
					</a>
				<?php } 
					if( '' != $url_twitter) { ?>
					<a href="<?= $url_twitter ?>">
						<img src="<?php bloginfo('template_url'); ?>/images/twitter.png" alt="icon" title="Twitter">
					</a>
				<?php }
					if( '' != $url_youtube) {?>
					<a href="<?= $url_youtube ?>">
						<img src="<?php bloginfo('template_url'); ?>/images/youtube.png" alt="icon" title="YouTube">
					</a>
				<?php } ?>
					<a href="http://get.adobe.com/reader/"><img src="<?php bloginfo('template_url'); ?>/images/content-end-pdf.jpg" alt="icon" title="Get Adobe Reader"></a>
				</p>
			</div>
		</nav>
	</div>

	<!-- footers -->
	<footer class="site-footer-container">

		<!-- main footer -->
		<div class="site-footer">
			<div class="container"> 
				<div class="row">
					<div class="col-md-4">
						<h2>Site Hosted by SDES</h2>
						<ul>
							<li><a href="http://www.sdes.ucf.edu/">Student Development and Enrollment Services</a></li>
							<li><a href="http://www.sdes.ucf.edu/about">What is SDES? / Students, Parents, Faculty, Staff</a></li>
							<li><a href="http://www.sdes.ucf.edu/departments">SDES Departments, Offices, and Services</a></li>
							<li><a href="http://www.sdes.ucf.edu/events">Division Events and Calendar</a></li>
							<li><a href="http://www.sdes.ucf.edu/contact">Contact SDES</a></li>
							<li><a href="http://www.sdes.ucf.edu/staff">SDES Leadership Team</a></li>
							<li><a href="http://creed.sdes.ucf.edu/">The UCF Creed</a></li>
							<li><a href="http://it.sdes.ucf.edu/">SDES Information Technology</a></li>
						</ul>
					</div>
					<div class="col-md-4">
						<?php
							/* TODO: consider caching ['anchors'] with WP Transients, or a general php alternative
							 * (libraries like C#'s memorycache, not servers like memcached, redis).
							 * Maybe desarrolla2/cache, doctrine/cache, or something under cache/cache on Packagist.org
							 */
							$ctx_ucf_today['header'] = 'UCF Today News';
							$ctx_ucf_today['anchors'] = SDES_Static::get_rss_links_and_titles( 'http://today.ucf.edu/feed/' );
							Render_Template::footer_links( $ctx_ucf_today );
						?>
					</div>
					<div class="col-md-4">
						<h2>Search</h2>
						<form action="http://google.cc.ucf.edu/search">
							<fieldset>
								<input type="hidden" name="output" value="xml_no_dtd">
								<input type="hidden" name="proxystylesheet" value="UCF_Main">
								<input type="hidden" name="client" value="UCF_Main">
								<input type="hidden" name="site" value="UCF_Main">
								<div class="input-group">
									<input type="text" class="form-control">
									<span class="input-group-btn">
										<input type="submit" class="btn" value="Search">
									</span>
								</div>
							</fieldset>
						</form>

						<h2>Contact</h2>
						<p>
							Student Development and Enrollment Services<br />
							Phone: 407-823-4625 &bull; Email: <a href="mailto:sdes@ucf.edu">sdes@ucf.edu</a><br />
							Location: <a href="http://map.ucf.edu/?show=1">Millican Hall 282</a>
						</p>
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