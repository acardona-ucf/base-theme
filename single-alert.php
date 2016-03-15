<?php
// Only content managers and admins should be able to see single alerts, usually as a preview.
if (! SDES_Static::Is_UserLoggedIn_Can('edit_posts') ) {
	wp_redirect( site_url() );
}

get_header('content'); 

// NOTE: Metadata fields don't have revisions, so the alert color will not be updated in previews.
// For a possible solution, see the following core plugin under development: https://wordpress.org/plugins/wp-post-meta-revisions/
?>

<h2 class="page-header">Alert preview below:</h2>

<?php
if (have_posts()) :
	while (have_posts()) : the_post(); 

		global $post;
		echo Alert::toHTML( $post );

	endwhile;
else:
	SDES_Helper::Get_No_Posts_Message();
endif;

get_footer();
