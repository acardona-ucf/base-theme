<?php

/**
 * Static Helper functions that are reusable in any PHP Site.
 */
class SDES_Static
{

	// Set default value for a give key in $args, which is passed by reference.
	public static function set_default_keyValue(&$args, $key, $default_value){
		$args[$key] = (isset($args[$key])) ? $args[$key] : $default_value;
	}


	// TODO: Needs tests
	// Always include an area code, show only numbers and the dash symbol, always show 2 dashes.
	public static function sanitize_telephone_407($value)
	{
		$areaCode = '407';
		$value = preg_replace("/[^0-9-]/", "", $value); //Remove non-numeric, unless a dash

		// Prepend area code if necessary
		if( strlen($value) <= 8 ){
			$value = $areaCode . '-' . $value;
		}

		// Add first dash if missing
		$firstDash = 3;
		if($value[$firstDash] != '-') {
			$value = substr_replace($value, '-', $firstDash, 0);
		}

		// Add last dash if missing
		$lastDash = 7; //strlen($value)-5;
		if($value[$lastDash] != '-') {
			$value = substr_replace($value, '-', $lastDash, 0);
		}

		return $value;
	}



	/************************
	 * WordPress functions
	 ***********************/
	// Returns the default even if the value in the database is null or whitespace.
	public static function get_theme_mod_defaultIfEmpty($value, $default_to, $get_theme_mod='get_theme_mod') {
		$output = $get_theme_mod( $value, $default_to );  // Default if no value stored in database
		if (null == $output || ctype_space($output)) { $output = $default_to; } // If value in database is null or whitespace
		return $output;
	}

	// Check if user is both logged in and has a given capability.
	public static function Is_UserLoggedIn_Can($capability)
	{
		return is_user_logged_in() && current_user_can($capability);
	}

	// Get a string of class names for a WP_Post object and optionally apply a filter.
	public static function Get_ClassNames($wp_post, $filter_tag='')
	{
	    $classes = empty( $wp_post->classes ) ? array() : (array) $wp_post->classes;
	    $class_names = join( ' ', apply_filters( $filter_tag, array_filter($classes), $wp_post ) );
	    return $class_names;
	}

	// Generate a key for a post's navpill menu location. Used as the 'theme_location' by wp_nav_menu()'s $args parameter and by the "Currently set to:" in Customizer.
	public static function the_locationKey_navpills()
	{
		//Assume called within TheLoop
		return "pg-" . the_title_attribute(array('echo'=>false));
	}
	// Generate a value for a post's navpill menu location. This is the display text shown in "Menu Locations" and "Manage Locations".
	public static function the_locationValue_navpills()
	{
		//Assume called within TheLoop
		return "Page " . the_title_attribute(array('echo'=>false));
	}



	/** fallback_navpills_warning - Call from wp_nav_menu as the 'fallback_cb' for navpills locations.
	 *    Optionally show a warning for logged in users (if the navpills are missing).
	 *  $args - Accepts $args array used by wp_nav_menu (merged with any default values), plus the standard following params:
	 *  $args['echo'] - Standard echo param, output to stdout if true.
	 *  $args['warn'] - Boolean flag to display admin message (default to true).
	 *  $args['warn_message'] - Format string for warning message (where %1$s is expended to the 'theme_location').
	 *
	 *  Testing Overrides:
	 *  $shouldWarn - Override login and capabilities check.
	 *  $get_query_var - Override call to get_query_var.
	 *  $esc_attr = Override the sanitize function provided by WordPress (used in testing).
	 */
	public static function fallback_navpills_warning($args,
		$shouldWarn = null, $get_query_var='get_query_var', $esc_attr='esc_attr')
	{
		SDES_Static::set_default_keyValue($args, 'echo', false);
		SDES_Static::set_default_keyValue($args, 'warn', true);
		SDES_Static::set_default_keyValue($args, 'warn_message', 
			'<li><a class="text-warning adminmsg" style="color: #8a6d3b !important;" href="/wp-admin/nav-menus.php?action=locations#locations-%1$s">Admin Warning: No menu set for "%1$s" menu location.</a></li>'
		);

		if($args['depth'] != 1)	trigger_error("Calling 'fallback_navpills_warning' with a depth that is not 1. The SDES base-theme CSS does not currently support multi-level menus.");

		$shouldWarn = (isset($shouldWarn)) ? $shouldWarn 
			: SDES_Static::Is_UserLoggedIn_Can('edit_posts');

		//Note: caching implications for conditional output on '?preview=true'
		$pages = '';
		if($args['warn'] && !$get_query_var('preview') && $shouldWarn ) {
			$pages .= sprintf($args['warn_message'], $args['theme_location']);
		}

		$menu_id = $esc_attr($args['menu_id']);
		$menu_class = $esc_attr($args['menu_class']);
		$nav_menu = sprintf( $args['items_wrap'], $menu_id, $menu_class, $pages);
		if($args['echo']) {
			echo $nav_menu;
		}
		return $nav_menu;
	}

}
