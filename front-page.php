<?php
use SDES\BaseTheme\SDES_Helper;

get_header();

$directory_cms_acronym = esc_attr(get_option('sdes_theme_settings_dir_acronym'));
		$departmentInfo = "<!-- Configure a department to show hours, phone, fax, email, and location. -->";
		if( null != $directory_cms_acronym && !ctype_space($directory_cms_acronym) ) {
			$departmentInfo = get_department_info( $directory_cms_acronym );
		}
?>
<?php
	$hideBillboard = false; // Could add an option for hiding Billboard in Theme Customizer.
	if ( $hideBillboard ) {
		// Could add an adminmsg here.
	} else {
		/* If using the WP Nivo Plugin, use the following code instead: */
		// if ( function_exists('show_nivo_slider') ) { show_nivo_slider(); } 
		echo do_shortcode( "[billboard-list]" );
	}
?>
<!-- content area -->
<div class="container site-content" id="content">
	<?= do_shortcode( '[alert-list show_all="true"]' ); ?>


<div class="row">
	<br>
	<div class="col-sm-8">
	 	<?php if ( have_posts() ) :
			while ( have_posts() ) : the_post();
				the_content();
			endwhile;
		else:
			$qNews = array('post_type' => 'news');
			$loop = new WP_Query($qNews);
			if ( $loop->have_posts() ) : ?>
				<h2 class="page-header">News and Announcements</h2>
				<?php echo do_shortcode("[news-list]"); 
			else:
				require_once( get_stylesheet_directory().'/functions/class-sdes-helper.php' );
				SDES_Helper::Get_No_Posts_Message();
			endif;
			wp_reset_query();
		endif; ?>
	</div>
	<div class="col-sm-4">
		<span id="departmentInfo"><?= $departmentInfo ?></span>
		<?php
			$sidebar = get_post_meta($post->ID, 'page_sidebar', $single=true);
			echo do_shortcode($sidebar);
		?>
	</div>	
</div>


</div> <!-- /DIV.container.site-content -->
<?php
get_footer();
