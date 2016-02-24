<?php
/**
 * Helper functions that are specific to this project, and are not reusable.
 * @package Rev2015 WordPress Prototype
 */

/**
 * Container for helper methods specific to this repository/solution.
 * Static functions solving general problems should be added to the class SDES_Static.
 */
class SDES_Helper
{
	/**
	 * Retrieve data about an SDES department from the SDES Directory CMS.
	 * @param string $directory_cms_acronym  The department 'acronym' to match against.
	 * @param string $uri                    The uri of the Directory CMS's json feed.
	 * @return Array   An associative array representing the department.
	 */
	public static function get_sdes_directory_department( $directory_cms_acronym,
		$uri = 'http://directory.sdes.ucf.edu/feed/' ) {
		$department = array();
		if ( '' !== $directory_cms_acronym && !ctype_space( $directory_cms_acronym ) ) {
			$json = json_decode( file_get_contents( $uri ), $assoc = true );
			foreach ( $json['departments'] as $idx => $dept ) {
				if ( $directory_cms_acronym === $dept['acronym'] ) {
					$department = $dept;
					break;
				}
			}
		}
		return $department;
	}
}
