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
	<?= get_template_part( 'includes/template', 'heading' ); ?>
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
