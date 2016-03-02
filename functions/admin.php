<?php

// @see https://github.com/UCF/Students-Theme/blob/d56183079c70836adfcfaa2ac7b02cb4c935237d/functions/admin.php#L41-L71
add_action( 'admin_menu', 'create_help_page' );
function create_help_page() {
	add_utility_page(
		__( 'Help' ), // $page_title,
		__( 'Help' ), // $menu_title,
		'edit_posts',     // $capability,
		'theme-help', // $menu_slug,
		'theme_help_page',  // $function,
		'dashicons-editor-help' // $icon_url
	);
}
function theme_help_page() {
	include( get_stylesheet_directory().'/includes/theme-help.php' );
}

