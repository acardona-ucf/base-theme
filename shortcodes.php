<?php
/**
 * Add and configure Shortcodes for this theme.
 * Relies on the implementation in ShortcodeBase.
 */
namespace SDES\BaseTheme\Shortcodes;
use \StdClass;
use \Exception;
use \SimpleXMLElement;
use SDES\SDES_Static as SDES_Static;
use SDES\Shortcodes\ShortcodeBase;
require_once('functions/class-sdes-static.php');

require_once( get_stylesheet_directory().'/vendor/autoload.php' );
use Underscore\Types\Arrays;

/**
 * [menuPanel] - Return an in-line menu panel (DIV.panel) using a user-configured Menu.
 * Available attributes:
 * name      => The "Menu Name" of the menu under Appearance>Menus, e.g., Pages
 * heading   => Display an alternate heading instead of the menu Id.
 *
 * Example:
 * [menuPanel name="Other Resources" heading="An Alternate heading"]
 */
class sc_menuPanel extends ShortcodeBase {
    public
        $name = 'Menu Panel',
        $command = 'menuPanel',
        $description = 'Show panelled menu, usually in sidecolumns.',
        $callback    = 'callback',
        $render      = 'render',
        $closing_tag = False,
        $wysiwyg     = True,
        $params      = array(
            array(
                'name'      => 'Menu Name',
                'id'        => 'name',
                'help_text' => 'The menu to display.',
                'type'      => 'text',
            ),
            array(
                'name'      => 'Heading',
                'id'        => 'heading',
                'help_text' => 'A heading to display (optional).',
                'type'      => 'text',
            ),
        ); // The parameters used by the shortcode.

    function __construct() {
        $menus = wp_get_nav_menus();
        $choices = array();
        foreach ($menus as $menu) {
            if ( !is_wp_error($menu) && !array_key_exists('invalid_taxonomy', $menu) && !empty($menu) ) {
                $choices[] = array('value'=>$menu->slug, 'name'=>$menu->name);
            }
        }
        $new_name_param = Arrays::from( $this->params )
         ->find( function($x) { return 'name' == $x['id']; } )
         ->set('type', 'dropdown')
         ->set('choices', $choices)
         ->obtain();
        $other_params = Arrays::from( $this->params )
             ->filter(function($x) { return 'name' != $x['id']; } )
             ->obtain();
        $this->params = array_merge( array( $new_name_param ), $other_params );
    }

    public static function callback( $attrs, $content=null )
    {
        $attrs = shortcode_atts( array(
                'name' => 'Pages',
                'heading' => $attrs['name'],
                'style' => 'max-width: 697px;',
            ), $attrs
        );
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
        // Sanitize input and set context for view
        $context['heading'] = esc_html( $attrs['heading'] );
        $context['menu_items'] = wp_get_nav_menu_items( esc_attr( $attrs['name'] ) );
        $context['style'] = esc_attr( $attrs['style'] );
        return static::render($context);
    }

    /**
     * Render HTML for a "menuPanel" shortcode with a given context.
     * Context variables:
     * heading    => The panel-heading.
     * menu_items => An array of WP_Post objects representing the items in the menu.
     * style  => Value for the css attribute "style" on the container div.
     */
    public static function render( $context ){
        ob_start();
        ?>
        <div class="panel panel-warning menuPanel" style="<?=$context['style']?>">
            <div class="panel-heading"><?=$context['heading']?></div>
            <div class="list-group">
                <?php
                foreach ( (array) $context['menu_items'] as $key => $menu_item ) {
                    $title = $menu_item->title;
                    $url = SDES_Static::url_ensure_prefix( $menu_item->url );
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
 * [row] - Wrap HTML in a Boostrap CSS row.
 * @see https://github.com/UCF/Students-Theme/blob/d56183079c70836adfcfaa2ac7b02cb4c935237d/shortcodes.php#L454-L504
 */
class sc_row extends ShortcodeBase {
    public
        $name        = 'Row',
        $command     = 'row',
        $description = 'Wraps content in a bootstrap row.',
        $render      = False,
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
                    'container' => 'false',
                    'class'     => '',
                    'style'    => ''
                ), $attr
            );

            ob_start();
          ?>
            <?php if ( 'true' == $attr['container'] ) : ?>
            <div class="container">
            <?php endif; ?>
                <div class="row <?php echo $attr['class'] ? $attr['class'] : ''; ?>"<?php echo $attr['style'] ? ' style="' . $attr['style'] . '"' : '';?>>
                    <?php echo apply_filters( 'the_content', $content); ?>
                </div>
            <?php if ( 'true' == $attr['container'] ) : ?>
            </div>
            <?php endif; ?>
          <?php
            return ob_get_clean();
        }
}

/**
 * [column] - Wrap HTML in a Boostrap CSS column.
 * @see https://github.com/UCF/Students-Theme/blob/d56183079c70836adfcfaa2ac7b02cb4c935237d/shortcodes.php#L506-L650
 */
class sc_column extends ShortcodeBase {
    public
        $name        = 'Column',
        $command     = 'column',
        $description = 'Wraps content in a bootstrap column',
        $render      = 'render',
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

        $ctxt['cls_str'] = esc_attr( implode( ' ', $classes ) );
        $ctxt['style'] = esc_attr( $attr['style'] );
        $ctxt['content'] = apply_filters( 'the_content', $content );
        return static::render( $ctxt );
    }

    public static function render ( $ctxt ) {
        ob_start();
      ?>
        <div class="<?= $ctxt['cls_str'] ?>" style="<?= $ctxt['style'] ?>">
            <?= $ctxt['content'] ?>
        </div>
      <?php
        return ob_get_clean();
    }
}

/**
 * [events] - Show an events calendar from events.ucf.edu
 */
class sc_events extends ShortcodeBase {
    public
        $name = 'Events', // The name of the shortcode.
        $command = 'events', // The command used to call the shortcode.
        $description = 'Show events calendar from a feed', // The description of the shortcode.
        $callback    = 'callback',
        $render      = False,
        $closing_tag = False,
        $wysiwyg     = True, // Whether to add it to the shortcode Wysiwyg modal.
        $params      = array(
            array(
                'name'      => 'Event Id',
                'id'        => 'id',
                'help_text' => 'The calendar_id of the events.ucf.edu calendar.',
                'type'      => 'text'
            ),
            array(
                'name'      => 'Header',
                'id'        => 'header',
                'help_text' => 'The a header for the events calendar.',
                'type'      => 'text',
                'default'   => 'Upcoming Events',
            ),
            array(
                'name'      => 'Limit',
                'id'        => 'limit',
                'help_text' => 'Only show this many items.',
                'type'      => 'number',
                'default'   => 6,
            ),
        ); // The parameters used by the shortcode.

    /**
     * @see https://github.com/ucf-sdes-it/it-php-template/blob/e88a085401523f78b812ea8b4d9557ba30e40c9f/template_functions_generic.php#L241-L326
     */
    public static function callback($attr, $content='') {
        $attr = shortcode_atts( array(
                'id' => 41, // SDES Events calendar.
                'limit' => 6,
                'header'    => 'Upcoming Events',
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
                <div class="panel-heading"><?= $attr['header'] ?></div>
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

/**
 * [socialButton] - Show a button for social network, based on the URL set in the Theme Customizer.
 */
class sc_socialButton extends ShortcodeBase {
    public
        $name = 'Social Button', // The name of the shortcode.
        $command = 'socialButton', // The command used to call the shortcode.
        $description = 'Show a button for a social network.', // The description of the shortcode.
        $callback    = 'callback',
        $render      = 'render',
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
        $ctxt['container_classes'] = esc_attr( $attr['class'] );
        switch ($attr['network']) {
            case 'facebook':
            case 'twitter':
            case 'youtube':
            default:
                $ctxt['url'] = esc_attr(
                    SDES_Static::url_ensure_prefix(
                        SDES_Static::get_theme_mod_defaultIfEmpty('sdes_rev_2015-'.$attr['network'], '') ) );
                $ctxt['image'] = esc_attr( "https://assets.sdes.ucf.edu/images/{$attr['network']}.gif" );
                break;
        }
        if ( '' == $ctxt['url'] ) return '';
        return static::render( $ctxt );
    }

    /**
     * Render HTML for a "socialButton" shortcode with a given context.
     * Context variables:
     * container_classes    => List of css classes for the cotainer div..
     * url  => The URL of the social network being linked.
     * image  => The button image.
     */
    public static function render ( $ctxt ) {
        ob_start();
        ?>
            <div class="<?= $ctxt['container_classes'] ?>">
                <a href="<?= $ctxt['url'] ?>">
                    <img src="<?= $ctxt['image'] ?>" class="clean" alt="button">
                </a>
            </div>
        <?php
        return ob_get_clean();
    }
}

require_once( get_stylesheet_directory().'/custom-posttypes.php' );
    use SDES\BaseTheme\PostTypes\Alert;
/**
 * Use code from the Alert class in a shortcode.
 * Extending Alert to add ContextToHTML, assuming responsiblity for sanitizing inputs.
 */
class AlertWrapper extends Alert {
    public static function ContextToHTML ( $alert_context ) {
        return static::render_to_html( $alert_context );
    }
}
/**
 * [alert] - Show a single, ad-hoc alert directly in a page's content.
 */
class sc_alert extends ShortcodeBase {
    public
        $name = 'Alert (Ad hoc)', // The name of the shortcode.
        $command = 'alert', // The command used to call the shortcode.
        $description = 'Show an alert on a single page.', // The description of the shortcode.
        $callback    = 'callback',
        $render      = 'render',
        $closing_tag = False,
        $wysiwyg     = True, // Whether to add it to the shortcode Wysiwyg modal.
        $params      = array(
            array(
                'name'      => 'Is Unplanned',
                'id'        => 'is_unplanned',
                'help_text' => 'Show the alert as red instead of yellow.',
                'type'      => 'checkbox',
                'default'   => true,
            ),
            array(
                'name'      => 'Title',
                'id'        => 'title',
                'help_text' => 'A title for the alert (shown in bold).',
                'type'      => 'text',
                'default'   => 'ALERT',
            ),
            array(
                'name'      => 'Message',
                'id'        => 'message',
                'help_text' => 'Message text for the alert.',
                'type'      => 'text',
                'default'   => 'Alert',
            ),
            array(
                'name'      => 'URL',
                'id'        => 'url',
                'help_text' => 'Make the alert a link.',
                'type'      => 'text',
                'default'   => '',
            ),
        ); // The parameters used by the shortcode.


    public static function callback($attr, $content='') {
        $attr = shortcode_atts( array(
                'title' => 'ALERT',
                'message' => 'Alert',
                'is_unplanned' => true,
                'url' => null,
            ), $attr
        );
        // TODO: consider using boolval after PHP 5.5.0.
        $attr['is_unplanned'] = filter_var( $attr['is_unplanned'], FILTER_VALIDATE_BOOLEAN);

        // Create and sanitize mocks for WP_Post and metadata using the shortcode attributes.
        $alert = new StdClass;
        $alert->post_title = esc_attr( $attr['title'] );
        $alert->post_content = esc_attr( $attr['message'] );
        $metadata_fields = array(
               'alert_is_unplanned' => $attr['is_unplanned'],
               'alert_url' => esc_attr( SDES_Static::url_ensure_prefix($attr['url']) ),
            );
        $ctxt = AlertWrapper::get_render_context( $alert, $metadata_fields );
        return AlertWrapper::ContextToHTML( $ctxt );
    }
}

class sc_departmentInfo extends ShortcodeBase {
    public
        $name = 'Department Information', // The name of the shortcode.
        $command = 'departmentInfo', // The command used to call the shortcode.
        $description = 'Show the department contact information box.', // The description of the shortcode.
        $callback    = 'callback',
        $render      = 'render',
        $closing_tag = False,
        $wysiwyg     = True, // Whether to add it to the shortcode Wysiwyg modal.
        $params      = array();

    public static function callback( $attr, $content='' ) {
        $directory_cms_acronym = esc_attr(get_option('sdes_theme_settings_dir_acronym'));
        $departmentInfo = "<!-- Configure a department to show hours, phone, fax, email, and location. -->";
        if( null != $directory_cms_acronym && !ctype_space($directory_cms_acronym) ) {
            $departmentInfo = static::get_department_info( $directory_cms_acronym );
        }
        $ctxt['departmentInfo'] = $departmentInfo;
        return static::render( $ctxt );
    }

    /**
     * Render HTML for a "departmentInfo" shortcode with a given context.
     * Context variables:
     * container_classes    => List of css classes for the cotainer div..
     */
    public static function render ( $ctxt ) {
        ob_start();
        ?>
            <span id="departmentInfo"><?= $ctxt['departmentInfo'] ?></span>
        <?php
        return ob_get_clean();
    }

    /* Reads in and displays department information */
    // TODO: Set the department feed URL with a Theme Option, default to the feed's current URL.
    // TODO: refactor out `get_department_info('menu')` and `get_department_info("{$ACRONYMN}")` functionality.
    // TODO: refactor HTML to use "View-and-Context" pattern instead of stringbuilding pattern.
    public static function get_department_info($action = NULL) {
        $json = file_get_contents('http://directory.sdes.ucf.edu/feed'); 
        $decodejson = json_decode($json);        

        $yield = '';

        if (!empty($action)) {
            switch ($action) {
                case 'menu':
                $yield .= '<div class="panel panel-warning">';
                $yield .= '<div class="panel-heading">Page Navigation</div>';
                $yield .= '<div class="list-group">';
                foreach ($decodejson->departments as $department) { 
                    $yield .= "<a href='#{$department->acronym}' class='list-group-item'>{$department->name}</a>";
                }                            
                $yield .= '</div>';
                $yield .= '</div>';
                break;
                
                default:
                $yield .= '<table class="table table-condensed table-striped table-bordered">';
                $yield .= '<tbody>';
                foreach ($decodejson->departments as $department) {
                    // TODO: helper function or best practices for get_theme_mod
                    $phone = SDES_Static::get_theme_mod_defaultIfEmpty('sdes_rev_2015-phone', $department->phone );
                    $fax = SDES_Static::get_theme_mod_defaultIfEmpty( 'sdes_rev_2015-fax', $department->fax );
                    $hours = SDES_Static::get_theme_mod_defaultIfEmpty( 'sdes_rev_2015-hours', $department->hours );
                    $email = SDES_Static::get_theme_mod_defaultIfEmpty( 'sdes_rev_2015-email', $department->email );

                    $site_hours = ($hours == $department->hours) ? html_site_hours($department->hours) : $hours;

                    if( $department->acronym == $action){
                        $yield .= '<tr><th scope="row">Hours</th>';
                        $yield .= '<td>' . $site_hours . '</td>';
                        $yield .= '</tr><tr>';
                        $yield .= '<th scope="row">Phone</th>';
                        $yield .= "<td>{$phone}</td>";
                        $yield .= '</tr><tr>';
                        $yield .= '<th scope="row">Fax</th>';
                        $yield .= "<td>{$fax}</td>";
                        $yield .= '</tr><tr>';
                        $yield .= '<th scope="row">Email</th>';
                        $yield .= "<td><a href='mailto:{$email}'>{$email}</a></td>";
                        $yield .= '</tr><tr>';
                        $yield .= '<th scope="row">Location</th>';
                        $yield .= "<td><a href='http://map.ucf.edu/?show={$department->location->building}' >{$department->location->building}, Building {$department->location->buildingNumber} Room {$department->location->roomNumber}</a>";
                        $yield .= '</tr>';
                    }
                }
                $yield .= '</tbody></table>';
                break;
            }
        } else {
            foreach ($decodejson->departments as $department) {
                // TODO: helper function or best practices for get_theme_mod
                $phone = SDES_Static::get_theme_mod_defaultIfEmpty('sdes_rev_2015-phone', $department->phone );
                $fax = SDES_Static::get_theme_mod_defaultIfEmpty( 'sdes_rev_2015-fax', $department->fax );
                $email = SDES_Static::get_theme_mod_defaultIfEmpty( 'sdes_rev_2015-email', $department->email );

                $yield .= "<div class='news' id='{$department->acronym}'>";
                $yield .= "<img src='http://directory.sdes.ucf.edu/{$department->image}' alt='thumbnail' class='img-responsive'>";
                $yield .= '<div class="news-content">';
                $yield .= '<div class="news-title">';
                $yield .= "<a href='{$department->websites[0]->uri}'>{$department->name}</a>";
                $yield .= '</div>';
                $yield .= '<div class="news-strapline">';
                foreach ($department->staff as $staff) {
                    $yield .= "{$staff->name}, <small class='text-muted'>{$staff->position}</small><br>";
                }
                $yield .= '</div>';
                $yield .= '<br>';
                $yield .= '<table class="table table-striped table-hover dept">';
                $yield .= '<tbody>';
                $yield .= '<tr>';
                $yield .= '<th scope="row">Phone</th>';
                $yield .= "<td>{$phone}</td>";
                $yield .= '</tr><tr>';
                $yield .= '<th scope="row">Fax</th>';
                $yield .= "<td>{$fax}</td>";
                $yield .= '</tr><tr>';
                $yield .= '<th scope="row">Email</th>';
                $yield .= "<td><a href='mailto:{$email}'>{$email}</a></td>";
                $yield .= '</tr><tr>';
                $yield .= '<th scope="row">Location</th>';
                $yield .= "<td><a href='http://map.ucf.edu/?show={$department->location->building}' >{$department->location->building}, Building {$department->location->buildingNumber} Room {$department->location->roomNumber}</a>";
                $yield .= '</tr><tr>';
                $yield .= '<th scope="row">Website</th>';
                $yield .= "<td><a href='{$department->websites[0]->uri}' class='external'>{$department->websites[0]->uri}</a></td>";
                $yield .= '</tr>';
                if(!empty($department->socialNetworks)){
                    $yield .= '<tr>';
                    $yield .= '<th scope="row">Social</th>';
                    $yield .= '<td>';
                    asort($department->socialNetworks);
                    foreach ($department->socialNetworks as $network) {
                        $yield .= "<a href='{$network->uri}'>";
                        $yield .= '<img src="//assets.sdes.ucf.edu/images/icons/'.strtolower($network->name).'.png" class="social" alt="icon">';
                        $yield .= '</a>';
                    }
                    $yield .= '</td>';
                    $yield .= '</tr>';
                }            
                $yield .= '</tbody></table>';
                array_shift($department->websites);
                if(!empty($department->websites)){
                    $yield .= '<table class="table table-striped table-hover sites">';
                    $yield .= '<caption>Programs, Teams, and Other Websites</caption>';
                    $yield .= '<tbody>';
                    foreach ($department->websites as $website) {
                        $yield .= "<tr><td><a href='{$website->uri}'>{$website->uri}</a></td></tr>";
                    }
                }
                $yield .= '</tbody></table></div></div>';
            }
        }
        return $yield;
    }
}

function register_shortcodes() {
    ShortcodeBase::Register_Shortcodes(array(
            __NAMESPACE__.'\sc_row',
            __NAMESPACE__.'\sc_column',
            __NAMESPACE__.'\sc_alert',
            __NAMESPACE__.'\sc_menuPanel',
            __NAMESPACE__.'\sc_events',
            __NAMESPACE__.'\sc_socialButton',
            __NAMESPACE__.'\sc_departmentInfo',
        ));
}
add_action( 'init', __NAMESPACE__.'\register_shortcodes' );
