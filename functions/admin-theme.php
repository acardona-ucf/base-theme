<?php
/**
 * Configure the Admin Dashboard (/wp-admin/).
 */

namespace SDES\BaseTheme\Admin;
use \WP_Query;
require_once( get_stylesheet_directory() . '/functions/class-sdes-static.php' );
	use SDES\SDES_Static as SDES_Static;
require_once( get_stylesheet_directory() . '/functions/class-sdes-helper.php' );
	use SDES\BaseTheme\SDES_Helper as SDES_Helper;

add_action( 'init', __NAMESPACE__.'\register_navpill_dynamic_menus' );
function register_navpill_dynamic_menus() {
	// Add menu locations for all posts/pages that match the WP_Query below.
	$query_navpill_locations = new WP_Query(
		array(
				'post_type' => 'page',
			)
	);

	$nav_locations = array();
	while ( $query_navpill_locations->have_posts() ) {
		$query_navpill_locations->the_post();
		$key = SDES_Helper::the_locationKey_navpills();
		$nav_locations[ $key ] = SDES_Helper::the_locationValue_navpills();
	}
	wp_reset_postdata(); // Restore original Post Data.

	register_nav_menus( $nav_locations );
}



function customize_admin_bar_menu() {
	global $wp_admin_bar;

	$settings = SDES_Static::get_theme_mod_defaultIfEmpty(
		'sdes_theme_settings',
	array( 'directory_cms_acronym' => '' ) );
				$dir_acronym = esc_attr( $settings['directory_cms_acronym'] );
				$office = 'slug/' . $dir_acronym;  // TODO: Update Directory to accept slugs for offices.
				$office = 'details/51';

				$wp_admin_bar->add_menu( array(
					'id' => 'abm-sdes',
					'title' => 'SDES Directory',
					'href' => 'https://directory.sdes.ucf.edu/admin/office/' . $office,
					'meta' => array(
					'target' => '_blank',
					),
				));
}
add_action( 'admin_bar_menu', __NAMESPACE__.'\customize_admin_bar_menu', 65 );




/**
 * Hide Appearance > Themes (/wp-admin/themes.php)
 * @see http://codex.wordpress.org/remove_submenu_page WP-Codex: remove_submenu_page()
 */
function remove_theme_submenu() {
	$page = remove_submenu_page( 'themes.php', 'themes.php' );
}
add_action( 'admin_menu', __NAMESPACE__.'\remove_theme_submenu', 999 );


/**
 * Hide links to "Themes.php" in adminbar (in the dropdown menu between "My Sites" and "Customize").
 * @see http://codex.wordpress.org/Function_Reference/remove_node WP-Codex: remove_node()
 */
function remove_theme_link_adminbar( $wp_admin_bar ) {
	$wp_admin_bar->remove_node( 'themes' );
}
add_action( 'admin_bar_menu', __NAMESPACE__.'\remove_theme_link_adminbar', 999 );
