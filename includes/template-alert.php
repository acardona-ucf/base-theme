<?php
if ( 'alert' !== get_query_var('post_type') ) {
	echo do_shortcode( "[alert-list]" );
}
