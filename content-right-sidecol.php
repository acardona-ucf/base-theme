<?php
/*
Template Name: Content Page Right Sidecolumn
*/
use SDES\SDES_Static as SDES_Static;
require_once( get_stylesheet_directory().'/functions/class-sdes-helper.php' );
	use SDES\BaseTheme\SDES_Helper;
get_header();
?>
<!-- content area -->
<div class="container site-content" id="content">
	<?= get_template_part( 'includes/template', 'alert' ); ?>


<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
	<div class="col-sm-8">
		<?php the_content(); ?>
	</div>
	<div class="col-sm-4">
		<?php
			$prefix = SDES_Static::get_post_type( get_the_ID() ).'_';
			echo do_shortcode( get_post_meta(get_the_ID(),  $prefix.'sidecolumn', true ) ); 
		?>
	</div>
<?php endwhile;
else: 
	SDES_Helper::Get_No_Posts_Message();
endif; ?>


</div> <!-- /DIV.container.site-content -->
<?php
get_footer();
