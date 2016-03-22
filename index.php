<?php
use SDES\BaseTheme\SDES_Helper;

get_header();
?>
<!-- content area -->
<div class="container site-content" id="content">
	<?= get_template_part( 'includes/template', 'alert' ); ?>


<?php if (have_posts()) :
	 while (have_posts()) : the_post();
		the_content();
	endwhile;
else:
	require_once( get_stylesheet_directory().'/functions/class-sdes-helper.php' );
	SDES_Helper::Get_No_Posts_Message();
endif; ?>


</div> <!-- /DIV.container.site-content -->
<?php
get_footer();
