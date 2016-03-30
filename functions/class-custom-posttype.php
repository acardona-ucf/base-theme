<?php
/**
 * Abstract base class for creating custom posttypes.
 * This class is central the a theme's fuctionality, and heavily relies on other files.
 * @filesource
 */
namespace SDES;

require_once( get_stylesheet_directory().'/functions/class-sdes-metaboxes.php' );
	use SDES\SDES_Metaboxes;
	class_alias('SDES\\SDES_Metaboxes', 'SDES_Metaboxes');  // Allows calling class_exists() without any changes.

require_once( get_stylesheet_directory().'/functions/class-shortcodebase.php' );
	use SDES\Shortcodes\ShortcodeBase;
	class_alias('SDES\\Shortcodes\\ShortcodeBase', 'ShortcodeBase');

require_once( get_stylesheet_directory().'/functions/class-sdes-static.php' );
	use SDES\SDES_Static as SDES_Static;

require_once( get_stylesheet_directory().'/vendor/autoload.php' );
use Underscore\Types\Object;
use Underscore\Types\Arrays;

/**
 * Abstract class for defining custom post types.
 *
 * @see SDES_Metaboxes::$installed_custom_post_types
 * @see SDES_Metaboxes::show_meta_boxes (calls SDES_Metaboxes::display_meta_box_field)
 * @see SDES_Static::instantiate_and_register_classes()
 * Based on: https://github.com/UCF/Students-Theme/blob/6ca1d02b062b2ee8df62c0602adb60c2c5036867/custom-post-types.php#L1-L242
 **/
abstract class CustomPostType {
	public
		$name           = 'custom_post_type',
		$plural_name    = 'Custom Posts',
		$singular_name  = 'Custom Post',
		$add_new_item   = 'Add New Custom Post',
		$edit_item      = 'Edit Custom Post',
		$new_item       = 'New Custom Post',
		$public         = True,  // I dunno...leave it true
		$use_title      = True,  // Title field
		$use_editor     = True,  // WYSIWYG editor, post content field
		$use_revisions  = True,  // Revisions on post content and titles
		$use_thumbnails = False, // Featured images
		$use_order      = False, // Wordpress built-in order meta data
		$use_metabox    = False, // Enable if you have custom fields to display in admin
		$use_shortcode  = False, // Auto generate a shortcode for the post type
								 // (see also objectsToHTML and toHTML methods)
		$taxonomies     = array( 'post_tag' ),
		$built_in       = False,
		// Optional default ordering for generic shortcode if not specified by user.
		$default_orderby = null,
		$default_order   = null,
		// Interface Columns/Fields
		$calculated_columns = array(
			array( 'heading'=>'Thumbnail', 'column_name'=>'_thumbnail_id', 'custom_column_order'=>100 ),
			// array( 'heading'=>'A Column Heading Text', 'column_name'=>'id_of_the_column' ),
		), // Calculate values within custom_column_echo_data.
		$sc_interface_fields = null; // Fields for shortcodebase interface (false hides from list, null shows only the default fields).

	/**
	 * Wrapper for get_posts function, that predefines post_type for this
	 * custom post type.  Any options valid in get_posts can be passed as an
	 * option array.  Returns an array of objects.
	 * */
	public function get_objects( $options=array() ) {
		$defaults = array(
			'numberposts'   => -1,
			'orderby'       => 'title',
			'order'         => 'ASC',
			'post_type'     => $this->options( 'name' ),
		);
		$options = array_merge( $defaults, $options );
		$objects = get_posts( $options );
		return $objects;
	}

	/**
	 * Similar to get_objects, but returns array of key values mapping post
	 * title to id if available, otherwise it defaults to id=>id.
	 **/
	public function get_objects_as_options( $options ) {
		$objects = $this->get_objects( $options );
		$opt     = array();
		foreach ( $objects as $o ) {
			switch ( True ) {
			case $this->options( 'use_title' ):
				$opt[$o->post_title] = $o->ID;
				break;
			default:
				$opt[$o->ID] = $o->ID;
				break;
			}
		}
		return $opt;
	}

	/**
	 * Return the instances values defined by $key.
	 * */
	public function options( $key ) {
		$vars = get_object_vars( $this );
		return $vars[$key];
	}

	/**
	 * Additional fields on a custom post type may be defined by overriding this
	 * method on an descendant object.
	 * @see SDES_Metaboxes::display_metafield() for field types.
	 * */
	public function fields() {
		return array();
	}

	/**
	 * Using instance variables defined, returns an array defining what this
	 * custom post type supports.
	 * */
	public function supports() {
		// Default support array
		$supports = array();
		if ( $this->options( 'use_title' ) ) {
			$supports[] = 'title';
		}
		if ( $this->options( 'use_order' ) ) {
			$supports[] = 'page-attributes';
		}
		if ( $this->options( 'use_thumbnails' ) ) {
			$supports[] = 'thumbnail';
		}
		if ( $this->options( 'use_editor' ) ) {
			$supports[] = 'editor';
		}
		if ( $this->options( 'use_revisions' ) ) {
			$supports[] = 'revisions';
		}
		return $supports;
	}

	/**
	 * Creates labels array, defining names for admin panel.
	 * */
	public function labels() {
		return array(
			'name'          => __( $this->options( 'plural_name' ) ),
			'singular_name' => __( $this->options( 'singular_name' ) ),
			'add_new_item'  => __( $this->options( 'add_new_item' ) ),
			'edit_item'     => __( $this->options( 'edit_item' ) ),
			'new_item'      => __( $this->options( 'new_item' ) ),
		);
	}

	/**
	 * Creates metabox array for custom post type. Override method in
	 * descendants to add or modify metaboxes.
	 * */
	public function metabox() {
		if ( $this->options( 'use_metabox' ) ) {
			return array(
				'id'       => 'custom_'.$this->options( 'name' ).'_metabox',
				'title'    => __( $this->options( 'singular_name' ).' Fields' ),
				'page'     => $this->options( 'name' ),
				'context'  => 'normal',
				'priority' => 'high',
				'fields'   => $this->fields(),
			);
		}
		return null;
	}

	/**
	 * Registers metaboxes defined for custom post type.
	 * @see SDES_Metaboxes::show_meta_boxes (calls SDES_Metaboxes::display_meta_box_field)
	 * */
	public function register_metaboxes() {
		if ( $this->options( 'use_metabox' ) ) {
			$metabox = $this->metabox();
			add_meta_box(
				$metabox['id'],
				$metabox['title'],
				'SDES_Metaboxes::show_meta_boxes',
				$metabox['page'],
				$metabox['context'],
				$metabox['priority']
			);
		}
	}

	/**
	 * Show metaboxes that have the context 'after_title'.
	 * @see CustomPostType::do_meta_boxes_after_title()
	 */
	public static function register_meta_boxes_after_title() {
		add_action('edit_form_after_title', __CLASS__.'::do_meta_boxes_after_title');
	}

	/**
	 * Callback function to print metaboxes used by add_action('edit_form_after_title').
	 * @see CustomPostType::register_meta_boxes_after_title()
	 */
	public static function do_meta_boxes_after_title( $post ) {
		//global $post, $wp_meta_boxes; // Get the globals.
		do_meta_boxes( get_current_screen(), 'after_title', $post ); // Output meta boxes for the 'after_title' context.
	}


	/**
	 * Get an HTML img element representing an image attachment for this post.
	 * @param int $post_id The ID of the current post.
	 * @see custom_column_echo_data() custom_column_echo_data()
	 * @see http://developer.wordpress.org/reference/functions/wp_get_attachment_image/ WP_Ref: wp_get_attachment_image()
	 * @see http://developer.wordpress.org/reference/functions/get_post_meta/ WP_Ref: get_post_meta()
	 * @see http://codex.wordpress.org/Function_Reference/get_children WP_Codex: get_children()
	 * @usedby custom_column_echo_data()
	 * @return string An html IMG element or default text.
	 */
	public static function get_thumbnail_or_attachment_image( $post_id ) {
		$width = (int) 120; $height = (int) 120;
		$thumbnail_id = get_post_meta( $post_id, '_thumbnail_id', true ); 
		$attachments = get_children( array('post_parent' => $post_id, 'post_type' => 'attachment', 'post_mime_type' => 'image') ); 
		if ($thumbnail_id) 
			$thumb = wp_get_attachment_image( $thumbnail_id, array($width, $height), true ); 
		elseif ($attachments) { 
			foreach ( $attachments as $attachment_id => $attachment ) { 
				$thumb = wp_get_attachment_image( $attachment_id, array($width, $height), true ); 
			} 
		} 
		if ( isset($thumb) && $thumb ) { return $thumb; } else { return __('None'); }
	}

	/**
	 * Get all custom columns for this post type. Autogenerates columns for $this->fields() with a 'custom_column_order' key (note: the value must not be 0).
	 * @see custom_column_set_headings() custom_column_set_headings()
	 * @see http://anahkiasen.github.io/underscore-php/#Arrays-filter Underscore-php: filter
	 * @see http://anahkiasen.github.io/underscore-php/#Arrays-each Underscore-php: each
	 * @usedby custom_column_set_headings()
	 * @return Array Collection of Columns, each with the keys: 'heading', 'metafield', 'custom_column_order'.
	 */
	public function custom_columns_get_all() {
		$calculated_columns = $this->options( 'calculated_columns' );
		// Filter and Map fields to custom columns.
		$field_columns = Arrays::from( $this->fields() )
			->filter( function( $x ) { return array_key_exists( 'custom_column_order', $x ) && $x['custom_column_order']; } )
			->each( function( $field ) {
				$custom_column_order = isset( $field['custom_column_order'] ) ? $field['custom_column_order'] : 1000;
				return array( 'heading' => $field['name'], 'column_name' => $field['id'], 'custom_column_order' => $custom_column_order );
			})->obtain();
		$columns = Arrays::sort( array_merge( $calculated_columns, $field_columns ), 'custom_column_order' );
		return $columns;
	}

	/**
	 * Add headers for custom columns to an index page (built-in listing of posts with this post_type).
	 * @see custom_columns_get_all() custom_columns_get_all()
	 * @uses custom_columns_get_all()
	 * @param array $columns An array of column name ⇒ label. The name is passed to functions to identify the column. The label is shown as the column header.
	 * @return array The updated column names and labels.
	 */
	public function custom_column_set_headings( $columns ) {
		foreach ( $this->custom_columns_get_all() as $custom_column ) {
			$columns[ $custom_column['column_name'] ] = $custom_column['heading'];
		}
		return $columns;
	}

	/**
	 * Show the data for a single row ($post_id) of a column.
	 * @see get_thumbnail_or_attachment_image() get_thumbnail_or_attachment_image()
	 * @see http://developer.wordpress.org/reference/functions/get_post_meta/ WP_Ref: get_post_meta()
	 * @uses get_thumbnail_or_attachment_image()
	 * @param string $column  The name of the column to display.
	 * @param int    $post_id The ID of the current post.
	 * @return void  Echos the data for this column.
	 */
	public function custom_column_echo_data( $column, $post_id ) {
		switch ( $column ) {
			case '_thumbnail_id':
				echo esc_html( static::get_thumbnail_or_attachment_image( $post_id ) );
				break;
			default:
				echo esc_attr( get_post_meta( $post_id, $column, true ) );
				break;
		}
		return;
	}

	/**
	 * Add columns to an index page (built-in listing of posts with this post_type).
	 * @see http://codex.wordpress.org/Plugin_API/Filter_Reference/manage_$post_type_posts_columns WP_Codex: manage_$post_type_posts_columns
	 * @see http://codex.wordpress.org/Plugin_API/Action_Reference/manage_$post_type_posts_custom_column WP_Codex: manage_$post_type_posts_custom_column
	 * @uses options(), $name.
	 */
	public function register_custom_columns() {
		$posttype = $this->options( 'name' );
		add_filter( "manage_{$posttype}_posts_columns", array( $this, 'custom_column_set_headings' ) );
		/* add_action( $tag, $function_to_add, $priority, $accepted_args ); // Signature. */
		add_action( "manage_{$posttype}_posts_custom_column" , array( $this, 'custom_column_echo_data' ), 10, 2 );
	}

	/**
	 * Registers the custom post type and any other ancillary actions that are
	 * required for the post to function properly.
	 * @see http://codex.wordpress.org/Function_Reference/register_post_type Codex - register_post_type()
	 * @see http://codex.wordpress.org/Function_Reference/add_shortcode Codex - add_shortcode()
	 * @uses labels(), supports(), options(), custom_columns_get_all(), register_custom_columns()
	 * @uses $public, $taxonomies, $built_in, $use_order, $name, $use_shortcode
	 * @param Array $args Override the registration args passed to register_post_type.
	 * */
	public function register( $args = array() ) {
		// Register the post type.
		$registration = array(
			'labels'     => $this->labels(),
			'supports'   => $this->supports(),
			'public'     => $this->options( 'public' ),
			'taxonomies' => $this->options( 'taxonomies' ),
			'_builtin'   => $this->options( 'built_in' ),
		);
		if ( $this->options( 'use_order' ) ) {
			$registration = array_merge( $registration, array( 'hierarchical' => true ) );
		}
		$registration = array_merge( $registration, $args );
		register_post_type( $this->options( 'name' ), $registration );
		// Register custom columns.
		$custom_columns = $this->custom_columns_get_all();
		if ( null !== $custom_columns && isset( $custom_columns[0] ) ) { $this->register_custom_columns(); }
		// Add shortcode.
		if ( $this->options( 'use_shortcode' ) ) {
			add_shortcode( $this->options( 'name' ).'-list', array( $this, 'shortcode' ) );
		}
	}

	/**
	 * Shortcode for this custom post type.  Can be overridden for descendants.
	 * Defaults to just outputting a list of objects outputted as defined by
	 * toHTML method.
	 * @see CustomPostType::objectsToHTML
	 * @see CustomPostType::toHTML
	 * */
	public function shortcode( $attr ) {
		$default = array(
			'type' => $this->options( 'name' ),
		);
		if ( is_array( $attr ) ) {
			$attr = array_merge( $default, $attr );
		}else {
			$attr = $default;
		}
		$args = array( 'classname' => __CLASS__, );
		return SDES_Static::sc_object_list( $attr, $args );
	}

	/**
	 * Static method that tries to call the correct instance method of objectsToHTML.
	 * @param string $classname Override the classname to instantiate the class.
	 */
	public static function tryObjectsToHTML( $objects, $css_classes, $classname = '' ) {
		if ( count( $objects ) < 1 ) { return (WP_DEBUG) ? '<!-- No objects were provided to objectsToHTML. -->' : '';}
		$classname = ( '' === $classname ) ? $objects[0]->post_type : $classname;
		if( class_exists($classname) ) {
			$class = new $classname;
			return $class->objectsToHTML( $objects, $css_classes );
		} else { return ''; }
	}

	/**
	 * Handles output for a list of objects, can be overridden for descendants.
	 * If you want to override how a list of objects are outputted, override
	 * this, if you just want to override how a single object is outputted, see
	 * the toHTML method.
	 * @param WP_Post $objects The post objects to display.
	 * @param string $css_classes List of css classes for the objects container.
	 * @see http://php.net/manual/en/language.oop5.late-static-bindings.php
	 * @see Always prefer `static::` over `self::` (in PHP 5.3+): http://stackoverflow.com/a/6807615
	 * */
	public function objectsToHTML( $objects, $css_classes ) {
		if ( count( $objects ) < 1 ) { return (WP_DEBUG) ? '<!-- No objects were provided to objectsToHTML. -->' : '';}
		$context['objects'] = $objects;
		$context['css_classes'] = ( $css_classes ) ? $css_classes : $this->options('name').'-list';
		return static::render_objects_to_html( $context );
	}

	/**
	 * Render HTML for a collection of objects.
	 * @param Array $context An array of sanitized variables to display with this view.
	 */
	protected static function render_objects_to_html( $context ){
		ob_start();
		?>
		<ul class="<?= $context['css_classes'] ?>">
			<?php foreach ( $context['objects'] as $o ):?>
			<li>
				<?= static::toHTML( $o ) ?>
			</li>
			<?php endforeach;?>
		</ul>
		<?php
		$html = ob_get_clean();
		return $html;
	}

	/**
	 * Outputs this item in HTML.  Can be overridden for descendants.
	 * @param WP_Post $object The post object to display.
	 * */
	public static function toHTML( $object ) {
		$context['permalink'] = get_permalink( $object->ID );
		$context['title'] = $object->post_title;
		return static::render_to_html( $context );
	}

	/**
	 * Render HTML for a single object.
	 * @param Array $context An array of sanitized variables to display with this view.
	 */
	protected static function render_to_html( $context ) {
		ob_start();
		?>
			<a href="<?= $context['permalink'] ?>"><?= $context['post_title'] ?></a>
		<?php
		$html = ob_get_clean();
		return $html;
	}


	/**
	 * @param Array $custom_posttypes Names of custom post types classes to register.
	 * @return  Array Array of instantiated posttype classes (array of arrays). Each item has the keys: 'classname', 'instance'.
	 * @see SDES_Static::instantiate_and_register_classes()
	 */
	public static function Register_Posttypes( $custom_posttypes ) {
		$posttype_instances = SDES_Static::instantiate_and_register_classes($custom_posttypes);
		foreach ($posttype_instances as $registered_posttype) {
			if (class_exists('SDES_Metaboxes') ) {
				SDES_Metaboxes::$installed_custom_post_types[] = $registered_posttype['instance'];
			}
			if (class_exists('ShortcodeBase') ) {
				ShortcodeBase::$installed_custom_post_types[] = $registered_posttype['instance'];
			}
		}
		static::Register_Thumbnails_Support($posttype_instances);
		return $posttype_instances;
	}

	/**
	 * @param Array $instances Instantiated classes for Custom Post Types.
	 * @return void
	 */
	public static function Register_Thumbnails_Support( $instances ) {
		// if the key $instances[0]['instance'] exists.
		if ( Arrays::has(Object::unpack($instances), 'instance') ) {
			$instances = Arrays::pluck($instances, 'instance');
		}

		$thumbnail_posttypes
		  = Arrays::from($instances)
			->filter( function($x) { return true === $x->use_thumbnails; })
			->pluck( 'name' )
			->obtain();
		add_theme_support( 'post-thumbnails', $thumbnail_posttypes );

		/** Scale thumbnails by default. */ define('SDES\\SCALE', false); 
		/** Crop thumbnails by default. */  define('SDES\\CROP', true);
		// For cropping behavior see `add_image_size`, e.g.: https://core.trac.wordpress.org/browser/tags/4.4.2/src/wp-includes/media.php#L228
		set_post_thumbnail_size( 125, 125, CROP );
		// $crop_from = array( 'top', 'left');
		// set_post_thumbnail_size( 125, 125, $crop_from );

	}
}

CustomPostType::register_meta_boxes_after_title();
