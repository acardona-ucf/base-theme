<?php

require_once('functions/class-sdes-static.php');

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

require_once( get_stylesheet_directory().'/functions/class-shortcodebase.php' );

/**
 * @see https://github.com/UCF/Students-Theme/blob/d56183079c70836adfcfaa2ac7b02cb4c935237d/shortcodes.php#L454-L504
 */
class RowSC extends ShortcodeBase {
    public
        $name        = 'Row',
        $command     = 'row',
        $description = 'Wraps content in a bootstrap row.',
        $params      = array(
            array(
                'name'      => 'Add Container',
                'id'        => 'container',
                'help_text' => 'Wrap the row in a container div',
                'type'      => 'checkbox'
            ),
            array(
                'name'      => 'Additional Classes',
                'id'        => 'class',
                'help_text' => 'Additional css classes',
                'type'      => 'text'
            ),
            array(
                'name'      => 'Inline Styles',
                'id'        => 'style',
                'help_text' => 'Inline css styles',
                'type'      => 'text'
            ),
        ),
        $callback    = 'callback',
        $wysiwyg     = True;

        public static function callback( $attr, $content='' ) {
            $attr = shortcode_atts( array(
                    'container' => False,
                    'class'     => '',
                    'style'    => ''
                ), $attr
            );

            ob_start();
          ?>
            <?php if ( $attr['container'] ) : ?>
            <div class="container">
            <?php endif; ?>
                <div class="row <?php echo $attr['class'] ? $attr['class'] : ''; ?>"<?php echo $attr['style'] ? ' style="' . $attr['style'] . '"' : '';?>>
                    <?php echo apply_filters( 'the_content', $content); ?>
                </div>
            <?php if ( $attr['container'] ) : ?>
            </div>
            <?php endif; ?>
          <?php
            return ob_get_clean();
        }
}

/**
 * @see https://github.com/UCF/Students-Theme/blob/d56183079c70836adfcfaa2ac7b02cb4c935237d/shortcodes.php#L506-L650
 */
class ColumnSC extends ShortcodeBase {
    public
        $name        = 'Column',
        $command     = 'column',
        $description = 'Wraps content in a bootstrap column',
        $params      = array(
            array(
                'name'      => 'Large Size',
                'id'        => 'lg',
                'help_text' => 'The size of the column when the screen is > 1200px wide (1-12)',
                'type'      => 'text'
            ),
            array(
                'name'      => 'Medium Size',
                'id'        => 'md',
                'help_text' => 'The size of the column when the screen is between 992px and 1199px wide (1-12)',
                'type'      => 'text'
            ),
            array(
                'name'      => 'Small Size',
                'id'        => 'sm',
                'help_text' => 'The size of the column when the screen is between 768px and 991px wide (1-12)',
                'type'      => 'text'
            ),
            array(
                'name'      => 'Extra Small Size',
                'id'        => 'xs',
                'help_text' => 'The size of the column when the screen is < 767px wide (1-12)',
                'type'      => 'text'
            ),
            array(
                'name'      => 'Large Offset',
                'id'        => 'lg_offset',
                'help_text' => 'The offset of the column when the screen is > 1200px wide (1-12)',
                'type'      => 'text'
            ),
            array(
                'name'      => 'Medium Offset',
                'id'        => 'md_offset',
                'help_text' => 'The offset of the column when the screen is between 992px and 1199px wide (1-12)',
                'type'      => 'text'
            ),
            array(
                'name'      => 'Small Offset',
                'id'        => 'sm_offset',
                'help_text' => 'The offset of the column when the screen is between 768px and 991px wide (1-12)',
                'type'      => 'text'
            ),
            array(
                'name'      => 'Extra Small Offset',
                'id'        => 'xs_offset',
                'help_text' => 'The offset of the column when the screen is < 767px wide (1-12)',
                'type'      => 'text'
            ),
            array(
                'name'      => 'Large Push',
                'id'        => 'lg_push',
                'help_text' => 'Pushes the column the specified number of column widths when the screen is > 1200px (1-12)',
                'type'      => 'text'
            ),
            array(
                'name'      => 'Medium Push',
                'id'        => 'md_push',
                'help_text' => 'Pushes the column the specified number of column widths when the screen is between 992px and 1199px wide (1-12)',
                'type'      => 'text'
            ),
            array(
                'name'      => 'Small Push',
                'id'        => 'sm_push',
                'help_text' => 'Pushes the column the specified number of column widths when the screen is between 768px and 991px wide (1-12)',
                'type'      => 'text'
            ),
            array(
                'name'      => 'Extra Small Push',
                'id'        => 'xs_push',
                'help_text' => 'Pushes the column the specified number of column widths when the screen is < 767px wide (1-12)',
                'type'      => 'text'
            ),
            array(
                'name'      => 'Large Pull',
                'id'        => 'lg_pull',
                'help_text' => 'Pulls the column the specified number of column widths when the screen is > 1200px wide (1-12)',
                'type'      => 'text'
            ),
            array(
                'name'      => 'Medium Offset Size',
                'id'        => 'md_pull',
                'help_text' => 'Pulls the column the specified number of column widths when the screen is between 992px and 1199px wide (1-12)',
                'type'      => 'text'
            ),
            array(
                'name'      => 'Small Offset Size',
                'id'        => 'sm_pull',
                'help_text' => 'Pulls the column the specified number of column widths when the screen is between 768px and 991px wide (1-12)',
                'type'      => 'text'
            ),
            array(
                'name'      => 'Extra Small Offset Size',
                'id'        => 'xs_pull',
                'help_text' => 'Pulls the column the specified number of column widths when the screen is < 767px wide (1-12)',
                'type'      => 'text'
            ),
            array(
                'name'      => 'Additional Classes',
                'id'        => 'class',
                'help_text' => 'Any additional classes for the column',
                'type'      => 'text'
            ),
            array(
                'style'     => 'Inline Styles',
                'id'        => 'style',
                'help_text' => 'Any additional inline styles for the column',
                'type'      => 'text'
            ),
        ),
        $callback    = 'callback',
        $wysiwig     = True;

    public static function callback( $attr, $content='' ) {
        // Size classes
        $classes = array( $attr['class'] ? $attr['class'] : '' );

        $prefixes = array( 'xs', 'sm', 'md', 'lg' );
        $suffixes = array( '', '_offset', '_pull', '_push' );

        foreach( $prefixes as $prefix ) {
            foreach( $suffixes as $suffix ) {
                if ( $attr[$prefix.$suffix] ) {
                    $suf = str_replace('_', '-', $suffix);
                    $classes[] = 'col-'.$prefix.$suf.'-'.$attr[$prefix.$suffix];
                }
            }
        }

        $cls_str = implode( ' ', $classes );

        ob_start();
      ?>
        <div class="<?php echo $cls_str; ?>"<?php echo $attr['style'] ? ' style="'.$attr['style'].'"' : ''; ?>>
            <?php echo apply_filters( 'the_content', $content ); ?>
        </div>
      <?php
        return ob_get_clean();
    }
}

ShortcodeBase::$installed_shortcodes = array_merge(
    ShortcodeBase::$installed_shortcodes,
    array(
        'RowSC',
        'ColumnSC',
        )
    );
