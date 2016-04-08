<?php
/**
 * Display a single Post or Page, per the WordPress Template Hierarchy.
 */ 
get_header();
?>
<!-- content area -->
<div class="container site-content" id="content">
	<?= get_template_part( 'includes/template', 'alert' ); ?>


<?php if (have_posts()) :
	while (have_posts()) : the_post();
		get_template_part( 'includes/template', 'heading' );
		the_content();
	endwhile;
else:
	echo "No posts were found.";
endif; ?>


</div> <!-- /DIV.container.site-content -->
<?php
get_footer();
