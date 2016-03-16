<?php
if(is_front_page()){
	get_header();
}else{
	get_header('content');
}

if (have_posts()) :
	 while (have_posts()) : the_post();
		the_content();
	endwhile;
else:
	require_once( get_stylesheet_directory().'/functions/class-sdes-helper.php' );
	SDES_Helper::Get_No_Posts_Message();
endif;

get_footer();
