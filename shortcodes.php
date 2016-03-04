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

class EventsSC extends ShortcodeBase {
    public
        $name = 'Events', // The name of the shortcode.
        $command = 'events', // The command used to call the shortcode.
        $description = 'Show events calendar from a feed', // The description of the shortcode.
        $callback    = 'callback',
        $wysiwyg     = True, // Whether to add it to the shortcode Wysiwyg modal.
        $params      = array(
            array(
                'name'      => 'Event Id',
                'id'        => 'id',
                'help_text' => 'The calendar_id of the events.ucf.edu calendar.',
                'type'      => 'text'
            ),
            array(
                'name'      => 'Limit',
                'id'        => 'limit',
                'help_text' => 'The calendar_id of the events.ucf.edu calendar.',
                'type'      => 'text',
                'default'   => 6,
            ),
        ); // The parameters used by the shortcode.

    /**
     * @see https://github.com/ucf-sdes-it/it-php-template/blob/e88a085401523f78b812ea8b4d9557ba30e40c9f/template_functions_generic.php#L241-L326
     */
    public static function callback($attr, $content='') {  //$id, $limit = 6, $header_text = "Upcoming Events"){
        $attr = shortcode_atts( array(
                'id' => 41, // SDES Events calendar.
                'limit' => 6,
                'header_text'    => 'Upcoming Events',
            ), $attr
        );
        if($attr['id'] == null) return true;
        
        //open cURL instance for the UCF Event Calendar RSS feed
        $ch = curl_init("http://events.ucf.edu/?calendar_id={$attr['id']}&upcoming=upcoming&format=rss");

        //set cURL options
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);  
        $rss = curl_exec($ch);
        curl_close($ch);
        $rss = @utf8_encode($rss);
        //disable libxml errors and allow user to fetch error information as needed
        libxml_use_internal_errors(true);
        try{
            $xml = new SimpleXMLElement($rss, LIBXML_NOCDATA);
        } catch(Exception $e){ }
        //if there are errors
        if(libxml_get_errors()){
            ob_start();
            ?>
            <li>Failed loading XML</li>
            <?php foreach(libxml_get_errors() as $error) : ?>
                <li><?= htmlentities($error->message) ?></li>
            <?php endforeach;
                return ob_get_clean();
        }

        //set limit if items returned are smaller than limit
        $count = (count($xml->channel->item) > $attr['limit']) ? $attr['limit'] : count($xml->channel->item);
          ob_start();
          ?>
            <div class="panel panel-warning">
                <div class="panel-heading"><?= $attr['header_text'] ?></div>
                <ul class="list-group ucf-events">
                <?php
                    //check for items
                    if(count($xml->channel->item) == 0) : ?>
                        <li class="list-group-item">Sorry, no events could be found.</li>
                    <?php
                    else :
                        //loop through until limit
                        for($i = 0; $i < $count; $i++){
                            //prepare xml output to html
                            $title = htmlentities($xml->channel->item[$i]->title);
                            $title = (strlen($title) > 50) ? substr($title, 0, 45) : $title;
                            $loc = htmlentities($xml->channel->item[$i]->children('ucfevent', true)->location->children('ucfevent', true)->name);
                            $map = htmlentities($xml->channel->item[$i]->children('ucfevent', true)->location->children('ucfevent', true)->mapurl);
                            $context['month'] = date('M', strtotime($xml->channel->item[$i]->children('ucfevent', true)->startdate));
                            $context['day'] = date('j', strtotime($xml->channel->item[$i]->children('ucfevent', true)->startdate));
                            $context['link'] = htmlentities($xml->channel->item[$i]->link);

                        ?>    
                        <li class="list-group-item">
                                <div class="date">
                                    <span class="month"><?= $context['month'] ?></span>
                                    <span class="day"><?= $context['day'] ?></span>
                                </div>
                                <a class="title" href="<?= $context['link'] ?>"><?= $title ?></a>
                                <a href="<?= $context['link'] ?>"><?= $loc ?></a>
                                <div class="end"></div>
                            </li>
                        <?php } 
                    endif;
                    ?>
                </ul>
                <div class="panel-footer">
                        <a href="http://events.ucf.edu/?calendar_id=<?= $attr['id'] ?>&amp;upcoming=upcoming">&raquo;More Events</a>
                </div>
            </div>
        <?php
            return ob_get_clean();
    }
}

class SocialButtonSC extends ShortcodeBase {
    public
        $name = 'Social Button', // The name of the shortcode.
        $command = 'social-button', // The command used to call the shortcode.
        $description = 'Show a button for a social network.', // The description of the shortcode.
        $callback    = 'callback',
        $closing_tag = False,
        $wysiwyg     = True, // Whether to add it to the shortcode Wysiwyg modal.
        $params      = array(
            array(
                'name'      => 'Network',
                'id'        => 'network',
                'help_text' => 'The social network to show.',
                'type'      => 'dropdown',
                'choices' => array(
                    array('value'=>'facebook', 'name'=>'facebook'),
                    array('value'=>'twitter', 'name'=>'twitter'),
                    array('value'=>'youtube', 'name'=>'youtube'),
                    )
            ),
            array(
                'name'      => 'Class',
                'id'        => 'class',
                'help_text' => 'The wrapper classes.',
                'type'      => 'text',
                'default' => 'col-sm-6 text-center',
            ),
        ); // The parameters used by the shortcode.

    /**
     * @see hhttps://github.com/ucf-sdes-it/it-php-template/blob/615ecbcfa0eccffd0e8b5f71501b1b7e78cd5cf7/template_data.php#L1723-L1740
     * @see https://shs.sdes.ucf.edu/home.php
     */
    public static function callback($attr, $content='') {
        $attr = shortcode_atts( array(
                'network' => '',
                'class' => 'col-sm-6 text-center',
            ), $attr
        );
        $ctx['container_classes'] = esc_attr( $attr['class'] );
        switch ($attr['network']) {
            case 'facebook':
            case 'twitter':
            case 'youtube':
            default:
                $ctxt['url'] = esc_attr( SDES_Static::get_theme_mod_defaultIfEmpty('sdes_rev_2015-'.$attr['network'], '') );
                $ctxt['image'] = esc_attr( "https://assets.sdes.ucf.edu/images/{$attr['network']}.gif" );
                break;
        }
        if ( '' == $ctxt['url'] ) return '';
        ob_start();
        ?>
            <div class="<?= $ctx['container_classes'] ?>">
                <a href="<?= $ctxt['url'] ?>">
                    <img src="<?= $ctxt['image'] ?>" class="clean" alt="button">
                </a>
            </div>
        <?php
        return ob_get_clean();
    }
}

function register_shortcodes() {
    ShortcodeBase::Register_Shortcodes(array(
            'RowSC',
            'ColumnSC',
            'EventsSC',
            'SocialButtonSC',
        ));
}
add_action( 'init', 'register_shortcodes' );
