<?php
if ( 'alert' !== get_query_var('post_type') ) {
	// TODO: Allow alerts to be restricted to certain pages.
	echo do_shortcode( "[alert-list]" );
}
