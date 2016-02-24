<?php

require_once( 'functions/class-custom-posttype.php' );
require_once( 'functions/class-sdes-metaboxes.php' );
require_once( 'functions/class-sdes-static.php' );

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
		$use_thumbnails = False, // Featured images
		$use_order      = True,  // Wordpress built-in order meta data
		$use_metabox    = True,  // Enable if you have custom fields to display in admin
		$use_shortcode  = False, // Auto generate a shortcode for the post type
								 // (see also objectsToHTML and toHTML methods)
		$taxonomies     = ['post_tag', 'category'],
		$built_in       = True,
		// Optional default ordering for generic shortcode if not specified by user.
		$default_orderby = null,
		$default_order   = null;

	public function fields() {
		$prefix = 'custom_'.$this->options('name').'_';
		return array(
			[
				'name' => 'Stylesheet',
				'desc' => '',
				'id' => $prefix.'stylesheet',
				'type' => 'file',
			],
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
			[
				'name' => 'Stylesheet',
				'desc' => '',
				'id' => $prefix.'stylesheet',
				'type' => 'file',
			],
		);
	}
}

function register_custom_posttypes() {
	$custom_posttypes = [
		'Post',
		'Page',
		];
	$class_instances = SDES_Static::instantiate_and_register_classes($custom_posttypes);
	foreach ($class_instances as $registered_class) {
		SDES_Metaboxes::$installed_custom_post_types[] = $registered_class['instance'];
	}
}
add_action('init', 'register_custom_posttypes');
