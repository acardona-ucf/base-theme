<?php

require_once( get_stylesheet_directory().'/functions/class-sdes-metaboxes.php' );
require_once( get_stylesheet_directory().'/functions/class-custom-posttype.php' );

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

	public function fields() {
		$prefix = 'custom_'.$this->options('name').'_';
		return array(
			array(
				'name' => 'Stylesheet',
				'descr' => '',
				'id' => $prefix.'stylesheet',
				'type' => 'file',
			),
		);
	}
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
				'name'  => 'Stylesheet',
				'descr' => '',
				'id'    => $prefix.'stylesheet',
				'type'  => 'file',
			),
			array(
				'name'  => 'Sidecolumn',
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
		$built_in       = False,
		// Optional default ordering for generic shortcode if not specified by user.
		$default_orderby = null,
		$default_order   = null,
		$sc_interface_fields = false;

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
			),
			array(
				'name' => 'Sitewide Alert',
				'descr' => 'Show alert across the entire site.',
				'id' => $prefix.'is_sitewide',
				'type' => 'checkbox_list',
				'choices' => array(
					'Sitewide alert.' => $prefix.'is_sitewide'
				),
			),
			array(
				'name' => 'Start Date',
				'descr' => 'The first day the alert should appear.',
				'id' => $prefix.'start_date',
				'type' => 'date',
			),
			array(
				'name' => 'End Date',
				'descr' => 'The last day the alert should appear.',
				'id' => $prefix.'end_date',
				'type' => 'date',
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
					'value' => date('Y-m-d', time()),
					'compare' => '<=',
				),
				array(
					'key' => $prefix.'end_date',
					'value' => date('Y-m-d', strtotime('+1 day')),
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
		return SDES_Static::sc_object_list( $attr );
	}

	private static function get_render_metadata( $alert ) {
		$metadata_fields = array();
		$metadata_fields['alert_is_unplanned'] = get_post_meta($alert->ID, 'alert_is_unplanned', true);
		$metadata_fields['alert_url'] = esc_attr( get_post_meta($alert, 'alert_url', true) );
		return $metadata_fields;
	}

	/**
	 * Generate a render context given a WP_Post object and an array of its metadata fields.
	 * Expected fields:
	 * $alert - post_content, post_title
	 * $metadata_fields - alert_is_unplanned, alert_url
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

	protected static function render_objects_to_html( $context ){
		ob_start();
		?>
			<span class="<?= $context['css_classes'] ?>">
				<?php foreach ( $context['alert_contexts'] as $alert ):
					echo static::render_to_html( $alert );
				endforeach; ?>
			</span>
		<?php
		return ob_get_clean();
	}

	public static function toHTML ( $post_object ) {
		$metadata_fields = static::get_render_metadata( $post_object );
		$alert_context = static::get_render_context( $post_object, $metadata_fields );
		return static::render_to_html( $alert_context );
	}

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
		return ob_get_clean();
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
			),
			array(
				'name' => 'End Date',
				'descr' => 'Stop showing the billboard after this date.',
				'id' => $prefix.'end_date',
				'type' => 'date',
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
					'value' => date('Y-m-d H:i:s'),
					'compare' => '<=',
				),
				array(
					'key' => $prefix.'end_date',
					'value' => date('Y-m-d H:i:s'),
					'compare' => '>=',
				),
			),
		);
		if ( is_array( $attr ) ) {
			$attr = array_merge( $default_attrs, $attr );
		}else {
			$attr = $default_attrs;
		}
		return SDES_Static::sc_object_list( $attr );
	}

	public function objectsToHTML( $objects, $css_classes ) {
		if ( count( $objects ) < 1 ) { return (WP_DEBUG) ? '<!-- No objects were provided to objectsToHTML. -->' : '';}
		$context['objects'] = $objects;
		$context['css_classes'] = ( $css_classes ) ? $css_classes : $this->options('name').'-list';
		return static::render_objects_to_html( $context );
	}

	protected static function render_objects_to_html( $context ){
		// TODO: don't show nivoslider directionNav if only 1 Billboard slide.
		$billboard_size = array(1140,318);
		ob_start();
		?>
		<!-- nivo slider -->
		<script type="text/javascript">
			$(window).load(function() {
				$('#slider-sc').nivoSlider({
					slices: 10,
					pauseTime: 5000,
					controlNav: false,
					captionOpacity: 0.7
				});
			});
		</script>
		<div class="container site-billboard theme-default">
			<div id="slider-sc" class="nivoSlider">
			<?php foreach ( $context['objects'] as $o ):
				if ( has_post_thumbnail( $o ) ) :
					$alt_text = get_post_meta(get_post_thumbnail_id( $o->ID ), '_wp_attachment_image_alt', true);
					$billboard_url = get_post_meta($o, 'billboard_url', true);
					if( $billboard_url ) :
						$billboard_url = SDES_Static::url_ensure_prefix( $billboard_url );
					?>
						<a href="<?= $billboard_url ?>" class="nivo-imageLink">
					<?php endif;
							echo get_the_post_thumbnail( $o, $billboard_size, 
									array('title'=>'#nivo-caption-'.$o->ID, 'alt' => $alt_text ) );
					if( $billboard_url ) : ?>
						</a>
					<?php endif;
				endif;
			endforeach; ?>
			</div>
			<?php foreach ( $context['objects'] as $o ):
				if ( has_post_thumbnail( $o ) ) : ?>
					<div id="nivo-caption-<?= $o->ID ?>" class="nivo-html-caption">
						<div class="nivo-padding"></div>
						<div class="nivo-title"><?= $o->post_title ?></div>
						<div class="nivo-strapline"><?= $o->post_content ?></div>
					</div>
		  		<?php endif;
			endforeach; ?>
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
		$built_in       = False,
		// Optional default ordering for generic shortcode if not specified by user.
		$default_orderby = null,
		$default_order   = null;

	public function register( $args = array() ) {
		$default_args = array(
				'menu_icon' => 'dashicons-groups',
			);
		parent::register( array_merge($default_args, $args) );
	}

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

	public function objectsToHTML( $objects, $css_classes ) {
		if ( count( $objects ) < 1 ) { return (WP_DEBUG) ? '<!-- No objects were provided to objectsToHTML. -->' : '';}
		$css_classes = ( $css_classes ) ? $css_classes : $this->options('name').'-list';
		$context['archiveUrl'] = '';
		ob_start();
		?>
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
		<span class="<?= $css_classes ?>">
			<?php foreach ( $objects as $o ):?>
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
		$thumbnailUrl = 'https://assets.sdes.ucf.edu/images/blank.png';
		$context['thumbnail']
			= has_post_thumbnail($post_object) 
				? get_the_post_thumbnail($post_object, 'post-thumbnail', array('class' => 'img-responsive'))
				: "<img src='".$thumbnailUrl."' alt='thumb' class='img-responsive'>";
		$context['title'] = get_the_title( $post_object );
		$context['staff_position_title'] = get_post_meta( $post_object->ID, 'staff_position_title', true );
		$context['staff_phone'] = get_post_meta( $post_object->ID, 'staff_phone', true );
		$context['staff_email'] = get_post_meta( $post_object->ID, 'staff_email', true );
		$context['content'] = $post_object->post_content;

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
		return ob_get_clean();
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
		$built_in       = False,
		// Optional default ordering for generic shortcode if not specified by user.
		$default_orderby = null,
		$default_order   = null,
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

	public function register( $args = array() ) {
		$default_args = array(
				'menu_icon' => 'dashicons-admin-site',
			);
		parent::register( array_merge($default_args, $args) );
	}

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
				'name' => 'Link',
				'descr' => '',
				'id' => $prefix.'link',
				'type' => 'text',
				'default' => 'http://',
			),
			array(
				'name' => 'Start Date',
				'descr' => '',
				'id' => $prefix.'start_date',
				'type' => 'date',
			),
			array(
				'name' => 'End Date',
				'descr' => '',
				'id' => $prefix.'end_date',
				'type' => 'date',
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
		);
		if ( is_array( $attr ) ) {
			$attr = array_merge( $default_attrs, $attr );
		}else {
			$attr = $default_attrs;
		}

		// TODO: consider using boolval after PHP 5.5.0.
		$attr['show-archives'] = filter_var( $attr['show-archives'], FILTER_VALIDATE_BOOLEAN);

		$current_datetime = date('Y-m-d H:i:s'); // Calculate NOW as MySQL datetime format.
		if ( $attr['show-archives'] ) {
			// Show where EndDate <= NOW
			$attr['meta_query'] = array(
				'relation' => 'AND',
				array(
					'key' => esc_sql( $prefix.'end_date' ),
					'value' => $current_datetime,
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
					'value' => $current_datetime,
					'compare' => '<=',
				),
				array(
					'key' => esc_sql( $prefix.'end_date' ),
					'value' => $current_datetime,
					'compare' => '>=',
				)
			);
		}

		// Unset keys to prevent treating them as taxonomies in sc_object_list.
		unset( $attr['show-archives'] );

		return SDES_Static::sc_object_list( $attr );
	}

	public function objectsToHTML( $objects, $css_classes ) {
		if ( count( $objects ) < 1 ) { return (WP_DEBUG) ? '<!-- No objects were provided to objectsToHTML. -->' : '';}
		$css_classes = ( $css_classes ) ? $css_classes : $this->options('name').'-list';
		$context['archiveUrl'] = '';
		ob_start();
		?>
		<span class="<?= $css_classes ?>">
			<?php foreach ( $objects as $o ):?>
				<?= static::toHTML( $o ) ?>
				<div class="hr-blank"></div>
			<?php endforeach;?>
			<div class="top-b"></div>
			<div class="datestamp"><a href="<?= $context['archiveUrl'] ?>">»News Archive</a></div>
		</span>
		<?php
			$html = ob_get_clean();
		return $html;
	}

	public static function toHTML ( $post_object ){
		$context['Post_ID'] = $post_object->ID;
		$thumbnailUrl = 'https://assets.sdes.ucf.edu/images/blank.png';
		$context['thumbnail']
			= has_post_thumbnail($post_object) 
				? get_the_post_thumbnail($post_object, '', array('class' => 'img-responsive'))
				: "<img src='".$thumbnailUrl."' alt='thumb' class='img-responsive'>";
		$news_link = get_post_meta( $post_object->ID, 'news_link', true );
		$context['permalink'] = get_permalink( $post_object );
		$context['title_link'] = ( '' !== $news_link ) ? $news_link : $context['permalink'];
		$context['title'] = get_the_title( $post_object );
		$news_strapline = get_post_meta( $post_object->ID, 'news_strapline', true );
		$context['news_strapline'] =('' !== $news_strapline ) ? '<div class="news-strapline">'.$news_strapline.'</div>' : '';
		$context['month_year_day'] = get_the_date('F j, Y', $post_object);
		$context['time'] = get_the_time('g:i a', $post_object);

		$loop = new WP_Query( array('p'=>$post_object->ID, 'post_type'=> $post_object->post_type ) );
		$loop->the_post();
			$context['excerpt'] = get_the_content( "[Read More]" );
		wp_reset_query();

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
						<?= $context['month_year_day'] ?> at <?= $context['time'] ?>
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
		return ob_get_clean();
	}
}

function register_custom_posttypes() {
	CustomPostType::Register_Posttypes(array(
		'Post',
		'Page',
		'Alert',
		'Billboard',
		'News',
		'Staff',
	));
}
add_action('init', 'register_custom_posttypes');
