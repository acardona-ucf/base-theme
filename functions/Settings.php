<?php

require_once( get_stylesheet_directory().'/functions/SettingsCallbacks.php' );

/**
 * Code that adds and registers Settings for a theme.
 * The focus of this file is configuration items that don't directly impact
 * the appearance of a page. Appearance settings should be accessible
 * from "Appearance > Customize", and configured in ThemeCustomizer.php.
 *
 * Settings/Options correspond to the "Settings" in Theme Customizer.
 */
//new options for admins//////////////////////////////////////////////

// Section - group of related fields/settings
// Field - the label and input area
// Setting - the actual value saved in the database.
// Option - low-level implementation of a setting.
// Page - container for a collection of sections
// Tabs - sugar for showing multiple sections

// WP Validation hook - pre-save
// WP Sanitize hook - post-save, pre-render

/*
Settings Functions - organizing/ordering
PAGES: menu, submenu, and options pages
    Functions: add_menu_page, add_submenu_page, add_options_page
    Hooks: admin_menu

SECTIONS, SETTINGS, AND FIELDS
    Functions: add_settings_sections, add_settings_field, register_setting
    Hooks: admin_init

CALLBACKS
    Functions: settings_errors, settings_fields, do_settings_sections, 
        submit_button, POST to options.php, get_options, echo
    Hooks: n/a (?)

Field-Callback-Option is added to a Page-Section.
*/

// TODO: Initialize theme settings on theme init (for SDES Directory values, initialize with default value of NULL)

/** Add an options page under settings: "Settings > SDES Theme Options" */
function options_page() {
    // Add page under regular settings area
    //add_options_page( $page_title, $menu_title, $capability,
    //                  $menu_slug, $function);
    add_options_page( 'SDES Theme Settings', 'SDES Theme Settings', 'manage_options',
                      'sdes_settings', 'sdes_settings_render' );
}
add_action( 'admin_menu', 'options_page' );


/** Register settings and fields to the 'sdes_settings' options page (id of its menu_slug). */
function option_page_settings() {
    // SETTINGS
    // TODO: Add sanitize callbacks for settings in 'sdes_setting_group'.
    // register_setting( $option_group, $option_name, $sanitize_callback );
    register_setting( 'sdes_setting_group', 'sdes_theme_settings_ga_id' );
    register_setting( 'sdes_setting_group', 'sdes_theme_settings_js' );
    register_setting( 'sdes_setting_group', 'sdes_theme_settings_js_lib' );
    register_setting( 'sdes_setting_group', 'sdes_theme_settings_css' );
    register_setting( 'sdes_setting_group', 'sdes_theme_settings_dir_acronym' );
    register_setting( 'sdes_setting_group', 'sdes_rev_2015-footer_content-left' );
    register_setting( 'sdes_setting_group', 'sdes_rev_2015-footer_content-center' );
    register_setting( 'sdes_setting_group', 'sdes_rev_2015-footer_content-right' );

    // SECTIONS
    // add_settings_section( $id, $title, $callback,
    //                       $page );
    add_settings_section( 'sdes_section_one', 'SDES Theme Settings', 'section_one_callback',
                          'sdes_settings' );

    // FIELDS - callbacks functions are defined in SettingsCallbacks.php.
    // add_settings_field( $id, $title, $callback,
    //                     $page, $section, $args );
    add_settings_field( 'sdes_theme_settings_ga_id', 'Google Analytics ID', 'google_analytics_id_callback',
                        'sdes_settings', 'sdes_section_one' );
    
    add_settings_field( 'sdes_theme_settings_js', 'javascript', 'javascript_callback',
                        'sdes_settings', 'sdes_section_one' );
    
    add_settings_field( 'sdes_theme_settings_js_lib', 'Javascript Libraries', 'javascript_libraries_callback',
                        'sdes_settings', 'sdes_section_one' );

    add_settings_field( 'sdes_theme_settings_css', 'CSS', 'css_callback',
                        'sdes_settings', 'sdes_section_one' );

    add_settings_field( 'sdes_theme_settings_dir_acronym', 'Acronym in SDES Directory/CMS', 'directory_cms_acronym_callback',
                        'sdes_settings', 'sdes_section_one' );

    add_settings_field( 'sdes_rev_2015-footer_content-left', 'Footer Static Content - Left', 'footer_content_left_callback',
                        'sdes_settings', 'sdes_section_one' );

    add_settings_field( 'sdes_rev_2015-footer_content-center', 'Footer Static Content - Center', 'footer_content_center_callback',
                        'sdes_settings', 'sdes_section_one' );

    add_settings_field( 'sdes_rev_2015-footer_content-right', 'Footer Static Content - Right', 'footer_content_right_callback',
                        'sdes_settings', 'sdes_section_one' );
}
    
add_action( 'admin_init', 'option_page_settings' );
/////////////////////////////////////////////////////////////////////

/** Add a Menu page to the Dashboard's left navigation (with an icon) and add subpages.*/
function menu_with_submenus() {
    // add_menu_page( $page_title, $menu_title, $capability, $menu_slug, $function, $icon_url, $position );
    add_menu_page( 
        'SDES Options',         // The text to be displayed in the browser title bar
        "SDES Options",         // The text to be used for the menu
        'manage_options',       // The required capability of users to access this menu
        'sdes_options',         // The slug by which this menu item is accessible
        'render_sdes_menu',    // The name of the function used to display the page content
        '//assets.sdes.ucf.edu/images/favicon_black.png' //  An icon to display besied the menu text
        // 78       // The position to innsert this menu item. Be careful not to hide another item!
    );

    // add_submenu_page( $parent_slug, $page_title, $menu_title, $capability,
    //                   $menu_slug, $function );
    add_submenu_page( 'sdes_options', 'Developer Settings', "Developer Settings", "manage_options",
                      "sdes_developer_settings", "render_developer_settings");

    add_submenu_page( 'sdes_options', 'Customize', 'Customize', 'edit_theme_options',
                      'sdes_customize', 'redirect_to_customize' );

    add_submenu_page( 'sdes_options', 'Tabbed Settings', "Tabbed Settings", "manage_options",
                      "tabbed_settings", "render_tabbed_settings");
}
add_action( 'admin_menu', 'menu_with_submenus' );

/** Render HTML for the menu page 'sdes_options' */
function render_sdes_menu() {
    ?>
        <br><br>
        This text should be displayed on /wp-admin/admin.php?page=sdes_options
        <br><br>
        Probably for non-developers who manage the site.  The other alternative is putting settings in the Theme Customizer.
        <br><br>
        Could add a "Super Editor" capability/role to access these "Super Editor Options".  That role would be more limited than "Administrator"", but more powerful than "Editor".
    <?php
}

/** Render HTML for the submenu page 'sdes_developer_settings'. */
function render_developer_settings() {
    sdes_settings_render();
}

/** Render HTML for the submenu page 'sdes_customize'. */
function redirect_to_customize() {
    $url = '/wp-admin/customize.php'; //TODO: make sure this works in subdirectory sites.
    ?>
    <script type="text/javascript">
        // TODO: Guard against redirect loop if previous location was $url.
        window.location = "<?=$url?>"
    </script>
    <a href="<?=$url?>"><?=$url?></a>
    <?php
}

/**
 * Render HTML for a given array of tabs. Helper function called by 'render_tabbed_settings'.
 * @param Array  $tabs  Array of tabs.  Each tab is a hash with the keys: 'text', 'slug', and 'option_group'.
 * @param String $active_tab  The slug of the take to render as the active tab.
 * WordPress CSS:
 *  .nav-tab-wrapper
 *  .nav-tab
 *  .nav-tab-active
*/
function render_tabbed_settings_nav_tabs(&$tabs, $active_tab) {
    ?>
    <h3 class="nav-tab-wrapper"> 
        <?php foreach ($tabs as $tab) { 
            $classes = "";
            if($tab['slug'] == $active_tab ) {
                $classes = "nav-tab-active"; 
            }
        ?>
            <a href="?page=tabbed_settings&slug=<?=$tab['slug']?>" class="nav-tab <?=$classes?>"><?=$tab['text']?></a>
        <?php } ?>
    </h3><!-- /.nav-tab-wrapper -->
    <?php
}

require_once( get_stylesheet_directory(). '/vendor/autoload.php' );
use Underscore\Types\Arrays;
/** Render HTML for the submenu page 'tabbed_settings'. */
function render_tabbed_settings() {
    if( isset( $_GET[ 'slug' ] ) ) {
        $active_tab = $_GET[ 'slug' ];
    }
    else if( isset( $_GET[ 'page' ] ) ) {
        $active_tab = $_GET[ 'page' ];
    }

    $tabs = array(
        array('text' => 'SDES Options', 'slug' => 'sdes_options', 'option_group' => 'sdes_setting_group'),
        array('text' => 'SDES Developer Settings', 'slug' => 'sdes_settings', 'option_group' => 'sdes_setting_group'),
        array('text' => 'Contact Information', 'slug' => 'contact', 'option_group' => ''),
        array('text' => 'Hours of Operation', 'slug' => 'hours', 'option_group' => '' ),
        array('text' => 'Social Networks', 'slug' => 'social', 'option_group' => '' ),
        array('text' => 'Footer (Feeds and Links)', 'slug' => 'footer', 'option_group' => '' ),
    );

    $active_tab_info = Arrays::find($tabs, function($tab, $active_tab) { 
        return $tab['slug'] == $active_tab; 
    });
    $option_group = $active_tab_info['option_group'];
    ?>
    Hello from render_tabbed_settings().
    <div class="wrap">
        <h2>Tabbed Settings/Options</h2>

        <?php
            // settings_errors( $setting, $sanitize, $hide_on_update );
            settings_errors(); ?>

        <!-- nav-tab-wrapper provided by WordPress -->
        <!-- <h3 class="nav-tab-wrapper"> 
            <a href="?page=tabbed_settings&slug=contact" class="nav-tab nav-tab-active">Contact Information</a>
            <a href="?page=tabbed_settings&slug=hours" class="nav-tab">Hours of Operation</a>
            <a href="?page=tabbed_settings&slug=social" class="nav-tab">Social Networks</a>
            <a href="?page=tabbed_settings&slug=footer" class="nav-tab">Footer (Feeds and Links)</a>
        </h3> --><!-- /.nav-tab-wrapper -->
        <?php render_tabbed_settings_nav_tabs( $tabs, $active_tab ); ?>

        <form action="options.php" method="POST">
            <?php if($active_tab == 'sdes_options') { ?>
                Note that render_sdes_menu() is not called here, but it is on <a href="/wp-admin/admin.php?page=sdes_options">/wp-admin/admin.php?page=sdes_options</a>
            <?php } ?>
            <?php
                settings_fields( $option_group );
                do_settings_sections( $active_tab );
                submit_button();
            ?>
        </form>
    </div>
    Bye from render_tabbed_settings().
    <?php   
}
