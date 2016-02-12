<?php
/**
 * Display the "Field" (labels and input areas) for a Setting/Option.
 * Corresponds to the "Control"s in Theme Customizer.
 */

require_once('class-sdes-static.php');

function section_one_callback() {
    echo 'Some help text goes here.';
}

function google_analytics_id_callback() {
    $sdes_theme_settings_ga_id = esc_attr( get_option('sdes_theme_settings_ga_id', '') );
    echo "<input type='text' name='sdes_theme_settings_ga_id' value='$sdes_theme_settings_ga_id' />";
}

function javascript_callback() {
    $sdes_theme_settings_js = esc_attr( get_option('sdes_theme_settings_js', '') );
    echo "<textarea name='sdes_theme_settings_js' >$sdes_theme_settings_js</textarea>";
}

function javascript_libraries_callback() {
    $sdes_theme_settings_js_lib = esc_attr( get_option('sdes_theme_settings_js_lib', '') );
    echo "<input type='text' name='sdes_theme_settings_js_lib' value='$sdes_theme_settings_js_lib' />";
}

function css_callback() {
    $sdes_theme_settings_css = esc_attr( get_option('sdes_theme_settings_css', '') );
    echo "<input type='text' name='sdes_theme_settings_css' value='$sdes_theme_settings_css' />";
}

function directory_cms_acronym_callback() {
    $sdes_theme_settings_dir_acronym = esc_attr( get_option('sdes_theme_settings_dir_acronym', '') );
    echo "<input type='text' name='sdes_theme_settings_dir_acronym' value='$sdes_theme_settings_dir_acronym' />";
}

function sdes_settings_render() {
    ?>
    Hello from sdes_settings_render().
    <div class="wrap">
        <h2>SDES Theme Settings</h2>
        <?php
            // settings_errors( $setting, $sanitize, $hide_on_update );
            settings_errors(); ?>
        <form action="options.php" method="POST">
            <?php settings_fields( 'sdes_setting_group' ); ?>
            <?php do_settings_sections( 'sdes_settings' ); ?>
            <?php submit_button(); ?>
        </form>
    </div>
    Bye from sdes_settings_render().
    <?php
}