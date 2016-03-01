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
}

function register_custom_posttypes() {
	CustomPostType::Register_Posttypes(array(
		'Post',
		'Page',
		'Billboard',
		'Staff',
	));
}
add_action('init', 'register_custom_posttypes');
