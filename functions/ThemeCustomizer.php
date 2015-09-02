<?php
// require_once('Class_SDES_Static.php');
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
}
add_action( 'customize_register', 'register_theme_customizer' );



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

