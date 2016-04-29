<?php
/**
 * Helper functions that are specific to this project, and are not reusable.
 * @package Rev2015 WordPress Prototype
 */
namespace SDES\BaseTheme;
require_once( get_stylesheet_directory().'/functions/class-sdes-static.php' );
		use SDES\SDES_Static as SDES_Static;
/**
 * Container for helper methods specific to this repository/solution.
 * Static functions solving general problems should be added to the class SDES_Static.
 */
class SDES_Helper
{
	/**
	 * Retrieve data about an SDES department from the SDES Directory CMS.
	 * @param string $directory_cms_acronym  The department 'acronym' to match against.
	 * @param Array  $default_department     Set default values if they are not in the feed.
	 * @param string $uri                    The uri of the Directory CMS's json feed.
	 * @return Array   An associative array representing the department.
	 */
	public static function get_sdes_directory_department( $directory_cms_acronym,
		$default_department = null, $uri = 'http://directory.sdes.ucf.edu/feed/' ) {
		$department = array();
		if ( '' !== $directory_cms_acronym && !ctype_space( $directory_cms_acronym ) ) {
			$json = json_decode( file_get_contents( $uri ), $assoc = true );
			foreach ( $json['departments'] as $idx => $dept ) {
				if ( $directory_cms_acronym === $dept['acronym'] ) {
					$department = array_merge($default_department, $dept);
					break;
				}
			}
		} else {
			$department = array_merge( 
				array(
				'location' => array( 'building' => '', 'buildingNumber' => '', 'roomNumber' => '', ),
				'name' => '', 'acroynm' => '', 'phone' => '', 'fax' => '', 'email' => '', 'postOfficeBox' => '',
				'image' => '', 'offersPublicServices' => '', 'isDept' => '', 'functionalGroup' => '',
				'websites' => array( array( 'name' => '', 'slug' => '', 'uri' => '', ), ) ,
				'hours' => array( array( 'day' => '', 'open' => '', 'close' => '', ) ) ,
				'staff' => array( array( 'name' => '', 'position' => '', ) ) ,
				'socialNetworks' => array( array( 'name' => '', 'uri' => '', 'uid' => '', ) ) ,
				),
				$default_department
			);
		}
		return $department;
	}

	public static function Get_No_Posts_Message( $args = array() )
	{
		$defaults = array( 'echo' => true, );
		$args = array_merge( $defaults, $args );
		$no_posts = 
			( SDES_Static::Is_UserLoggedIn_Can( 'edit_posts' ) )
			? '<a class="text-danger adminmsg" style="color: red !important;"'
			. 'href="' . get_site_url() . '/wp-admin/">Admin Alert: %1$s</a>'
			: '<!-- %1$s -->';
		$output = sprintf( $no_posts, 'No posts were found.');
		if ( $args['echo'] ) {
			echo $output;
		}
		return $output;
	}
}
