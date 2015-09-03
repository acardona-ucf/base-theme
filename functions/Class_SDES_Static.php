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
	public static function get_theme_mod_defaultIfEmpty($value, $default_to) {
		$output = get_theme_mod( $value, $default_to );  // Default if no value stored in database
		if (null == $output || ctype_space($output)) { $output = $default_to; } // If value in database is null or whitespace
		return $output;
	}


}
