<?php

get_header('content');

if (have_posts()) :
	while (have_posts()) : the_post();
		the_content();
	endwhile;
else:
	echo "No posts were found.";
endif;

get_footer();
