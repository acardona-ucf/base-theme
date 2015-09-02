<?php
/*
Template Name: Content Page Right Sidebar
*/
?>

<?php get_header('content'); ?>
<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
	<div class="col-sm-8">
		<?php the_content(); ?>
	</div>
	<div class="col-sm-4">
		<h2>sidebar stuffs</h2>
	</div>
<?php endwhile; else: ?>
<?php endif; ?>
<?php get_footer(); ?>