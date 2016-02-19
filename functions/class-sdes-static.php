<?php
/**
 * Static Helper functions that are reusable in any PHP Site.
 * @package SDES Static - Rev2015 WordPress Prototype
 */

/**
 * Container for reusable static functions (i.e., the same parameters should always return the same output).
 */
class SDES_Static
{

	/**
	 * Set default value for a give key in $args, which is passed by reference.
	 * @param array  $args    An args array (that is passed in by reference).
	 * @param string $key     The key to set.
	 * @param mixed  $default_value   A default value for the key if it is not already set.
	 */
	public static function set_default_keyValue( &$args, $key, $default_value ) {
		$args[ $key ] = (isset( $args[ $key ] ))
			 ? $args[ $key ]
			 : $default_value;
	}


	// TODO: add tests, try-catch block.
	/**
	 * Return a collection links and titles from an RSS feed.
	 * @param string           $uri           The uri of the RSS feed.
	 * @param int              $max_count         The number of items to return.
	 * @param int              $char_limit    Limit the number of characters in the titles, add &hellip; if over.
	 * @param SimpleXMLElement $xml           Reuse an existing SimpleXMLElement.
	 * @return Array A collection of anchors (array of arrays). Each anchor has the keys: 'link', 'title'.
	 */
	public static function get_rss_links_and_titles( $uri,
		$max_count = 8, $char_limit = 45,
		$xml = null ) {
		if ( null === $xml ) { $xml = simplexml_load_file( $uri ); }

		$output = array();
		$i = 0;  // TODO: refactor with generator pattern when VCCW upgrades to PHP 5.5+.
		foreach ( $xml->channel->item  as $idx => $item ) if ( $i++ < $max_count ) {
			$title_truncated
				= ( strlen( $item->title ) > $char_limit )
					? substr( $item->title, 0, $char_limit ) . '&hellip;'
					: (string) $item->title;
			$output[] = [
				'link' => (string) $item->link,
				'title' => $title_truncated,
			];
		}
		return $output;
	}


	// TODO: Needs tests.
	/**
	 * Always include an area code, show only numbers and the dash symbol, always show 2 dashes.
	 * @param string $value    The value to be sanitized, as passed back by sanitize_callback.
	 */
	public static function sanitize_telephone_407( $value ) {
		$areaCode = '407';
		$value = preg_replace( '/[^0-9-]/', '', $value ); // Remove non-numeric, unless a dash.

		// Prepend area code if necessary.
		if ( strlen( $value ) <= 8 ) {
			$value = $areaCode . '-' . $value;
		}

		// Add first dash if missing.
		$firstDash = 3;
		if ( '-' !== $value[ $firstDash ]  ) {
			$value = substr_replace( $value, '-', $firstDash, 0 );
		}

		// Add last dash if missing.
		$lastDash = 7; // strlen($value)-5; //.
		if ( '-' !== $value[ $lastDash ] ) {
			$value = substr_replace( $value, '-', $lastDash, 0 );
		}

		return $value;
	}



	/**
	 * *********************
	 * WordPress functions
	 ***********************/
	/**
	 * Returns the default even if the value in the database is null, whitespace, or an empty string.
	 * @param string $value				The theme modification name to pass to get_theme_mod.
	 * @param string $default_to		Default value to return.
	 * @param string $get_theme_mod		Reference to the get_theme_mod function, or a mock for testing.
	 */
	public static function get_theme_mod_defaultIfEmpty( $value, $default_to, $get_theme_mod = 'get_theme_mod' ) {
		$output = $get_theme_mod( $value, $default_to );  // Default if no value stored in database.
		// Default if value in db is null, empty string, or whitespace.
		if ( null === $output || '' === $output || ctype_space( $output ) ) { $output = $default_to; }
		return $output;
	}

	/**
	 * Check if user is both logged in and has a given capability.
	 * @param string $capability	A capability or role name to pass to current_user_can.
	 */
	public static function Is_UserLoggedIn_Can( $capability ) {
		return is_user_logged_in() && current_user_can( $capability );
	}

	/**
	 * Get a string of class names for a WP_Post object and optionally apply a filter.
	 * @param object $wp_post	The WP_Post object whose classes will be retrieved.
	 * @param string $filter_tag	A filter to pass to apply_filters.
	 */
	public static function Get_ClassNames( $wp_post, $filter_tag = '' ) {
		$classes = empty( $wp_post->classes ) ? array() : (array) $wp_post->classes;
		$class_names = join( ' ', apply_filters( $filter_tag, array_filter( $classes ), $wp_post ) );
		return $class_names;
	}

	/**
	 * Generate a key for a post's navpill menu location.
	 * Used as the 'theme_location' by wp_nav_menu()'s $args parameter and by the "Currently set to:" in Customizer.
	 */
	public static function the_locationKey_navpills() {
		// Assume called within TheLoop.
		return 'pg-' . the_title_attribute( array( 'echo' => false ) );
	}
	/**
	 * Generate a value for a post's navpill menu location.
	 * This is the display text shown in "Menu Locations" and "Manage Locations".
	 */
	public static function the_locationValue_navpills() {
		// Assume called within TheLoop.
		return 'Page ' . the_title_attribute( array( 'echo' => false ) );
	}



	/**
	 * Fallback_navpills_warning - Call from wp_nav_menu as the 'fallback_cb' for navpills locations.
	 *    Optionally show a warning for logged in users (if the navpills are missing).
	 *
	 * @param array  $args  Accepts $args array used by wp_nav_menu (merged with any default values), plus the standard following params:
	 *  $args['echo'] - Standard echo param, output to stdout if true.
	 *  $args['warn'] - Boolean flag to display admin message (default to true).
	 *  $args['warn_message'] - Format string for warning message (where %1$s is expended to the 'theme_location').
	 *
	 * Testing Overrides.
	 * @param bool   $shouldWarn		Override login and capabilities check.
	 * @param string $get_query_var		Call to get_query_var.
	 * @param string $esc_attr			Override the sanitize function provided by WordPress (used in testing).
	 */
	public static function fallback_navpills_warning( $args,
		$shouldWarn = null, $get_query_var = 'get_query_var', $esc_attr = 'esc_attr' ) {
		SDES_Static::set_default_keyValue( $args, 'echo', false );
		SDES_Static::set_default_keyValue( $args, 'warn', true );
		SDES_Static::set_default_keyValue( $args, 'warn_message',
			'<li><a class="text-warning adminmsg" style="color: #8a6d3b !important;" href="/wp-admin/nav-menus.php?action=locations#locations-%1$s">Admin Warning: No menu set for "%1$s" menu location.</a></li>'
		);

		if ( 1 !== $args['depth'] ) {
			trigger_error( "Calling 'fallback_navpills_warning' with a depth that is not 1. The SDES base-theme CSS does not currently support multi-level menus." ); }

		$shouldWarn = (isset( $shouldWarn )) ? $shouldWarn
			: SDES_Static::Is_UserLoggedIn_Can( 'edit_posts' );

		// Note: caching implications for conditional output on '?preview=true'.
		$pages = '';
		if ( $args['warn'] && ! $get_query_var('preview') && $shouldWarn ) {
			$pages .= sprintf( $args['warn_message'], $args['theme_location'] );
		}

		$menu_id = $esc_attr($args['menu_id']);
		$menu_class = $esc_attr($args['menu_class']);
		$nav_menu = sprintf( $args['items_wrap'], $menu_id, $menu_class, $pages );
		if ( $args['echo'] ) {
			echo $nav_menu;
		}
		return $nav_menu;
	}

	/**
	 * Fallback_navbar_list_pages - Call from wp_nav_menu as the 'fallback_cb'.
	 *   Allow graceful failure when menu is not set by showing a formatted listing of pages
	 *   instead of the default wp_page_menu output.
	 *
	 *  @param array  $args	 Accepts $args array used by wp_nav_menu (merged with any default values), plus the standard following params:
	 *  $args['number'] - Number of pages to pull from wp_list_pages.
	 *  $args['echo'] - Standard echo param, output to stdout if true.
	 *  $args['warn'] - Boolean flag to display admin message (default to true).
	 *  $args['warn_message'] - Format string for warning message (where %1$s is expended to the 'theme_location').
	 *
	 *  Testing Overrides.
	 *  @param bool   $shouldWarn - Override login and capabilities check.
	 *  @param string $get_query_var	Override call to get_query_var.
	 *  @param string $wp_list_pages	Override the call to wp_list_pages (Returns a string containing li>a elements).
	 *  @param string $esc_attr			Override the sanitize function provided by WordPress (used in testing).
	 */
	public static function fallback_navbar_list_pages( $args,
		$shouldWarn = null, $get_query_var = 'get_query_var', $wp_list_pages = 'wp_list_pages', $esc_attr = 'esc_attr' ) {
		SDES_Static::set_default_keyValue( $args, 'number', 6 );
		SDES_Static::set_default_keyValue( $args, 'echo', false );
		SDES_Static::set_default_keyValue( $args, 'warn', true );
		SDES_Static::set_default_keyValue( $args, 'warn_message',
			'<li><a class="text-danger adminmsg" style="color: red !important;" href="/wp-admin/nav-menus.php?action=locations#locations-%1$s">Admin Alert: Missing "%1$s" menu location.</a></li>'
		);

		if ( 1 !== $args['depth'] ) {
			trigger_error( "Calling 'fallback_navbar_list_pages' with a depth that is not 1. The SDES base-theme CSS does not currently support multi-level menus." ); }

		$pages = $wp_list_pages(array(
			'echo' => false,
			'title_li' => '',
			'depth' => ( $args['depth'] ),
			'number' => ( $args['number'] ),
		));

		$shouldWarn = (isset( $shouldWarn )) ? $shouldWarn
			: SDES_Static::Is_UserLoggedIn_Can( 'edit_posts' );

		// Note: caching implications for conditional output on '?preview=true'.
		if ( $args['warn'] && ! $get_query_var('preview') && $shouldWarn ) {
			$pages .= sprintf( $args['warn_message'], $args['theme_location'] );
		}

		$menu_id = $esc_attr($args['menu_id']);
		$menu_class = $esc_attr($args['menu_class']);
		$nav_menu = sprintf( $args['items_wrap'], $menu_id, $menu_class, $pages );
		if ( $args['echo'] ) {
			echo $nav_menu;
		}
		return $nav_menu;
	}
}
