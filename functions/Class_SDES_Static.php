<?php

/**
 * Static Helper functions that are reusable in any PHP Site.
 */
class SDES_Static
{





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
