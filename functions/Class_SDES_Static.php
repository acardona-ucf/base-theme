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

}
