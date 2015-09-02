<?php
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
      'header-menu' => __( 'Header Menu' ),
      'extra-menu' => __( 'Extra Menu' )
      )
    );
}
add_action( 'init', 'register_my_menus' );

/*-------------------------------------------------------------------------------------------*/
/* news_list Post Type */
/*-------------------------------------------------------------------------------------------*/
class news_list {

    function news_list() {
        add_action('init',array($this,'create_post_type'));
    }

    function create_post_type() {
        $labels = array(
            'name' => 'news_lists',
            'singular_name' => 'news_list',
            'add_new' => 'Add New',
            'all_items' => 'All Posts',
            'add_new_item' => 'Add New Post',
            'edit_item' => 'Edit Post',
            'new_item' => 'New Post',
            'view_item' => 'View Post',
            'search_items' => 'Search Posts',
            'not_found' =>  'No Posts found',
            'not_found_in_trash' => 'No Posts found in trash',
            'parent_item_colon' => 'Parent Post:',
            'menu_name' => 'News'
            );
        $args = array(
            'labels' => $labels,
            'description' => "Description",
            'public' => true,
            'exclude_from_search' => true,
            'publicly_queryable' => true,
            'show_ui' => true,
            'show_in_nav_menus' => true,
            'show_in_menu' => true,
            'show_in_admin_bar' => true,
            'menu_icon' => 'dashicons-admin-site',
            'capability_type' => 'post',
            'hierarchical' => false,
            'supports' => array('title','editor', 'thumbnail'),
            'has_archive' => true,
            'rewrite' => array('slug' => 'newslist'),
            'query_var' => true,
            'can_export' => true
            );
        register_post_type('news_list',$args);
    }
}

$news_list = new news_list();

/*-------------------------------------------------------------------------------------------*/
/* staff_list Post Type */
/*-------------------------------------------------------------------------------------------*/
class staff_list {

    function staff_list() {
        add_action('init',array($this,'create_post_type'));
    }

    function create_post_type() {
        $labels = array(
            'name' => 'staff_lists',
            'singular_name' => 'staff_list',
            'add_new' => 'Add New',
            'all_items' => 'All Posts',
            'add_new_item' => 'Add New Post',
            'edit_item' => 'Edit Post',
            'new_item' => 'New Post',
            'view_item' => 'View Post',
            'search_items' => 'Search Posts',
            'not_found' =>  'No Posts found',
            'not_found_in_trash' => 'No Posts found in trash',
            'parent_item_colon' => 'Parent Post:',
            'menu_name' => 'Staff'
            );
        $args = array(
            'labels' => $labels,
            'description' => "Description",
            'public' => true,
            'exclude_from_search' => true,
            'publicly_queryable' => true,
            'show_ui' => true,
            'show_in_nav_menus' => true,
            'show_in_menu' => true,
            'show_in_admin_bar' => true,
            'menu_icon' => 'dashicons-groups',
            'capability_type' => 'post',
            'hierarchical' => false,
            'supports' => array('title','editor', 'thumbnail'),
            'has_archive' => true,
            'rewrite' => array('slug' => 'stafflist'),
            'query_var' => true,
            'can_export' => true
            );
        register_post_type('staff_list',$args);
    }
}

$staff_list = new staff_list();

/*-------------------------------------------------------------------------------------------*/
/* Adds feature image to News, Staff
/*-------------------------------------------------------------------------------------------*/
add_theme_support( 'post-thumbnails', array('news_list', 'staff_list') );


/*-------------------------------------------------------------------------------------------*/
/* Add thumbnail column to admin page 
/*-------------------------------------------------------------------------------------------*/
if ( !function_exists('AddThumbColumn') && function_exists('add_theme_support') ) { 

    function AddThumbColumn($cols) { 
        $cols['thumbnail'] = __('Thumbnail'); return $cols; 
    } 
    function AddThumbValue($column_name, $post_id) { 
        $width = (int) 120; $height = (int) 120; 
        if ( 'thumbnail' == $column_name ) { 

            $thumbnail_id = get_post_meta( $post_id, '_thumbnail_id', true ); 

            $attachments = get_children( array('post_parent' => $post_id, 'post_type' => 'attachment', 'post_mime_type' => 'image') ); 
            if ($thumbnail_id) 
                $thumb = wp_get_attachment_image( $thumbnail_id, array($width, $height), true ); 
            elseif ($attachments) { 
                foreach ( $attachments as $attachment_id => $attachment ) { 
                    $thumb = wp_get_attachment_image( $attachment_id, array($width, $height), true ); 
                } 
            } 
            if ( isset($thumb) && $thumb ) { echo $thumb; } else { echo __('None'); 
        } 
    }
} 


     // for posts
add_filter( 'manage_posts_columns', 'AddThumbColumn' ); 
add_action( 'manage_posts_custom_column', 'AddThumbValue', 10, 2 ); 
/*     // for pages 
add_filter( 'manage_pages_columns', 'AddThumbColumn' ); 
add_action( 'manage_pages_custom_column', 'AddThumbValue', 10, 2 );*/ 
}


/*-------------------------------------------------------------------------------------------*/
/* function to return a custom field value.
/*-------------------------------------------------------------------------------------------*/
function get_custom_field( $value ) {
    global $post;

    $custom_field = get_post_meta( $post->ID, $value, true );
    if ( !empty( $custom_field ) )
        return is_array( $custom_field ) ? stripslashes_deep( $custom_field ) : stripslashes( wp_kses_decode_entities( $custom_field ) );

    return false;
}

/*-------------------------------------------------------------------------------------------*/
/* Register the Custom Fields for Custom Post Types
/*-------------------------------------------------------------------------------------------*/
function add_custom_meta_box() {
    add_meta_box( 'meta-box-for-details', __( 'Details', 'sdes' ), 'meta_box_output_for_news', 'news_list' , 'normal', 'high' );
    add_meta_box( 'meta-box-for-details', __( 'Details', 'sdes' ), 'meta_box_output_for_staff', 'staff_list' , 'normal', 'high' );
}

// Enqueue Datepicker + jQuery UI CSS
wp_enqueue_script( 'jquery-ui-datepicker' );
wp_enqueue_style( 'jquery-ui-style', 'http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.1/themes/smoothness/jquery-ui.css', true);

add_action( 'add_meta_boxes', 'add_custom_meta_box' );                                     

/*-------------------------------------------------------------------------------------------*/
/* Output the Meta box for news
/*-------------------------------------------------------------------------------------------*/
function meta_box_output_for_news($post) {
    // create a nonce field
    wp_nonce_field('my_news_nonce', 'news_nonce'); ?>  
    
    <p>
        <label for="news_strapline">Strapline:</label><br>
        <input style="width:95%;"  type="text" name="news_strapline" id="news_strapline" value="<?= get_custom_field('news_strapline'); ?>" >
    </p>

    <p>
        <label for="news_link">Link:</label><br>
        <input style="width:95%;" type="text" name="news_link" id="news_link" value="<?= get_custom_field('news_link'); ?>" >
    </p>
    
    <div style="float:left; width:49%">
        <label for="news_start_date">Start Date:</label><br>
        <input style="width:95%;" type="text" class="date" name="news_start_date" id="news_start_date" value="<?= get_custom_field('news_start_date'); ?>" >
    </div>

    <div style="float:left; width:49%">
        <label for="news_end_date">End Date:</label><br>
        <input style="width:95%;" type="text" class="date" name="news_end_date" id="news_end_date" value="<?= get_custom_field('news_end_date'); ?>" >
    </div>
    <br><br><br>
    <script>
        jQuery(document).ready(function(){
            jQuery('.date').datepicker({
                minDate: '0d',
                dateFormat : 'mm-dd-yy'
            });
        });
    </script>
    
    <?php

}

/*-------------------------------------------------------------------------------------------*/
/* Save the Meta box values for news
/*-------------------------------------------------------------------------------------------*/
function meta_box_news_save( $post_id ) {
    // Stop the script when doing autosave
    if( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;

    // Verify the nonce. If insn't there, stop the script
    if( !isset( $_POST['news_nonce'] ) || !wp_verify_nonce( $_POST['news_nonce'], 'my_news_nonce' ) ) return;

    // Stop the script if the user does not have edit permissions
    if( !current_user_can( 'edit_post', get_the_id() ) ) return;

    // Save the news_strapline
    if( isset( $_POST['news_strapline'] ) )
        update_post_meta( $post_id, 'news_strapline', esc_attr( $_POST['news_strapline'] ) );

    // Save the news_start_date
    if( isset( $_POST['news_start_date'] ) )
        update_post_meta( $post_id, 'news_start_date', esc_attr( $_POST['news_start_date'] ) );

    // Save the news_end_date
    if( isset( $_POST['news_end_date'] ) )
        update_post_meta( $post_id, 'news_end_date', esc_attr( $_POST['news_end_date'] ) );

    // Save the news_link
    if( isset( $_POST['news_link'] ) )
        update_post_meta( $post_id, 'news_link', esc_attr( $_POST['news_link'] ) );

}

add_action( 'save_post', 'meta_box_news_save' );

/*-------------------------------------------------------------------------------------------*/
/* Output the Meta box for news
/*-------------------------------------------------------------------------------------------*/
function meta_box_output_for_staff($post) {
    // create a nonce field
    wp_nonce_field('my_staff_nonce', 'staff_nonce'); ?>  
    
    <div>
        <label for="position_title">Position Title:</label><br>
        <input style="width:95%;"  type="text" name="position_title" id="position_title" value="<?= get_custom_field('position_title'); ?>" >
    </div>

    <div style="float:left; width:49%">
        <label for="staff_email">Email:</label><br>
        <input style="width:95%;" type="text" name="staff_email" id="staff_email" value="<?= get_custom_field('staff_email'); ?>" >
    </div>

    <div style="float:left; width:49%">
        <label for="staff_phone">Phone:</label><br>
        <input style="width:95%;" type="text" name="staff_phone" id="staff_phone" value="<?= get_custom_field('staff_phone'); ?>" >
    </div>

    <br><br><br>
    
    <?php

}

/*-------------------------------------------------------------------------------------------*/
/* Save the Meta box values for news
/*-------------------------------------------------------------------------------------------*/
function meta_box_staff_save( $post_id ) {
    // Stop the script when doing autosave
    if( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;

    // Verify the nonce. If insn't there, stop the script
    if( !isset( $_POST['staff_nonce'] ) || !wp_verify_nonce( $_POST['staff_nonce'], 'my_staff_nonce' ) ) return;

    // Stop the script if the user does not have edit permissions
    if( !current_user_can( 'edit_post', get_the_id() ) ) return;

    // Save the position_title
    if( isset( $_POST['position_title'] ) )
        update_post_meta( $post_id, 'position_title', esc_attr( $_POST['position_title'] ) );

    // Save the staff_email
    if( isset( $_POST['staff_email'] ) )
        update_post_meta( $post_id, 'staff_email', esc_attr( $_POST['staff_email'] ) );

    // Save the staff_phone
    if( isset( $_POST['staff_phone'] ) )
        update_post_meta( $post_id, 'staff_phone', esc_attr( $_POST['staff_phone'] ) );

}

add_action( 'save_post', 'meta_box_staff_save' );

// Replaces the excerpt "[Read more]" text by a link
function new_excerpt_more($more) {
   global $post;

   return ' ... <br><br><a class="moretag" href="'. get_permalink($post->ID) . '">[Read more]</a>';
}
add_filter('excerpt_more', 'new_excerpt_more');

/*-------------------------------------------------------------------------------------------*/
/* Reads in and displays department information
/*-------------------------------------------------------------------------------------------*/
function get_department_info($action = NULL) {
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
                if( $department->acronym == $action){
                    $yield .= '<tr><th scope="row">Hours</th>';
                    $yield .= '<td>' . html_site_hours($department->hours) . '</td>';
                    $yield .= '</tr><tr>';
                    $yield .= '<th scope="row">Phone</th>';
                    $yield .= "<td>{$department->phone}</td>";
                    $yield .= '</tr><tr>';
                    $yield .= '<th scope="row">Fax</th>';
                    $yield .= "<td>{$department->fax}</td>";
                    $yield .= '</tr><tr>';
                    $yield .= '<th scope="row">Email</th>';
                    $yield .= "<td><a href='mailto:{$department->email}'>{$department->email}</a></td>";
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
            $yield .= "<td>{$department->phone}</td>";
            $yield .= '</tr><tr>';
            $yield .= '<th scope="row">Fax</th>';
            $yield .= "<td>{$department->fax}</td>";
            $yield .= '</tr><tr>';
            $yield .= '<th scope="row">Email</th>';
            $yield .= "<td><a href='mailto:{$department->email}'>{$department->email}</a></td>";
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

/*-------------------------------------------------------------------------------------------*/
/* Display hours from directory feed
/*-------------------------------------------------------------------------------------------*/
function html_site_hours($hours){
    $output = null;
    $collections = [];
    $final = [];

    //value-to-day conversion array
    $names = ['Mon', 'Tues', 'Wed', 'Thur', 'Fri', 'Sat', 'Sun'];

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

/*-------------------------------------------------------------------------------------------*/
/* wrapper for calendar feed
/*-------------------------------------------------------------------------------------------*/
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

//new options for admins/////////////////////////////////////////////////////////////////////////////////////
add_action( 'admin_menu', 'my_admin_menu' );
function my_admin_menu() {
    add_options_page( 'SDES Theme Settings', 'SDES Theme Settings', 'manage_options', 'sdes_settings', 'sdes_settings_render' );
}

add_action( 'admin_init', 'my_admin_init' );
function my_admin_init() {
    register_setting( 'sdes_setting_group', 'sdes_theme_settings' );
    add_settings_section( 'sdes_section_one', 'SDES Theme Settings', 'section_one_callback', 'sdes_settings' );
    add_settings_field( 'sdes_theme_settings_subtile', 'Subtile', 'subtitle_callback', 'sdes_settings', 'sdes_section_one' );
    add_settings_field( 'sdes_theme_settings_ga_id', 'google_analytics_id', 'google_analytics_id_callback', 'sdes_settings', 'sdes_section_one' );
    add_settings_field( 'sdes_theme_settings_js', 'javascript', 'javascript_callback', 'sdes_settings', 'sdes_section_one' );
    add_settings_field( 'sdes_theme_settings_js_lib', 'javascript_libraries', 'javascript_libraries_callback', 'sdes_settings', 'sdes_section_one' );
    add_settings_field( 'sdes_theme_settings_css', 'css', 'css_callback', 'sdes_settings', 'sdes_section_one' );
    add_settings_field( 'sdes_theme_settings_dir_acronym', 'directory_cms_acronym', 'directory_cms_acronym_callback', 'sdes_settings', 'sdes_section_one' );
}

function section_one_callback() {
    echo 'Some help text goes here.';
}

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
    <div class="wrap">
        <h2>SDES Theme Settings</h2>
        <form action="options.php" method="POST">
            <?php settings_fields( 'sdes_setting_group' ); ?>
            <?php do_settings_sections( 'sdes_settings' ); ?>
            <?php submit_button(); ?>
        </form>
    </div>
    <?php
}
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////

?>