<?php
namespace SDES\BaseTheme\ThemeCustomizer;
use \WP_Customize_Control;
use \WP_Customize_Color_Control;
use SDES\CustomizerControls\SDES_Customizer_Helper;
use SDES\CustomizerControls\Textarea_CustomControl;
use SDES\CustomizerControls\Phone_CustomControl;
// require_once('class-sdes-static.php');
	use SDES\SDES_Static as SDES_Static;
// require_once('Classes_WP_Customize_Control.php');

// Theme Customizer ///////////////////////////////////////////////////////////
// https://developer.wordpress.org/themes/advanced-topics/customizer-api/
// https://codex.wordpress.org/Theme_Customization_API
// http://codex.wordpress.org/Data_Validation
/*
	Theme Customizer - show changes with a more WYSIWIG interface.
	Some overlapping functionality needed to make interface smoother, but not required.
*/

// header('Pragma: no-cache');
// header('Cache-Control: private, no-cache, no-store, max-age=0, must-revalidate, proxy-revalidate');
// header('Expires: Tue, 04 Sep 2012 05:32:29 GMT');

/*
Theme Customizer Functions - organizing/ordering
Functions:
		$wp_customizer->add_panel
		$wp_customizer->add_section
		$wp_customizer->add_setting
		$wp_customizer->add_control
		Other verbs: get, remove
			$wp_customizer->get_panel();
			$wp_customizer->remove_section();

Hooks:
	customize_preview_init, wp_head, customize_register

Functions (used elsewhere):
		get_theme_mod, wp_title


PANELS, SECTIONS, SETTINGS, AND CONTROLS
Panel - grouping of sections, but another level/click away from user.
Section - group of settings (shown as an accordion element)
Setting - the value being stored
Control - representation of an html form element(s)
*/


//TODO: Investigate possible changes to panels and sections in WordPress 4.3.1.
/**
 * Defines all of the sections, settings, and controls for the various
 * options introduced into the Theme Customizer
 *
 * @param   object    $wp_customizer    A reference to the WP_Customize_Manager Theme Customizer
 * @since   1.0.0
 */
function register_theme_customizer( $wp_customizer ) {
	//add_section_DisplayOptions($wp_customizer);
	add_panel_DisplayOptions($wp_customizer);

	add_section_ContactOptions($wp_customizer);

	add_to_section_TitleAndTagline($wp_customizer);

	add_section_news_options( $wp_customizer );

	add_section_social_options($wp_customizer);

	add_section_footer_options( $wp_customizer );
}
add_action( 'customize_register', __NAMESPACE__.'\register_theme_customizer' );

function add_section_DisplayOptions( $wp_customizer, $args = null) {
	// TODO: define $args defaults here
	/**
	*  pseudocoode:
	*		$args['section_id'] = $args['section_id'] ?? "defaultValue";
	*		Helper Function: array_default($args, 'section_id', 'defaultValue');
	*		Functional:
	*			$array_default_args = array_default($args);
	*			$array_default_args( array( 'section_id' => 'defaultValue', ) );
	*/

	$wp_customizer->add_section(
		'tctheme_display_options',
		array(
			'title'    => 'Display Options',
			'priority' => 200,
			'panel' => $args['panelId'],
		)
	);
	
	$wp_customizer->add_setting(
		'tctheme_link_color',
		array(
			'default'    =>  '#000000',
			'transport'  =>  'refresh',
	        //'transport'  =>  'postMessage',  // refresh (default) or postMessage
	        // 'sanitize_callback' => '',
		)
	);
	$wp_customizer->add_control(
		new WP_Customize_Color_Control(
			$wp_customizer,
			'tctheme_link_color',
			array(
				'label'    => 'Title Link Color (Site Title and Tagline)',
				'section'  => 'tctheme_display_options',
				'settings' => 'tctheme_link_color'
			)
		)
	);
}
require_once("ThemeCustomizer_DisplayOptions.php");


function add_panel_DisplayOptions( $wp_customizer, $args = null ) {
	$panelId = 'pnl_displayOptions';

	$wp_customizer->add_panel(
		$panelId,
		array(
		    'title' => 'Front Page Stuff',
		    'description' => 'Stuff that you can change about the Front Page',
		    'priority' => 10,
		    //'active_callback' => 'is_front_page',
		)
	);

	$args['panelId'] = $panelId;
	add_section_DisplayOptions($wp_customizer, $args);
}

//TODO: Convert to using SDES_Customizer_Helper
require_once('Classes_WP_Customize_Control.php');
require_once('Class_SDES_Customizer_Helper.php');
function add_section_ContactOptions( $wp_customizer, $args = null) {
	// TODO: set defaults using SDES_Static::set_default_keyValue(). 
	$args['panelId'] = (isset($args['panelId'])) ? $args['panelId'] : '';
	$args['hours-transport'] = (isset($args['hours-transport'])) ? $args['hours-transport'] : 'refresh';
	$args['phone-transport'] = (isset($args['phone-transport'])) ? $args['phone-transport'] : 'postMessage';
	$args['fax-transport'] = (isset($args['fax-transport'])) ? $args['fax-transport'] : 'refresh';

	$args['sdes_rev_2015-email']['transport'] = 'refresh';
	$args['sdes_rev_2015-email']['default'] = 'StudentDevelopmentEnrollmentServices@ucf.edu';
	
	$section = 'sdes_rev_2015-contact_options';
	$wp_customizer->add_section(
		$section,
		array(
			'title'    => 'Contact Information',
			'priority' => 250,
			'panel' => $args['panelId'],
		)
	);

	// TODO: clean up initializing default args arrays.  These must be initialized if other fields are set to avoid an index error.
	// e.g., `SDES_Static::set_default_args_arrays( $args, ['sdes_rev_2015-departmentName', 'sdes_rev_2015-buildingName', 'sdes_rev_2015-buildingNumber', 'sdes_rev_2015-roomNumber'] );`
	SDES_Static::set_default_keyValue( $args, 'sdes_rev_2015-departmentName', array() );
	SDES_Static::set_default_keyValue( $args, 'sdes_rev_2015-buildingName', array() );
	SDES_Static::set_default_keyValue( $args, 'sdes_rev_2015-buildingNumber', array() );
	SDES_Static::set_default_keyValue( $args, 'sdes_rev_2015-roomNumber', array() );

	$departmentName_args = $args['sdes_rev_2015-departmentName'];
	SDES_Static::set_default_keyValue($departmentName_args, 'default', get_bloginfo( 'name' ) );
	$buildingName_args = $args['sdes_rev_2015-buildingName'];
	$buildingNumber_args = $args['sdes_rev_2015-buildingNumber'];
	$roomNumber_args = $args['sdes_rev_2015-roomNumber'];

	// departmentName
	SDES_Customizer_Helper::add_setting_and_control('WP_Customize_Control', // Control Type
		$wp_customizer,			// WP_Customize_Manager
		'sdes_rev_2015-departmentName',	// id
		'Department Name',		// label
		$section,				// section
		$departmentName_args	// arguments array
	);


	// Hours
	$wp_customizer->add_setting(
		'sdes_rev_2015-hours',
		array(
			'default'    =>  'Mon-Fri: 8:00am - 5:00pm',
			'transport'  =>  $args['hours-transport'],  // refresh (default) or postMessage
			// 'sanitize_callback' => '',
			// 'sanitize_js_callback' => '',
			// 'capability' => '',
		)
	);
	$wp_customizer->add_control(
		new Textarea_CustomControl(
			$wp_customizer,
			'sdes_rev_2015-hours',
			array(
				'label'    => ('Hours'),
				'section'  => $section,
				'settings' => 'sdes_rev_2015-hours',
				'type' => 'text',
			)
		)
	);
	

	// Phone
	$wp_customizer->add_setting(
		'sdes_rev_2015-phone',
		array(
			'default'    =>  '407-823-4625',
			'transport'  =>  $args['phone-transport'],  // refresh (default) or postMessage
			'sanitize_callback' => 'SDES\\SDES_Static::sanitize_telephone_407',
			'sanitize_js_callback' => 'SDES\\SDES_Static::sanitize_telephone_407',
			// 'capability' => '',
		)
	);
	$wp_customizer->add_control(
		new WP_Customize_Control(
			$wp_customizer,
			'sdes_rev_2015-phone',
			array(
				'label'    => ('Phone {{' . $args["phone-transport"] . '}}'),
				'section'  => $section,
				'settings' => 'sdes_rev_2015-phone',
				'type' => 'text',
			)
		)
	);


	// Fax
	$wp_customizer->add_setting(
		'sdes_rev_2015-fax',
		array(
			'default'    =>  '407-823-2969',
	        'transport'  =>  $args['fax-transport'],  // refresh (default) or postMessage
	        'sanitize_callback' => 'SDES\\SDES_Static::sanitize_telephone_407',
	        'sanitize_js_callback' => 'SDES\\SDES_Static::sanitize_telephone_407',
	        // 'capability' => '',
		)
	);
	$wp_customizer->add_control(
		// new WP_Customize_Control(
		new Phone_CustomControl(
			$wp_customizer,
			'sdes_rev_2015-fax',
			array(
				'label'    => ('Fax {{' . $args["fax-transport"] . '}}'),
				'section'  => $section,
				'settings' => 'sdes_rev_2015-fax',
				'type' => 'text',
			)
		)
	);


	// Email
	SDES_Customizer_Helper::add_setting_and_control('WP_Customize_Control', // Control Type
		$wp_customizer,			// WP_Customize_Manager
		'sdes_rev_2015-email',	// id
		'Email',				// label
		$section,				// section
		$args['sdes_rev_2015-email']	// arguments array
	);

	// buildingName
	SDES_Customizer_Helper::add_setting_and_control('WP_Customize_Control', // Control Type
		$wp_customizer,					// WP_Customize_Manager
		'sdes_rev_2015-buildingName',	// id
		'Building Name',				// label
		$section,						// section
		$buildingName_args				// arguments array
	);

	// buildingNumber
	SDES_Customizer_Helper::add_setting_and_control('WP_Customize_Control', // Control Type
		$wp_customizer,					// WP_Customize_Manager
		'sdes_rev_2015-buildingNumber',	// id
		'Building Number',				// label
		$section,						// section
		$buildingNumber_args			// arguments array
	);

	// roomNumber
	SDES_Customizer_Helper::add_setting_and_control('WP_Customize_Control', // Control Type
		$wp_customizer,					// WP_Customize_Manager
		'sdes_rev_2015-roomNumber',		// id
		'Room Number',					// label
		$section,						// section
		$roomNumber_args				// arguments array
	);
}



// TODO: should tagline be called description to match the built-in Theme Customizer name? And/or should CSS .site-subtitle be updated?
function add_to_section_TitleAndTagline( $wp_customizer, $args = null) {
	$section = 'title_tagline';

	$taglineURL_args = $args['sdes_rev_2015-taglineURL'];
	SDES_Static::set_default_keyValue($taglineURL_args, 'transport', 'postMessage');
	SDES_Static::set_default_keyValue($taglineURL_args, 'default', 'http://www.sdes.ucf.edu/');
	SDES_Static::set_default_keyValue($taglineURL_args, 'sanitize_callback', 'esc_url');

	SDES_Customizer_Helper::add_setting_and_control('WP_Customize_Control', //Control Type
		$wp_customizer,			// WP_Customize_Manager
		'sdes_rev_2015-taglineURL',	// id
		'Tagline URL',				// label
		$section,					// section
		$taglineURL_args			// arguments array
	);
}

function add_section_news_options( $wp_customizer, $args = array() ) {
	$args = array_merge(array(
			'panelId' => null, 'sdes_rev_2015-newsArchiveUrl' => array(),
		), $args );

	/* SECTION */
	$section = 'sdes_rev_2015-news_options';
	$wp_customizer->add_section(
		$section,
		array(
			'title'    => 'News Archives',
			'priority' => 275,
			'panel' => $args['panelId'],
		)
	);

	/* ARGS */
	$newsArchiveUrl_defaults = array(
		'sanitize_callback' => 'esc_url',
		'sanitize_js_callback' => 'esc_url',
		'control_type' => 'url',
	);
	$newsArchiveUrl_args = array_merge( $newsArchiveUrl_defaults, $args['sdes_rev_2015-newsArchiveUrl'] );
	

	/* FIELDS */
	// Facebook
	SDES_Customizer_Helper::add_setting_and_control('WP_Customize_Control', //Control Type
		$wp_customizer,			// WP_Customize_Manager
		'sdes_rev_2015-newsArchiveUrl',	// id
		'News Archive URL',		// label
		$section,				// section
		$newsArchiveUrl_args	// arguments array
	);
}

/** Register the social_options section, add settings and controls. */
function add_section_social_options( $wp_customizer, $args = null) {
	/* SECTION */
	$section = 'sdes_rev_2015-social_options';
	$wp_customizer->add_section(
		$section,
		array(
			'title'    => 'Social',
			'priority' => 300,
			'panel' => $args['panelId'],
		)
	);

	/* ARGS */
	// TODO: Sanitize social links.
	$facebook_args = $args['sdes_rev_2015-facebook'];
	SDES_Static::set_default_keyValue($facebook_args, 'sanitize_callback', 'esc_url');
	SDES_Static::set_default_keyValue($facebook_args, 'sanitize_js_callback', 'esc_url');

	$twitter_args = $args['sdes_rev_2015-twitter'];
	SDES_Static::set_default_keyValue($twitter_args, 'sanitize_callback', 'esc_url');
	SDES_Static::set_default_keyValue($twitter_args, 'sanitize_js_callback', 'esc_url');

	$youtube_args = $args['sdes_rev_2015-youtube'];
	SDES_Static::set_default_keyValue($youtube_args, 'sanitize_callback', 'esc_url');
	SDES_Static::set_default_keyValue($youtube_args, 'sanitize_js_callback', 'esc_url');

	/* FIELDS */
	// Facebook
	SDES_Customizer_Helper::add_setting_and_control('WP_Customize_Control', //Control Type
		$wp_customizer,			// WP_Customize_Manager
		'sdes_rev_2015-facebook',	// id
		'Facebook',				// label
		$section,				// section
		$facebook_args			// arguments array
	);

	// Twitter
	SDES_Customizer_Helper::add_setting_and_control('WP_Customize_Control', //Control Type
		$wp_customizer,			 // WP_Customize_Manager
		'sdes_rev_2015-twitter', // id
		'Twitter',				 // label
		$section,				 // section
		$twitter_args			 // arguments array
	);

	// Youtube
	SDES_Customizer_Helper::add_setting_and_control('WP_Customize_Control', //Control Type
		$wp_customizer,			 // WP_Customize_Manager
		'sdes_rev_2015-youtube', // id
		'Youtube',				 // label
		$section,				 // section
		$twitter_args			 // arguments array
	);
}


function add_section_footer_options( $wp_customizer, $args = null) {
	/* SECTION */
	$section = 'sdes_rev_2015-footer_options';
	$wp_customizer->add_section(
		$section,
		array(
			'title'    => 'Footer',
			'priority' => 350,
			'panel' => $args['panelId'],
		)
	);

	/* ARGS */
	SDES_Static::set_default_keyValue( $args, 'sdes_rev_2015-footer_header-left', array() );
	SDES_Static::set_default_keyValue( $args, 'sdes_rev_2015-footer_showLinks-left', array() );
	SDES_Static::set_default_keyValue( $args, 'sdes_rev_2015-footer_feed-left', array() );
	SDES_Static::set_default_keyValue( $args, 'sdes_rev_2015-footer_header-center', array() );
	SDES_Static::set_default_keyValue( $args, 'sdes_rev_2015-footer_showLinks-center', array() );
	SDES_Static::set_default_keyValue( $args, 'sdes_rev_2015-footer_feed-center', array() );

	$left_header_args = $args['sdes_rev_2015-footer_header-left'];
	$left_showLinks_args = $args['sdes_rev_2015-footer_showLinks-left'];
	$left_showLinks_args['control_type'] = 'checkbox';
	$left_feed_args = $args['sdes_rev_2015-footer_feed-left'];
	SDES_Static::set_default_keyValue($left_feed_args, 'sanitize_callback', 'esc_url');
	SDES_Static::set_default_keyValue($left_feed_args, 'sanitize_js_callback', 'esc_url');

	$center_header_args = $args['sdes_rev_2015-footer_header-center'];
	$center_showLinks_args = $args['sdes_rev_2015-footer_showLinks-center'];
	$center_showLinks_args['control_type'] = 'checkbox';
	$center_feed_args = $args['sdes_rev_2015-footer_feed-center'];
	SDES_Static::set_default_keyValue($center_feed_args, 'sanitize_callback', 'esc_url');
	SDES_Static::set_default_keyValue($center_feed_args, 'sanitize_js_callback', 'esc_url');


	/* FIELDS */
	// Left Footer Header
	SDES_Customizer_Helper::add_setting_and_control('WP_Customize_Control', //Control Type
		$wp_customizer,			// WP_Customize_Manager
		'sdes_rev_2015-footer_header-left', // id
		'Left Footer Header', // label
		$section,				// section
		$left_header_args		// arguments array
	);

	// Left Footer - Feed
	SDES_Customizer_Helper::add_setting_and_control('WP_Customize_Control', //Control Type
		$wp_customizer,			// WP_Customize_Manager
		'sdes_rev_2015-footer_feed-left', // id
		'Left Feed URL (RSS)', // label
		$section,				// section
		$left_feed_args			// arguments array
	);

	// Left Footer - Show Links
	SDES_Customizer_Helper::add_setting_and_control('WP_Customize_Control', //Control Type
		$wp_customizer,			// WP_Customize_Manager
		'sdes_rev_2015-footer_showLinks-left', // id
		'(Left) Show menu instead of feed?', // label
		$section,				// section
		$left_showLinks_args	// arguments array
	);

	// Center Footer Header
	SDES_Customizer_Helper::add_setting_and_control('WP_Customize_Control', //Control Type
		$wp_customizer,			// WP_Customize_Manager
		'sdes_rev_2015-footer_header-center', // id
		'Center Footer Header', // label
		$section,				// section
		$center_header_args		// arguments array
	);

	// Center Footer - Feed
	SDES_Customizer_Helper::add_setting_and_control('WP_Customize_Control', //Control Type
		$wp_customizer,			// WP_Customize_Manager
		'sdes_rev_2015-footer_feed-center', // id
		'Center Feed URL (RSS)', // label
		$section,				// section
		$center_feed_args		// arguments array
	);

	// Center Footer - Show Links
	SDES_Customizer_Helper::add_setting_and_control('WP_Customize_Control', //Control Type
		$wp_customizer,			// WP_Customize_Manager
		'sdes_rev_2015-footer_showLinks-center', // id
		'(Center) Show links instead of feed?',  // label
		$section,				// section
		$center_showLinks_args			// arguments array
	);
}

// Allow AJAX updates to theme from Theme Customizer interface by
// using the Theme Customizer API in javascript.
// Enables $wp_customizer->add_setting() with 'transport'=>'postMessage'
/**
 * Registers and enqueues the `theme-customizer.js` file responsible
 * for handling the transport messages for the Theme Customizer.
 *
 * @package tctheme
 * @since   1.0.0
 */
function tctheme_customizer_live_preview() {
	
	wp_enqueue_script(
	    'theme-customizer-postMessage',
	    get_template_directory_uri() . '/js/theme-customizer.js',
	    array( 'jquery', 'customize-preview' ),
	    '1.0.0',
	    true
	);
	
}
add_action( 'customize_preview_init', __NAMESPACE__.'\tctheme_customizer_live_preview' );
