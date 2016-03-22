<?php
/**
* Template Name: Content Full Width
*/
// TODO: Should template filename conventions be changed, e.g., start with `t_`?
use SDES\SDES_Static as SDES_Static;
require_once( get_stylesheet_directory().'/functions/class-sdes-helper.php' );
	use SDES\BaseTheme\SDES_Helper;
get_header();
?>
<!-- content area -->
<div class="container site-content" id="content">
	<?= get_template_part( 'includes/template', 'alert' ); ?>


<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
	<div class="page-header">
		<?php 
			// Re-register location for Page's navpill menu, in case it was not registered yet (e.g., moment-of-creation).
			$locKey = SDES_Static::the_locationKey_navpills();
			$locValue = SDES_Static::the_locationValue_navpills();
			register_nav_menu($locKey, $locValue);
			// Display navpill menu
			wp_nav_menu( array(
				'theme_location' => $locKey
				, 'depth' => 1
				, 'container' => 'ul'
				, 'menu_class' => 'nav nav-pills pull-right'
				, 'fallback_cb' => 'SDES\\SDES_Static::fallback_navpills_warning'
			));
		?>
		<h1 id="content-top"><?=the_title();?></h1>
	</div>
	<div class="col-sm-12">
		<?php the_content(); ?>
	</div>
<?php endwhile;
else: 
	SDES_Helper::Get_No_Posts_Message();
endif; ?>

</div> <!-- /DIV.container.site-content -->
<?php
get_footer();
