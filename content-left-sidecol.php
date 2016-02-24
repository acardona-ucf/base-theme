<?php
/*
Template Name: Content Page Left Sidecolumn
*/
?>

<?php get_header('content'); ?>

<?php if (have_posts()) : while (have_posts()) : the_post(); ?>

	<div class="col-sm-4">
		<?php
			$prefix = SDES_Static::get_post_type( get_the_ID() ).'_';
			echo get_post_meta(get_the_ID(),  $prefix.'sidecolumn', true ); 
		?>
	</div>
	<div class="col-sm-8">

		<?php the_content(); ?>

	</div>
	
	

<?php endwhile; else: ?>

<?php endif; ?>

<?php get_footer(); ?>