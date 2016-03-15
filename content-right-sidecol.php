<?php
/*
Template Name: Content Page Right Sidecolumn
*/
require_once( get_stylesheet_directory().'/functions/class-sdes-helper.php' );
?>

<?php get_header('content'); ?>
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
endif;
get_footer();
