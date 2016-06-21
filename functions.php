<?php
/**
 * Entry point for a WordPress theme, along with the style.css file.
 * Includes or references all functionality for this theme.
 */

require_once('functions/class-sdes-static.php');
use SDES\SDES_Static as SDES_Static;
// TODO: remove all custom functions, constants (and possibly classes) from global namespace.

/**
 * Contributors for this theme should be able to edit, but not publish pages and posts.
 * Add capabilities for the Contributor role to: edit pages, delete unpublished pages, and upload files.
 * @see http://codex.wordpress.org/Roles_and_Capabilities#Contributor WP-Codex: Roles_and_Capabilities
 * @see http://codex.wordpress.org/Plugin_API/Action_Reference/admin_init WP-Codex: admin_init
 */
function extend_contributor_caps() {
    $role = get_role( 'contributor' );
    $role->add_cap( 'edit_others_posts' );
    $role->add_cap( 'edit_others_pages' );
    $role->add_cap( 'edit_pages' );
    $role->add_cap( 'delete_pages' ); // Still cannot delete_published_pages.
    $role->add_cap( 'upload_files' );
}
add_action( 'admin_init', 'extend_contributor_caps');

/*-------------------------------------------------------------------------------------------*/
//Home title fix
/*-------------------------------------------------------------------------------------------
add_filter( 'wp_title', 'home_title_fix_for_custom_homepage' );
function home_title_fix_for_custom_homepage( $title )
{
	if( empty( $title ) && ( is_home() || is_front_page() ) ) {
		return __( 'Home');
	}
	return $title;
}*/

//Adds in menu support into dashboard
function register_my_menus() {
  register_nav_menus(
    array(
      'main-menu' => __( 'Main Menu' ),
      'footer-left-menu' => __( 'Footer Left Column' ),
      'footer-center-menu' => __( 'Footer Center Column' ),
      )
    );
}
add_action( 'init', 'register_my_menus' );



// Enqueue Datepicker + jQuery UI CSS
add_action( 'wp_enqueue_scripts', 'enqueue_scripts_and_styles');
add_action( 'admin_enqueue_scripts', 'enqueue_scripts_and_styles');
function enqueue_scripts_and_styles(){
  wp_enqueue_script( 'jquery-ui-datepicker' );
  wp_enqueue_style( 'jquery-ui-style', '//ajax.googleapis.com/ajax/libs/jqueryui/1.8.1/themes/smoothness/jquery-ui.css', true);
}



require_once('custom-taxonomies.php');    // Define and Register taxonomies for this theme

require_once('custom-posttypes.php');  // Define and Register custom post_type's (CPTs) for this theme



/* function to return a custom field value. */
function get_custom_field( $value ) {
    global $post;

    $custom_field = get_post_meta( $post->ID, $value, true );
    if ( !empty( $custom_field ) )
        return is_array( $custom_field ) ? stripslashes_deep( $custom_field ) : stripslashes( wp_kses_decode_entities( $custom_field ) );

    return false;
}

// Replaces the excerpt "[Read more]" text by a link
function new_excerpt_more($more) {
   global $post;

   return ' ... <br><br><a class="moretag" href="'. get_permalink($post->ID) . '">[Read more]</a>';
}
add_filter('excerpt_more', 'new_excerpt_more');





require_once('functions/class-sdes-static.php');


/* Display hours from directory feed */
function html_site_hours($hours){
    $output = null;
    $collections = array();
    $final = array();

    //value-to-day conversion array
    $names = array('Mon', 'Tues', 'Wed', 'Thur', 'Fri', 'Sat', 'Sun');

    if(empty($hours)){
        $output = "Mon-Sun: Closed";
        return $output;
    }

    //loop through each day
    foreach($hours as $day => $hours){

        //if the day has hours set
        if($hours->day != null and $hours->open != null){

            //grab each piece of the time
            $seconds_open = explode(':', $hours->open);
            $seconds_close = explode(':', $hours->close);

            //set the time stamp
            $open_format = $seconds_open[0] == '00' ? 'ga' : 'g:ia';
            $close_format = $seconds_close[0] == '00' ? 'ga' : 'g:ia';

            //save the times out as clean times (8:00am)
            $open = date($open_format, strtotime('1985-10-22 ' . $hours->open));
            $close = date($close_format, strtotime('1985-10-22 ' . $hours->close));
            $both = $open.' - '.$close;

            //if this range exists, capture it
            $collections[$both][] = $day;
        }
    }
    if(empty($collections)){
        $output = "Mon-Sun: Closed";
        return $output;
    }

    $blocks = null;
    $block = null;

    //separate them by sequential order
    foreach($collections as $time => $days){

        //for each day in the collection
        foreach($days as $index => $day){

            //set the current day to the current block
            $block[] = $day;

            //save and start a new block if the next day isn't sequential or is the last element
            if($day == end($days) or (isset($days[$index + 1]) and $day + 1 != $days[$index + 1])){
                $blocks[] = $block;
                $block = null;
            }
        }

        //save out blocks, reset
        $collections[$time] = $blocks;
        $blocks = null;
    }

    //echo time
    foreach($collections as $time => $days){

        foreach($days as $piece){
            $temp[] = count($piece) == 1 ? $names[$piece[0]] : $names[$piece[0]].'-'.$names[end($piece)];
        }

        $final[] = implode(', ', $temp).': '.$time;
        $temp = null;
    }

    //save to output string
    $output .= implode("<br />\n", $final);

    return $output;
}

/* wrapper for calendar feed */
function render_calendar($id, $limit = 6, $header_text = "Upcoming Events"){
    if($id == null) return true;
    
    //open cURL instance for the UCF Event Calendar RSS feed
    $ch = curl_init("http://events.ucf.edu/?calendar_id={$id}&upcoming=upcoming&format=rss");

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
        $output = '<li>Failed loading XML</li>';
        foreach(libxml_get_errors() as $error){
            $output .= '<li>'.htmlentities($error->message).'</li>';
        }
        return $output;
    }

    //set limit if items returned are smaller than limit
    $count = (count($xml->channel->item) > $limit) ? $limit : count($xml->channel->item);
    $output = '<div class="panel panel-warning"><div class="panel-heading">'.$header_text.'</div><ul class="list-group ucf-events">';

    //check for items
    if(count($xml->channel->item) == 0){
        //error message
        $output .= '<li class="list-group-item">Sorry, no events could be found.</li></ul>';

        //finish unordered list
        $output .= '</ul><div class="panel-footer"><a href="http://events.ucf.edu/?calendar_id='.$id.'&amp;upcoming=upcoming">&raquo;More Events</a></div></div>';

        return $output;
    }

    //loop through until limit
    for($i = 0; $i < $count; $i++){

        //prepare xml output to html
        $title  = htmlentities($xml->channel->item[$i]->title);
        $title = (strlen($title) > 50) ? substr($title, 0, 45) : $title;
        $loc = htmlentities($xml->channel->item[$i]->children('ucfevent', true)->location->children('ucfevent', true)->name);
        $map = htmlentities($xml->channel->item[$i]->children('ucfevent', true)->location->children('ucfevent', true)->mapurl);

        //output html
        $output .= 
        '<li class="list-group-item">
            <div class="date">
                <span class="month">'.date('M', strtotime($xml->channel->item[$i]->children('ucfevent', true)->startdate)).'</span>
                <span class="day">'.date('j', strtotime($xml->channel->item[$i]->children('ucfevent', true)->startdate)).'</span>
            </div>
            <a class="title" href="'.htmlentities($xml->channel->item[$i]->link).'">'.$title.'</a>
            <a href="'.htmlentities($xml->channel->item[$i]->link).'">'.$loc.'</a>
            <div class="end"></div>
        </li>';
    }

    //finish unordered list
    $output .= '</ul><div class="panel-heading"><a href="http://events.ucf.edu/?calendar_id='.$id.'&amp;upcoming=upcoming">&raquo;More Events</a></div></div>';

    //return clean
    return $output;
}

// Is this function called anywhere?
function links(){
    $output = null;
    if (is_page('Departments')) {
        $output .= '<ul class="nav nav-pills pull-right">';
        $output .= '<li><a href="programs-and-services">Programs and Services</a></li>';
        $output .= '</ul>';
    } elseif (is_page('pagename')) {
            # code...
    } elseif (is_page('pagename')) {
            # code...
    } elseif (is_page('pagename')) {
            # code...
    } elseif (is_page('pagename')) {
            # code...
    }
    return $output; 
}




require_once('functions/Settings.php');

//////////////////////////////////////////////////////////////////////////////////////////////////////////////////



//////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////
require_once('functions/ThemeCustomizer.php'); // Admin > Appearance > Customize
require_once('functions/admin.php'); // Admin/login functions.
require_once('functions/admin-theme.php'); // Theme-specific admin/login functions.
require_once('shortcodes.php');


require_once( 'functions/class-render-template.php' );


/**
 * Add .img-responsive to img tags.
 * @see https://developer.wordpress.org/reference/functions/the_content/ WP-Ref: the_content()
 * @see http://stackoverflow.com/a/20499803 Stack-Overflow: /a/20499803
 */
function img_add_responsive_class_content( $content ){
    return SDES_Static::img_add_responsive_class_content( $content );
}
add_filter('the_content', 'img_add_responsive_class_content');


/**
 * Add .img-responsive to img tags in sidecolumn metadata fields.
 * Note: ${meta_type}s are "comment, post, user" so both post_sidecolumn and page_sidecolumn fields are filtered.
 * @see https://developer.wordpress.org/reference/hooks/get_meta_type_metadata/ WP-Ref: get_{meta_type}_metadata
 * @see http://wordpress.stackexchange.com/a/175179 Stack-Overflow: /a/175179
 */
function img_add_responsive_class_sidecolumn( $value, $object_id, $meta_key, $single ){
    if( false === strpos( $meta_key, '_sidecolumn' ) ) {
        return $value;
    } else {
        remove_filter( 'get_post_metadata', __FUNCTION__, true );
        $value = get_post_meta( $object_id, $meta_key, true );
        add_filter( 'get_post_metadata', __FUNCTION__, true, 4 );
        return img_add_responsive_class_content( $value );
    }
}
add_filter('get_post_metadata', 'img_add_responsive_class_sidecolumn', true, 4);

//TODO: add screenshot.png for Theme.
