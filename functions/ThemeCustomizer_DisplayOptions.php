<?php

/**
 * Writes out the CSS as defined by the values in the Theme Customizer
 * to the `head` element of the header template.
 */
function tctheme_customizer_css() {
    ?>
    <style type="text/css">
        div.site-title a { 
            color: <?php echo get_theme_mod( 'tctheme_link_color' ); ?> !important;
        }        
    </style>
    <?php
}
add_action( 'wp_head', 'tctheme_customizer_css' );
