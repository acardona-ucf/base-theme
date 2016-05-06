<?php
/**
 *  Add and configure custom posttypes for this theme.
 */
namespace SDES\BaseTheme\PostTypes;
use \WP_Query;
use SDES\SDES_Static as SDES_Static;
require_once( get_stylesheet_directory().'/functions/class-sdes-metaboxes.php' );
	use SDES\SDES_Metaboxes;
require_once( get_stylesheet_directory().'/functions/class-custom-posttype.php' );
	use SDES\CustomPostType;

/**
 * The built-in post_type named 'Post'.
 */
class Post extends CustomPostType {
	public
		$name           = 'post',
		$plural_name    = 'Posts',
		$singular_name  = 'Post',
		$add_new_item   = 'Add New Post',
		$edit_item      = 'Edit Post',
		$new_item       = 'New Post',
		$public         = True, 
		$use_title      = True, 
		$use_editor     = True,  // WYSIWYG editor, post content field
		$use_revisions  = True,  // Revisions on post content and titles
		$use_thumbnails = True, // Featured images
		$use_order      = True,  // Wordpress built-in order meta data
		$use_metabox    = True,  // Enable if you have custom fields to display in admin
		$use_shortcode  = False, // Auto generate a shortcode for the post type
								 // (see also objectsToHTML and toHTML methods)
		$taxonomies     = array('post_tag', 'category'),
		$built_in       = True,
		// Optional default ordering for generic shortcode if not specified by user.
		$default_orderby = null,
		$default_order   = null;
}

/**
 * The built-in post_type named 'Page'.
 */
class Page extends CustomPostType {
	public
		$name           = 'page',
		$plural_name    = 'Pages',
		$singular_name  = 'Page',
		$add_new_item   = 'Add New Page',
		$edit_item      = 'Edit Page',
		$new_item       = 'New Page',
		$public         = True,
		$use_editor     = True,
		$use_thumbnails = False,
		$use_order      = True,
		$use_title      = True,
		$use_metabox    = True,
		$built_in       = True;

	public function fields() {
		$prefix = $this->options('name').'_';
		return array(
			array(
				'name'  => 'Side Column',
				'descr' => 'Show content in column to the right or left of the page (e.g., menuPanels).',
				'id'    => $prefix.'sidecolumn',
				'type'  => 'editor',
				'args'  => array('tinymce'=>true,),
			),
		);
	}
}

/**
 * An alert bar displayed at the top of a page.
 */
class Alert extends CustomPostType {
	public
		$name           = 'alert',
		$plural_name    = 'Alerts',
		$singular_name  = 'Alert',
		$add_new_item   = 'Add New Alert',
		$edit_item      = 'Edit Alert',
		$new_item       = 'New Alert',
		$public         = True,  // I dunno...leave it true
		$use_title      = True,  // Title field
		$use_editor     = True,  // WYSIWYG editor, post content field
		$use_revisions  = True,  // Revisions on post content and titles
		$use_thumbnails = True,  // Featured images
		$use_order      = False, // Wordpress built-in order meta data
		$use_metabox    = True,  // Enable if you have custom fields to display in admin
		$use_shortcode  = True,  // Auto generate a shortcode for the post type
		                         // (see also objectsToHTML and toHTML methods)
		$taxonomies     = array( 'post_tag' ),
		$menu_icon      = 'dashicons-warning',
		$built_in       = False,
		// Optional default ordering for generic shortcode if not specified by user.
		$default_orderby = null,
		$default_order   = null,
		// Interface Columns/Fields
		$calculated_columns = array(), // Empty array to hide thumbnails.
		$sc_interface_fields = false;

	// TODO: convert checkboxes from 'checkbox_list' to 'checkbox' after it is implemented.
	public function fields() {
		$prefix = $this->options('name').'_';
		return array(
			array(
				'name' => 'Unplanned Alert',
				'descr' => 'If checked, show the alert as red instead of yellow.',
				'id' => $prefix.'is_unplanned',
				'type' => 'checkbox_list',
				'choices' => array(
					'Unplanned alert.' => $prefix.'is_unplanned' 
				),
				'custom_column_order' => 400,
			),
			array(
				'name' => 'Sitewide Alert',
				'descr' => 'Show alert across the entire site.',
				'id' => $prefix.'is_sitewide',
				'type' => 'checkbox_list',
				'choices' => array(
					'Sitewide alert.' => $prefix.'is_sitewide'
				),
				'custom_column_order' => 300,
			),
			array(
				'name' => 'Start Date',
				'descr' => 'The first day the alert should appear.',
				'id' => $prefix.'start_date',
				'type' => 'date',
				'custom_column_order' => 100,
			),
			array(
				'name' => 'End Date',
				'descr' => 'The last day the alert should appear.',
				'id' => $prefix.'end_date',
				'type' => 'date',
				'custom_column_order' => 200,
			),
			array(
				'name' => 'URL',
				'descr' => 'Add a link for this alert.',
				'id' => $prefix.'url',
				'type' => 'text',
				'default' => 'http://',
			),
		);
	}

	public function custom_column_echo_data( $column, $post_id ) {
		$prefix = $this->options( 'name' ) . '_';
		switch ( $column ) {
			case $prefix.'is_unplanned':
			case $prefix.'is_sitewide':
				$data = get_post_meta( $post_id, $column, true );
				$checked = ( '' !== $data ) ? "Yes" : '&mdash;';
				echo wp_kses_data( "{$checked}" );
				break;
			default:
				parent::custom_column_echo_data( $column, $post_id );
				break;
		}
	}

	public function shortcode( $attr ) {
		$prefix = $this->options('name').'_';
		$default_attrs = array(
			'type' => $this->options( 'name' ),
			'orderby' => 'meta_value_datetime',
			'meta_key' => $prefix.'start_date',
			'order' => 'ASC',
			'meta_query' => array(
				'relation' => 'AND',
				array(
					'key' => $prefix.'start_date',
					'value' => date( 'Y-m-d', time() ),
					'compare' => '<=',
				),
				array(
					'key' => $prefix.'end_date',
					'value' => date( 'Y-m-d', strtotime('-1 day') ), // Datetime is stored as 24 hours before it should expire.
					'compare' => '>=',
				),
			),
			'show_all'=> false,
		);
		if ( is_array( $attr ) ) {
			$attr = array_merge( $default_attrs, $attr );
		}else {
			$attr = $default_attrs;
		}
		$attr['show_all'] = filter_var( $attr['show_all'], FILTER_VALIDATE_BOOLEAN);
		if ( ! $attr['show_all'] ) {
			array_push( $attr['meta_query'],
				array(
					'key' => $prefix.'is_sitewide',
					// Remember that Checkbox list values are serialized.
					// See: https://wordpress.org/support/topic/meta_query-doesnt-find-value-if-custom-field-value-is-serialized#post-2106580
					'value' => serialize(strval($prefix.'is_sitewide')),
					'compare' => 'LIKE', )
			);
		}
		// Unset custom attributes.
		unset( $attr['show_all'] );
		$args = array( 'classname' => __CLASS__, );
		return SDES_Static::sc_object_list( $attr, $args );
	}

	/**
	 * Return an array of only the metadata fields used to create a render context.
	 * @param WP_Post $alert The alert whose metadata should be retrieved.
	 * @return Array The fields to pass into get_render_context.
	 */
	private static function get_render_metadata( $alert ) {
		$metadata_fields = array();
		$metadata_fields['alert_is_unplanned'] = get_post_meta($alert->ID, 'alert_is_unplanned', true);
		$metadata_fields['alert_url'] = esc_attr( get_post_meta($alert, 'alert_url', true) );
		return $metadata_fields;
	}

	/**
	 * Generate a render context for an alert, given its WP_Post object and an array of its metadata fields.
	 * Expected fields:
	 * $alert - post_content, post_title
	 * $metadata_fields - alert_is_unplanned, alert_url
	 * @param WP_Post $alert The post object to be displayed.
	 * @param Array $metadata_fields The metadata fields associated with this alert.
	 */
	public static function get_render_context( $alert, $metadata_fields ) {
		$alert_css_classes = 
			( $metadata_fields['alert_is_unplanned'] )
			? 'alert-danger' : 'alert-warning';
		$alert_url = $metadata_fields['alert_url'];
		$alert_url = SDES_Static::url_ensure_prefix( $alert_url );
		if( false == strrpos($alert_url, '//') ) { $alert_url = 'http://'.$alert_url; }
		$alert_message = $alert->post_content;
		$alert_message = 
			(true !== $alert_url && '' != $alert_url )
			? sprintf( '<a href="%1$s" class="external">%2$s</a>', $alert_url, $alert_message)
			: $alert_message;
		return array(
			'css_classes' => $alert_css_classes,
			'title' => $alert->post_title,
			'message' => $alert_message,
		);
	}

	public function objectsToHTML( $objects, $css_classes ) {
		if ( count( $objects ) < 1 ) { return (WP_DEBUG) ? '<!-- No objects were provided to objectsToHTML. -->' : '';}
		$context['css_classes'] = ( $css_classes ) ? $css_classes : $this->options('name').'-list';

		foreach ($objects as $alert) {
			$metadata_fields = static::get_render_metadata( $alert );
			$context['alert_contexts'][] = static::get_render_context( $alert, $metadata_fields );
		}
		return static::render_objects_to_html( $context );
	}

	/**
	 * Render HTML for a collection of alerts.
	 */
	protected static function render_objects_to_html( $context ){
		ob_start();
		?>
			<span class="<?= $context['css_classes'] ?>">
				<?php foreach ( $context['alert_contexts'] as $alert ):
					echo static::render_to_html( $alert );
				endforeach; ?>
			</span>
		<?php
		$html = ob_get_clean();
		return $html;
	}

	public static function toHTML ( $post_object ) {
		$metadata_fields = static::get_render_metadata( $post_object );
		$alert_context = static::get_render_context( $post_object, $metadata_fields );
		return static::render_to_html( $alert_context );
	}

	/**
	 * Render HTML for a single alert.
	 */
	protected static function render_to_html( $context ) {
		ob_start();
		?>
		<div class="alert <?= $context['css_classes'] ?>">
			<p>
				<strong><?= $context['title'] ?></strong>
				<?= $context['message'] ?>
			</p>
		</div>
		<div class="clearfix"></div>
		<?php
		$html = ob_get_clean();
		return $html;
	}
}

/**
 * A single billboard slide to be displayed in a carousel, such as the NivoSlider jQuery plugin.
 */
class Billboard extends CustomPostType {
	public
		$name           = 'billboard',
		$plural_name    = 'Billboards',
		$singular_name  = 'Billboard',
		$add_new_item   = 'Add New Billboard',
		$edit_item      = 'Edit Billboard',
		$new_item       = 'New Billboard',
		$public         = True,  // I dunno...leave it true
		$use_title      = True,  // Title field
		$use_editor     = True,  // WYSIWYG editor, post content field
		$use_revisions  = True,  // Revisions on post content and titles
		$use_thumbnails = True,  // Featured images
		$use_order      = False, // Wordpress built-in order meta data
		$use_metabox    = True,  // Enable if you have custom fields to display in admin
		$use_shortcode  = True,  // Auto generate a shortcode for the post type
		                         // (see also objectsToHTML and toHTML methods)
		$taxonomies     = array( 'post_tag' ),
		$menu_icon      = 'dashicons-slides',
		$built_in       = False,
		// Optional default ordering for generic shortcode if not specified by user.
		$default_orderby = null,
		$default_order   = null;

	public function fields() {
		$prefix = $this->options('name').'_';
		return array(
			array(
				'name' => 'URL',
				'descr' => 'Add a link for this billboard.',
				'id' => $prefix.'url',
				'type' => 'text',
				'default' => 'http://',
			),
			array(
				'name' => 'Start Date',
				'descr' => 'The billboard will be shown starting on this date.',
				'id' => $prefix.'start_date',
				'type' => 'date',
				'custom_column_order' => 200,
				'default' => date( 'Y-m-d', time() ),
			),
			array(
				'name' => 'End Date',
				'descr' => 'Stop showing the billboard after this date.',
				'id' => $prefix.'end_date',
				'type' => 'date',
				'custom_column_order' => 300,
			),
		);
	}

	public function register_metaboxes() {
		parent::register_metaboxes();

		// Move and Rename the Featured Image Metabox.
		remove_meta_box( 'postimagediv', $this->name, 'side' );
		add_meta_box('postimagediv', __("{$this->singular_name} Image"),
			'post_thumbnail_meta_box', $this->name, 'after_title', 'high');
		CustomPostType::register_meta_boxes_after_title();
	}

	public function shortcode( $attr ) {
		$prefix = $this->options('name').'_';
		$default_attrs = array(
			'type' => $this->options( 'name' ),
			'orderby' => 'meta_value_datetime',
			'meta_key' => $prefix.'start_date',
			'order' => 'ASC',
			'meta_query' => array(
				'relation' => 'AND',
				array(
					'key' => $prefix.'start_date',
					'value' => date( 'Y-m-d', time() ),
					'compare' => '<=',
				),
				array(
					'relation' => 'OR',
					array(
						'key' => $prefix.'end_date',
						'value' => date( 'Y-m-d', strtotime('-1 day') ), // Datetime is stored as 24 hours before it should expire.
						'compare' => '>=',
					),
					array(
						'key' => $prefix.'end_date',
						'compare' => 'NOT EXISTS', // Allow perpetual billboards.
					),
				)
			),
		);
		if ( is_array( $attr ) ) {
			$attr = array_merge( $default_attrs, $attr );
		}else {
			$attr = $default_attrs;
		}
		$args = array( 'classname' => __CLASS__, );
		return SDES_Static::sc_object_list( $attr, $args );
	}

	public function objectsToHTML( $objects, $css_classes ) {
		if ( count( $objects ) < 1 ) { return (WP_DEBUG) ? '<!-- No objects were provided to objectsToHTML. -->' : '';}
		$context['objects'] = $objects;
		$context['css_classes'] = ( $css_classes ) ? $css_classes : $this->options('name').'-list';
		return static::render_objects_to_html( $context );
	}

	protected static function render_objects_to_html( $context ){
		// TODO: don't show nivoslider directionNav if only 1 Billboard slide.
		$BILLBOARD_SIZE = array( 1140, 318 );
		$billboard_count = 0;
		foreach ( $context['objects'] as $o ) {
			// Extend WP_Post objects to save query results.
			$o->has_post_thumbnail = has_post_thumbnail( $o );
			if ( $o->has_post_thumbnail ) {
				$billboard_count++;
				$o->alt_text = SDES_Static::get_post_thumbnail_alttext( $o->ID, 'Billboard '. $billboard_count );
				$o->billboard_url = get_post_meta( $o, 'billboard_url', true );
				$o->billboard_url = ( $o->billboard_url ) ? SDES_Static::url_ensure_prefix( $o->billboard_url ) : false;
				$o->is_caption_valid = (! SDES_Static::is_null_or_whitespace( $o->post_title )  || ! SDES_Static::is_null_or_whitespace( $o->post_content ));
			}
		}
		ob_start();
		?>
		<!-- nivo slider -->
		<script type="text/javascript">
			$(window).load(function() {
				$('#slider').nivoSlider({
					slices: <?= $billboard_count ?>,
					pauseTime: 5000,
					controlNav: false,
					captionOpacity: 0.7,
					<?php if ( 1 === $billboard_count ) : ?>
					directionNav: false,
					<?php endif; ?>
				});
				$('#slider').show();

				// Remove accessibility errors.
				$('.nivo-main-image').attr('alt', 'Rotating Billboard');
				$('.nivo-slice img').attr('alt', '');
			});
		</script>
		<div class="container site-billboard theme-default">
			<div id="slider" class="nivoSlider" style="display: none;">
			<?php foreach ( $context['objects'] as $o ):
				if ( $o->has_post_thumbnail ) :
					if( $o->billboard_url ) : ?>
						<a href="<?= $o->billboard_url ?>" class="nivo-imageLink">
					<?php endif;
							$title = ( $o->is_caption_valid ) ? '#nivo-caption-'.$o->ID : null ;
							echo get_the_post_thumbnail( $o, $BILLBOARD_SIZE, 
									array('title'=> $title, 'alt' => $o->alt_text ) );
					if( $o->billboard_url ) : ?>
						</a>
					<?php endif;
				endif;
			endforeach; ?>
			</div>
			<?php foreach ( $context['objects'] as $o ):
				if ( $o->has_post_thumbnail ):
					if ( $o->is_caption_valid ) : ?>
					<div id="nivo-caption-<?= $o->ID ?>" class="nivo-html-caption">
						<div class="nivo-padding"></div>
						<div class="nivo-title"><?= $o->post_title ?></div>
						<div class="nivo-strapline"><?= $o->post_content ?></div>
					</div>
		  		<?php endif;
		  		endif;
			endforeach; ?>
			<div class="noscript">
				Billboards:<br><br>
				<?php echo "\n";
				foreach ( $context['objects'] as $o ):
					if( $o->billboard_url ) : ?>
						<a href="<?= $o->billboard_url ?>">
					<?php endif;
					echo "\t\t\t\t{$o->post_title} {$o->post_content} {$o->alt_text}<br><br>\n";
					if( $o->billboard_url ) : ?>
						</a>
					<?php endif;
				endforeach; ?>
			</div>
		</div>
		<?php
		return ob_get_clean();
	}
}

/**
 * An employee associated with this site.
 */
class Staff extends CustomPostType {
	public
		$name           = 'staff',
		$plural_name    = 'Staff',
		$singular_name  = 'Staff',
		$add_new_item   = 'Add New Staff',
		$edit_item      = 'Edit Staff',
		$new_item       = 'New Staff',
		$public         = True,  // I dunno...leave it true
		$use_title      = True,  // Title field
		$use_editor     = True,  // WYSIWYG editor, post content field
		$use_revisions  = True,  // Revisions on post content and titles
		$use_thumbnails = True,  // Featured images
		$use_order      = True, // Wordpress built-in order meta data
		$use_metabox    = True, // Enable if you have custom fields to display in admin
		$use_shortcode  = True, // Auto generate a shortcode for the post type
		                         // (see also objectsToHTML and toHTML methods)
		$taxonomies     = array( 'post_tag', 'org_groups' ),
		$menu_icon      = 'dashicons-groups',
		$built_in       = False,
		// Optional default ordering for generic shortcode if not specified by user.
		$default_orderby = null,
		$default_order   = null,
		$sc_interface_fields = array(
			array(
				'name' => 'Header',
				'id' => 'header',
				'help_text' => 'Show a header for above the staff list.',
				'type' => 'text',
				'default' => 'Staff List'
			),
			array(
				'name' => 'Collapse',
				'id' => 'collapse',
				'help_text' => 'Add a "[Read More] link for long "Details" sections.',
				'type' => 'dropdown',
				'choices' => array(
					array('value'=>'false', 'name'=>"Don't show [Read More] link."),
					array('value'=>'true', 'name'=>'Show [Read More] link.'),
				),
			),
		);

	public function fields() {
		$prefix = $this->options('name').'_';
		return array(
			array(
				'name' => 'Position Title',
				'descr' => '',
				'id' => $prefix.'position_title',
				'type' => 'text',
			),
			array(
				'name' => 'Email',
				'descr' => '',
				'id' => $prefix.'email',
				'type' => 'text',
			),
			array(
				'name' => 'Phone',
				'descr' => '',
				'id' => $prefix.'phone',
				'type' => 'text',
			),
		);
	}

	public function metabox() {
		if ( $this->options( 'use_metabox' ) ) {
			return array(
				'id'       => 'custom_'.$this->options( 'name' ).'_metabox',
				'title'    => __( $this->options( 'singular_name' ).' Fields' ),
				'page'     => $this->options( 'name' ),
				'context'  => 'after_title',
				'priority' => 'high',
				'fields'   => $this->fields(),
			);
		}
		return null;
	}

	public function register_metaboxes() {
		// Move and Rename the Featured Image Metabox.
		remove_meta_box( 'postimagediv', $this->name, 'side' );
		add_meta_box('postimagediv', __("{$this->singular_name} Image"),
			'post_thumbnail_meta_box', $this->name, 'after_title', 'high');
		CustomPostType::register_meta_boxes_after_title();

		parent::register_metaboxes();
	}

	public function shortcode( $attr ) {
		$prefix = $this->options('name').'_';
		$default_attrs = array(
			'type' => $this->options( 'name' ),
			'header' => $this->options( 'plural_name' ) . ' List',
			'css_classes' => '',
			'collapse' => false,
		);
		if ( is_array( $attr ) ) {
			$attr = array_merge( $default_attrs, $attr );
		}else {
			$attr = $default_attrs;
		}

		$context['header'] = $attr['header'];
		$context['css_classes'] = ( $attr['css_classes'] ) ? $attr['css_classes'] : $this->options('name').'-list';
		$context['collapse'] = filter_var( $attr['collapse'], FILTER_VALIDATE_BOOLEAN);
		unset( $attr['header'] );
		unset( $attr['css_classes'] );
		unset( $attr['collapse'] );
		$args = array( 'classname' => __CLASS__, 'objects_only'=>true, );
		$objects = SDES_Static::sc_object_list( $attr, $args );

		$context['objects'] = $objects;
		return static::render_objects_to_html( $context );
	}

	public function objectsToHTML( $objects, $css_classes ) {
		if ( count( $objects ) < 1 ) { return (WP_DEBUG) ? '<!-- No objects were provided to objectsToHTML. -->' : '';}
		$context['css_classes'] = ( $css_classes ) ? $css_classes : $this->options('name').'-list';
		$context['archiveUrl'] = '';
		$context['objects'] = $objects;
		return static::render_objects_to_html( $context );
	}

	protected static function render_objects_to_html( $context ) {
		ob_start();
		?>
			<?php if( $context['collapse'] ) : ?>
			<script type="text/javascript">
				$(function(){
					var collapsedSize = 60;
					$(".staff-details").each(function() {
						var h = this.scrollHeight;
						var div = $(this);
						if (h > 30) {
							div.css("height", collapsedSize);
							div.after("<a class=\"staff-more\" href=\"\">[Read More]</a>");
							var link = div.next();
							link.click(function(e) {
								e.stopPropagation();
								e.preventDefault();
								if (link.text() != "[Collapse]") {
									link.text("[Collapse]");
									div.animate({ "height": h });
								} else {
									div.animate({ "height": collapsedSize });
									link.text("[Read More]");
								}
							});
						}
					});
				});
			</script>
			<?php endif;
			if ( $context['header'] ) : ?>
				<div class="staff-role"><?= $context['header'] ?></div>
			<?php endif; ?>
			<span class="<?= $context['css_classes'] ?>">
				<?php foreach ( $context['objects'] as $o ):?>
					<?= static::toHTML( $o ) ?>
					<div class="hr-blank"></div>
				<?php endforeach;?>
			</span>
		<?php
		$html = ob_get_clean();
		return $html;
	}

	public static function toHTML ( $post_object ){
		$context['Post_ID'] = $post_object->ID;
		$thumbnailUrl = get_stylesheet_directory_uri() . '/images/blank.png';
		$context['thumbnail']
			= has_post_thumbnail($post_object) 
				? get_the_post_thumbnail($post_object, 'post-thumbnail', array('class' => 'img-responsive'))
				: "<img src='".$thumbnailUrl."' alt='thumb' class='img-responsive'>";
		$context['title'] = get_the_title( $post_object );
		$context['staff_position_title'] = get_post_meta( $post_object->ID, 'staff_position_title', true );
		$context['staff_phone'] = get_post_meta( $post_object->ID, 'staff_phone', true );
		$context['staff_email'] = get_post_meta( $post_object->ID, 'staff_email', true );
		$context['content'] = $post_object->post_content;
		return static::render_to_html( $context );
	}

	protected static function render_to_html( $context ) {
		ob_start();
		?>
			<div class="staff">
				<?= $context['thumbnail'] ?>
				<div class="staff-content">
					<div class="staff-name"><?= $context['title'] ?></div>
					<div class="staff-title"><?= $context['staff_position_title'] ?></div>
					<div class="staff-phone"><?= $context['staff_phone'] ?></div>
					<div class="staff-email">
						<a href="mailto:<?= $context['staff_email'] ?>"><?= $context['staff_email'] ?></a>
					</div>
					<div class="staff-details"><?= $context['content'] ?></div>
				</div>
			</div>
		<?php
		$html = ob_get_clean();
		return $html;
	}
}

/**
 * A single news article.
 */
class News extends CustomPostType {
	public
		$name           = 'news',
		$plural_name    = 'News',
		$singular_name  = 'News',
		$add_new_item   = 'Add New News',
		$edit_item      = 'Edit News',
		$new_item       = 'New News',
		$public         = True,  // I dunno...leave it true
		$use_title      = True,  // Title field
		$use_editor     = True,  // WYSIWYG editor, post content field
		$use_revisions  = True,  // Revisions on post content and titles
		$use_thumbnails = True,  // Featured images
		$use_order      = True, // Wordpress built-in order meta data
		$use_metabox    = True, // Enable if you have custom fields to display in admin
		$use_shortcode  = True, // Auto generate a shortcode for the post type
		                         // (see also objectsToHTML and toHTML methods)
		$taxonomies     = array( 'post_tag', 'categories' ),
		$menu_icon      = 'dashicons-admin-site',
		$built_in       = False,
		// Optional default ordering for generic shortcode if not specified by user.
		$default_orderby = null,
		$default_order   = null,
		// Interface Columns/Fields
		// $calculated_columns = array(),
		$sc_interface_fields = array(
			array(
				'name' => 'Show Archives',
				'id' => 'show-archives',
				'help_text' => 'Choose to whether to show News articles that are archived.',
				'type' => 'dropdown',
				'choices' => array(
					array('value'=>'false', 'name'=>'Show current news.'),
					array('value'=>'true', 'name'=>'Show archived news.'),
					)
			),
		);

	public function fields() {
		$prefix = $this->options('name').'_';
		return array(
			array(
				'name' => 'Strapline',
				'descr' => '',
				'id' => $prefix.'strapline',
				'type' => 'text',
			),
			array(
				'name' => 'URL',
				'descr' => '',
				'id' => $prefix.'url',
				'type' => 'text',
				'default' => 'http://',
			),
			array(
				'name' => 'Start Date',
				'descr' => '',
				'id' => $prefix.'start_date',
				'type' => 'date',
				'custom_column_order' => 200,
			),
			array(
				'name' => 'End Date',
				'descr' => '',
				'id' => $prefix.'end_date',
				'type' => 'date',
				'custom_column_order' => 300,
			),
		);
	}

	public function shortcode( $attr ) {
		$prefix = $this->options('name').'_';
		$default_attrs = array(
			'type' => $this->options( 'name' ),
			'show-archives' => false,
			'orderby' => 'meta_value_datetime',
			'meta_key' => $prefix.'start_date',
			'order' => 'ASC',
			'header' => 'News & Announcements',
			'css_classes' => '',
		);
		if ( is_array( $attr ) ) {
			$attr = array_merge( $default_attrs, $attr );
		}else {
			$attr = $default_attrs;
		}

		// TODO: consider using boolval after PHP 5.5.0.
		$attr['show-archives'] = filter_var( $attr['show-archives'], FILTER_VALIDATE_BOOLEAN);

		if ( $attr['show-archives'] ) {
			// Show where EndDate <= NOW
			$attr['meta_query'] = array(
				'relation' => 'AND',
				array(
					'key' => esc_sql( $prefix.'end_date' ),
					'value' => date( 'Y-m-d', strtotime('-1 day') ),  // Datetime is stored as 24 hours before it should expire.
					'compare' => '<=',
				)
			);
		} else {
			// Show where StartDate is before now (StartDate <= NOW)
			// AND EndDate is after now (EndDate >= NOW)
			$attr['meta_query'] = array(
				'relation' => 'AND',
				array(
					'key' => esc_sql( $prefix.'start_date' ),
					'value' => date( 'Y-m-d', time() ),
					'compare' => '<=',
				),
				array(
					'key' => esc_sql( $prefix.'end_date' ),
					'value' => date( 'Y-m-d', strtotime('-1 day') ), // Datetime is stored as 24 hours before it should expire.
					'compare' => '>=',
				)
			);
		}

		$context['header'] = $attr['header'];
		$context['css_classes'] = ( $attr['css_classes'] ) ? $attr['css_classes'] : $this->options('name').'-list';
		// Unset keys to prevent treating them as taxonomies in sc_object_list.
		unset( $attr['show-archives'] );
		unset( $attr['header'] );
		unset( $attr['css_classes'] );
		$args = array( 'classname' => __CLASS__, 'objects_only'=>true, );
		$context['objects'] = SDES_Static::sc_object_list( $attr, $args );
		$context['archiveLink'] = static::get_archive_link();
		return static::render_objects_to_html( $context );
	}

	/**
	 * Get the a link for the archive URL option stored in ThemeCustomizer or show an adminmsg.
	 * @param $option_id   The name of the option stored in Theme Customizer
	 * @param $posttype_name The name of this posttype, 'news'.
	 */
	private static function get_archive_link( $option_id = 'sdes_rev_2015-newsArchiveUrl', $posttype_name = 'News' ) {
		$archive_url = 
			SDES_Static::get_theme_mod_defaultIfEmpty(
				$option_id,
				get_post_type_archive_link( $posttype_name ) );
		$archive_url = SDES_Static::url_ensure_prefix( $archive_url );
		if( 'http://' !== $archive_url ) {
			$archive_link = '<div class="datestamp"><a href="' . $archive_url . '">»News Archive</a></div>';
		} else {
			$format_default_message =
				( SDES_Static::Is_UserLoggedIn_Can( 'edit_posts' ) )
				? '<span class="adminmsg">Admin Alert:<a class="text-danger" data-control-name="sdes_rev_2015-newsArchiveUrl"'
				  . 'href="' . get_site_url() . '/wp-admin/">%1$s</a><br>'
				  . "If you have not created <a href='".get_admin_url()."/post-new.php?post_type=page&post_title={$posttype_name}&content=%%5Bnews-list%%20show-archives%%3Dtrue%%5D'>a news archive page with a [news-list] shortcode</a>, please do this first.<br></span>"
				: '<!-- %1$s -->';
			$archive_link = sprintf( $format_default_message, 'No news archive page was set.');
		}
		return $archive_link;
	}

	public function objectsToHTML( $objects, $css_classes ) {
		if ( count( $objects ) < 1 ) { return (WP_DEBUG) ? '<!-- No objects were provided to objectsToHTML. -->' : '';}
		$context['objects'] = $objects;
		$context['css_classes'] = ( $css_classes ) ? $css_classes : $this->options('name').'-list';
		$context['archiveLink'] = static::get_archive_link();
		return static::render_objects_to_html( $context );
	}

	protected static function render_objects_to_html ( $context ) {
		ob_start();
		?>
		<?php if ( $context['header'] ) : ?>
			<div class="page-header">
				<h1><?= $context['header'] ?></h1>
			</div>
		<?php endif; ?>
		<span class="<?= $context['css_classes'] ?>">
			<?php foreach ( $context['objects'] as $o ):?>
				<?= static::toHTML( $o ) ?>
				<div class="hr-blank"></div>
			<?php endforeach;?>
			<?php if ( 0 == count( $context['objects'] ) ) : ?>
				<p>No news articles at this time.</p>
			<?php endif; ?>
			<div class="top-b"></div>
			<?php if ( ! SDES_Static::is_null_or_whitespace( $context['archiveLink'] ) ) :?>
				<?= $context['archiveLink'] ?>
			<?php endif; ?>
		</span>
		<?php
		$html = ob_get_clean();
		return $html;
	}

	public static function toHTML ( $post_object ){
		$context['Post_ID'] = $post_object->ID;
		$thumbnailUrl = get_stylesheet_directory_uri() . '/images/blank.png';
		$context['thumbnail']
			= has_post_thumbnail($post_object)
				? get_the_post_thumbnail($post_object, '',
					array('class' => 'img-responsive', 'alt' => SDES_Static::get_post_thumbnail_alttext( $post_object->ID, 'Thumbnail' ) ) )
				: "<img src='".$thumbnailUrl."' alt='Thumbnail' class='img-responsive'>";
		$news_url = get_post_meta( $post_object->ID, 'news_url', true );
		$context['permalink'] = get_permalink( $post_object );
		$context['title_link'] = ( '' !== $news_url ) ? $news_url : $context['permalink'];
		$context['title'] = get_the_title( $post_object );
		$news_strapline = get_post_meta( $post_object->ID, 'news_strapline', true );
		$context['news_strapline'] =('' !== $news_strapline ) ? '<div class="news-strapline">'.$news_strapline.'</div>' : '';
		$context['month_year_day'] = get_the_date('F j, Y', $post_object);
		$context['time'] = get_the_time('g:i a', $post_object);
		$context['datetime'] = get_the_time( DATE_ISO8601, $post_object );

		$loop = new WP_Query( array('p'=>$post_object->ID, 'post_type'=> $post_object->post_type ) );
		$loop->the_post();
			$context['excerpt'] = get_the_content( "[Read More]" );
		wp_reset_query();
		return static::render_to_html( $context );
	}

	protected static function render_to_html( $context ) {
		ob_start();
		?>
		<div class="news">		
			<?= $context['thumbnail'] ?>
			<div class="news-content">
				<div class="news-title">
					<a href="<?= $context['title_link'] ?>"><?= $context['title'] ?></a>
				</div> 
				<?= $context['news_strapline'] ?>
				<div class="datestamp">
					Posted on 
					<a href="<?= $context['permalink'] ?>">
						<time datetime="<?= $context['datetime'] ?>" pubdate>
							<?= $context['month_year_day'] ?> at <?= $context['time'] ?>
						</time>
					</a>
				</div>
				<div class="news-summary">
					<p>
						<?= $context['excerpt'] ?>
					</p>
				</div>
			</div>
		</div>
		<?php
		$html = ob_get_clean();
		return $html;
	}
}

/** Register custom post types when the theme is initialized.
 * @see http://codex.wordpress.org/Plugin_API/Action_Reference/init WP-Codex: init action hook. */
function register_custom_posttypes() {
	CustomPostType::Register_Posttypes(array(
	__NAMESPACE__.'\Post',
	__NAMESPACE__.'\Page',
	__NAMESPACE__.'\Alert',
	__NAMESPACE__.'\Billboard',
	__NAMESPACE__.'\News',
	__NAMESPACE__.'\Staff',
	));
}
add_action('init', __NAMESPACE__.'\register_custom_posttypes');
