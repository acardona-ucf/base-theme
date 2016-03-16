<?php
// require_once('functions/class-sdes-static.php');

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
  wp_enqueue_style( 'jquery-ui-style', 'http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.1/themes/smoothness/jquery-ui.css', true);
}



require_once('custom-taxonomies.php');    // Define and Register taxonomies for this theme

require_once('custom-posttypes.php');  // Define and Register custom post_type's (CPTs) for this theme
//TODO: extract Custom Post Type classes to their own file.

/* Add thumbnail column to admin page */
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
            if ( isset($thumb) && $thumb ) { echo $thumb; } else { echo __('None'); } 
        }
    } 
    // for posts
    add_filter( 'manage_posts_columns', 'AddThumbColumn' ); 
    add_action( 'manage_posts_custom_column', 'AddThumbValue', 10, 2 ); 
    /*     // for pages 
    add_filter( 'manage_pages_columns', 'AddThumbColumn' ); 
    add_action( 'manage_pages_custom_column', 'AddThumbValue', 10, 2 );*/ 
}


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
/* Reads in and displays department information */
// TODO: refactor out `get_department_info('menu')` and `get_department_info('ACRONYMN')` functionality.
// TODO: refactor HTML to use "View-and-Context" pattern instead of stringbuilding pattern.
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
require_once('functions/admin.php'); // Admin/login functions
require_once('shortcodes.php');


require_once( 'functions/class-render-template.php' );

//TODO: add screenshot.png for Theme.
