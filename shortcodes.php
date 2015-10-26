<?php

require_once('functions/Class_SDES_Static.php');

/**
 * [menuPanel] - Return an in-line menu panel (DIV.panel) using a user-configured Menu.
 * Available attributes:
 * name      => The "Menu Name" of the menu under Appearance>Menus, e.g., Pages
 * heading   => Display an alternate heading instead of the menu Id.
 * max-width => Set a max-width on the container DIV.panel.
 *
 * Example:
 * [menuPanel name="Other Resources" heading="An Alternate heading"]
 */
function sc_menuPanel($attrs, $content=null)
{
    // Default attributes
    SDES_Static::set_default_keyValue($attrs, 'name', 'Pages');
    SDES_Static::set_default_keyValue($attrs, 'heading', $attrs['name']);
    SDES_Static::set_default_keyValue($attrs, 'max-width', '697px');
    // Sanitize input
    $attrs['name'] = esc_attr($attrs['name']);
    $attrs['heading'] = esc_html($attrs['heading']);
    $attrs['max-width'] = esc_attr($attrs['max-width']);
    // Check for errors
    if( !is_nav_menu($attrs['name']) ) {
        $error = sprintf('Could not find a nav menu named "%1$s"', $attrs['name']);

        // Output as HTML comment when not logged in or can't edit.
        $format_error = 
         (SDES_Static::Is_UserLoggedIn_Can('edit_posts'))
          ? '<p class="bg-danger text-danger">Admin Alert: %1$s</p>'
          : '<!-- %1$s -->';
        $error = sprintf($format_error, $error);
        return $error;
    }

    // Set context for view
    $context['heading'] = $attrs['heading'];
    $context['menu_items'] = wp_get_nav_menu_items( $attrs['name'] );
    $context['max-width'] = $attrs['max-width'];

    // Render HTML
    return render_sc_menuPanel($context);
}
add_shortcode('menuPanel', 'sc_menuPanel');
/**
 * Render HTML for a "menuPanel" shortcode with a given context.
 * Context variables:
 * heading    => The panel-heading.
 * menu_items => An array of WP_Post objects representing the items in the menu.
 * max-width  => Value for the css attribute "max-width" on the container div.
 */
function render_sc_menuPanel($context)
{
    ob_start();
    ?>
    <div class="panel panel-warning menuPanel" style="max-width: <?=$context['max-width']?>;">
        <div class="panel-heading"><?=$context['heading']?></div>
        <div class="list-group">
            <?php
            foreach ( (array) $context['menu_items'] as $key => $menu_item ) {
                $title = $menu_item->title;
                $url = $menu_item->url;
                $class_names = SDES_Static::Get_ClassNames($menu_item, 'nav_menu_css_class');
                //TODO: Automatically add .external if url is external (check with regex?)
            ?>
                <a href="<?=$url?>" class="list-group-item <?=$class_names?>"><?=$title?></a>
            <?php  } ?>
        </div>
    </div>
    <?php
    return ob_get_clean();
}




/**************** SHORTCODE Boilerplate START **********************
 * [myShortcode] - Shortcode description.
 * Available attributes:
 * attr1 => Description of attr1.
 * attr2 => Description of attr2.
 *
 * Example:
 * [myShortcode attr1="SomeValue" attr2="AnotherValue"]
 */
function sc_myShortcode($attrs, $content=null)
{
    // Default attributes
    SDES_Static::set_default_keyValue($attrs, 'attr1', 'SomeValue');
    SDES_Static::set_default_keyValue($attrs, 'attr2', 'AnotherValue');
    // Sanitize input
    $attrs['attr1'] = esc_attr($attrs['attr1']);
    $attrs['attr2'] = esc_attr($attrs['attr2']);
    
    // Shortcode logioc

    // Set context for view
    $context['disp1'] = $attrs['attr1'];
    $context['disp2'] = $attrs['attr2'];
    // Render HTML
    return rencer_sc_myShortcode($context);
}
add_shortcode('myShortcode', 'sc_myShortcode');
/**
 * Render HTML for a "myShortcode" shortcode with a given context.
 * Context variables:
 * disp1 => Description.
 * disp2 => Description.
 */
function render_sc_myShortcode($context)
{
    ob_start();
    ?>
    <div>Some: <?=$context['disp1']?></div>
    <div>Another: <?=$context['disp2']?></div>
    <?php
    return ob_get_clean();
}
/**************** SHORTCODE Boilerplate END   **********************/
