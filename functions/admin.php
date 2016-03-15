<?php

// @see https://github.com/UCF/Students-Theme/blob/d56183079c70836adfcfaa2ac7b02cb4c935237d/functions/admin.php#L41-L71
add_action( 'admin_menu', 'create_help_page' );
function create_help_page() {
	add_utility_page(
		__( 'Help' ), // $page_title,
		__( 'Help' ), // $menu_title,
		'edit_posts',     // $capability,
		'theme-help', // $menu_slug,
		'theme_help_page',  // $function,
		'dashicons-editor-help' // $icon_url
	);
}
function theme_help_page() {
	include( get_stylesheet_directory().'/includes/theme-help.php' );
}



add_action( 'init', 'register_navpill_dynamic_menus' );
function register_navpill_dynamic_menus() {
    // Add menu locations for all posts/pages that match the WP_Query below.
    $query_navpill_locations =
        new WP_Query(
            array(
                'post_type' => 'page'
            )
    );

    $nav_locations = array();
    while ( $query_navpill_locations->have_posts() ) {
        $query_navpill_locations->the_post();
        $key = SDES_Static::the_locationKey_navpills();
        $nav_locations[$key] = SDES_Static::the_locationValue_navpills();
    }
    wp_reset_postdata(); // Restore original Post Data

    register_nav_menus($nav_locations);
}



function customize_admin_bar_menu() {
    global $wp_admin_bar;

    $settings = SDES_Static::get_theme_mod_defaultIfEmpty(
                'sdes_theme_settings',
                array( 'directory_cms_acronym'=>'' ) );
    $dir_acronym = esc_attr( $settings['directory_cms_acronym'] );
    $office = 'slug/' . $dir_acronym;  //TODO: Update Directory to accept slugs for offices
    $office = 'details/51';

    $wp_admin_bar->add_menu( array(
        'id' => 'abm-sdes'
        , 'title' => 'SDES Directory'
        , 'href' => 'https://directory.sdes.ucf.edu/admin/office/' . $office
        , 'meta' => array( 
            'target' => '_blank' 
        )
    ));
}
add_action( 'admin_bar_menu', 'customize_admin_bar_menu', 65);



function customize_admin_theme() {
    wp_enqueue_style( 'admin-theme', get_stylesheet_directory_uri() . '/css/admin.css');
    wp_enqueue_script( 'admin-theme', get_stylesheet_directory_uri() . '/js/admin.js');
}
add_action('admin_enqueue_scripts','customize_admin_theme');
