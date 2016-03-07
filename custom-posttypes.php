<?php

require_once( get_stylesheet_directory().'/functions/class-sdes-metaboxes.php' );
require_once( get_stylesheet_directory().'/functions/class-custom-posttype.php' );

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
		$use_metabox    = False, // Enable if you have custom fields to display in admin
		$use_shortcode  = False, // Auto generate a shortcode for the post type
		                         // (see also objectsToHTML and toHTML methods)
		$taxonomies     = array( 'post_tag' ),
		$built_in       = False,
		// Optional default ordering for generic shortcode if not specified by user.
		$default_orderby = null,
		$default_order   = null;

	public function fields() {
		$prefix = $this->options('name').'_';
		return array(
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
}

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
				? get_the_post_thumbnail($post_object, '', array('class' => 'img-responsive'))
				: "<img src='".$thumbnailUrl."' alt='thumb' class='img-responsive'>";
		$context['title'] = get_the_title( $post_object );
		$context['staff_position_title'] = get_post_meta( $post_object->ID, 'staff_position_title', true );
		$context['staff_phone'] = get_post_meta( $post_object->ID, 'staff_phone', true );
		$context['staff_email'] = get_post_meta( $post_object->ID, 'staff_email', true );

		$loop = new WP_Query( array('p'=>$post_object->ID, 'post_type'=> $post_object->post_type ) );
		$loop->the_post();
			$context['content'] = get_the_content();
		wp_reset_query();

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
		$default_order   = null;

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
		$default_attrs = array(
			'type' => $this->options( 'name' ),
			'startdate' => null,
			'enddate'   => date('Y-m-d'),
		);
		if ( is_array( $attr ) ) {
			$attr = array_merge( $default_attrs, $attr );
		}else {
			$attr = $default_attrs;
		}

		$prefix = $this->options('name').'_';
		$attr['meta_query'] = array(
				'relation' => 'AND',
				array(
						'key' => esc_sql( $prefix.'start_date' ),
						'value' => esc_sql( $attr['startdate'] ),
						'compare' => '>=',
					),
				array(
						'key' => esc_sql( $prefix.'end_date' ),
						'value' => esc_sql( $attr['enddate'] ),
						'compare' => '>=',
					),
			);
		// Unset keys to prevent treating them as taxonomies in sc_object_list.
		unset( $attr['startdate'] );
		unset( $attr['enddate'] );
		
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
		'Billboard',
		'News',
		'Staff',
	));
}
add_action('init', 'register_custom_posttypes');
