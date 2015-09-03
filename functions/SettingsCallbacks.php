<?php

function section_one_callback() {
    echo 'Some help text goes here.';
}

//TODO: handle edge case where the setting has never been set in database., e.g., $settings['directory_cms_acronym'] throws "Undefined index".
function subtitle_callback() {
    $settings = (array) get_option( 'sdes_theme_settings' );
    $subtitle = esc_attr( $settings['subtitle'] );
    echo "<input type='text' name='sdes_theme_settings[subtitle]' value='$subtitle' />";
}

function google_analytics_id_callback() {
    $settings = (array) get_option( 'sdes_theme_settings' );
    $ga_id = esc_attr( $settings['google_analytics_id'] );
    echo "<input type='text' name='sdes_theme_settings[google_analytics_id]' value='$ga_id' />";
}

function javascript_callback() {
    $settings = (array) get_option( 'sdes_theme_settings' );
    $js = esc_attr( $settings['javascript'] );
    echo "<textarea name='sdes_theme_settings[javascript]' >$js</textarea>";
}

function javascript_libraries_callback() {
    $settings = (array) get_option( 'sdes_theme_settings' );
    $js_lib = esc_attr( $settings['javascript_libraries'] );
    echo "<input type='text' name='sdes_theme_settings[javascript_libraries]' value='$js_lib' />";
}

function css_callback() {
    $settings = (array) get_option( 'sdes_theme_settings' );
    $css = esc_attr( $settings['css'] );
    echo "<input type='text' name='sdes_theme_settings[css]' value='$css' />";
}

function directory_cms_acronym_callback() {
    $settings = (array) get_option( 'sdes_theme_settings' );
    $dir_acronym = esc_attr( $settings['directory_cms_acronym'] );
    echo "<input type='text' name='sdes_theme_settings[directory_cms_acronym]' value='$dir_acronym' />";
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