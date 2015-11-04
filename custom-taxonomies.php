<?php

/**
 * Abstract class for defining custom taxonomies.  
 * 
 **/
abstract class CustomTaxonomy {
	public
		$name			= 'custom_taxonomy',
		
		// Do not register the taxonomy with the post type here.
		// Register it on the `taxonomies` attribute of the post type in
		// custom-post-types.php
		$object_type	= Array(), 
		//Labels
		$singular_name      = 'Post Tag',
		$menu_name          = NULL,
		$all_items          = 'All Tags',
		$edit_item          = 'Edit Tag',
		$view_item          = 'View Tag',  // Not in WordPress-Generic-Theme
		$update_item        = 'Update Tag',
		$add_new_item       = 'Add New Tag',
		$new_item_name      = 'New Tag Name',
		$parent_item        = 'Parent Category',
		$parent_item_colon  = 'Parent Category:',
		$search_items       = 'Search Tags',
		$popular_items      = 'Popular Tags',
		$separate_items_with_commas = ' Separate tags with commas',  // Not in WordPress-Generic-Theme
		$add_or_remove_items    = 'Add or remove tags',  // Not in WordPress-Generic-Theme
		$choose_from_most_used  = 'Choose from the most used tags',  // Not in WordPress-Generic-Theme
		$not_found              = 'No tags found.',  // Not in WordPress-Generic-Theme
		
		$general_name       = 'Post Tags',
		//Args
		$public                = True,
		$show_in_nav_menus     = True,
		$show_in_name_menus    = NULL,
		$show_ui               = NULL,
		$show_tagcloud         = NULL,
		$hierarchical          = False,
		$update_count_callback = '',
		$rewrite               = True,
		$query_var             = NULL,
		$capabilities          = Array();
		//TODO: Add or array_merge the missing default values for $args, see: http://codex.wordpress.org/Function_Reference/register_taxonomy
	
	function __construct() {
		if(is_null($this->show_in_name_menus)) $this->show_in_name_menus = $this->public;
		if(is_null($this->show_ui)) $this->show_ui = $this->public;
		if(is_null($this->show_tagcloud)) $this->show_tagcloud = $this->show_ui;
		if(is_null($this->menu_name)) $this->menu_name = $this->general_name;
	}
	
	public function options($key){
		$vars = get_object_vars($this);
		return $vars[$key];
	}
	
	public function labels() {
		return Array(
				'name'                       => _x($this->options('general_name'), 'taxonomy general name'),
				'singular_name'              => _x($this->options('singular_name'), 'taxonomy singular name'),
				'search_items'               => __($this->options('search_items')),
				'popular_items'              => __($this->options('popular_items')),
				'all_items'                  => __($this->options('all_items')),
				'parent_item'                => __($this->options('popular_items')),
				'parent_item_colon'          => __($this->options('parent_item_colon')),
				'edit_item'                  => __($this->options('edit_item')),
				'update_item'                => __($this->options('update_item')),
				'add_new_item'               => __($this->options('add_new_item')),
				'new_item_name'              => __($this->options('new_item_name')),
				'separate_items_with_commas' => __($this->options('separate_items_with_commas')),
				'add_or_remove_items'        => __($this->options('add_or_remove_items')),
				'choose_from_most_used'      => __($this->options('choose_from_most_used')),
				'menu_name'                  => __($this->options('menu_name'))
				);
	}
	
	public function register() {
		$args = Array(
				'labels'                => $this->labels(),
				'public'                => $this->options('public'),
				'show_in_nav_menus'     => $this->options('show_in_nav_menus'),
				'show_ui'               => $this->options('show_ui'),
				'show_tagcloud'         => $this->options('show_tagcloud'),
				'hierarchical'          => $this->options('hierarchical'),
				'update_count_callback' => $this->options('update_count_callback'),
				'rewrite'               => $this->options('rewrite'),
				'query_var'             => $this->options('query_var'),
				'capabilities'          => $this->options('capabilities')
			);
		register_taxonomy($this->options('name'), $this->options('object_type'), $args);
	}
}


/**
 * Describes organizational groups
 *
 * @author Chris Conover
 **/
class OrganizationalGroups extends CustomTaxonomy
{
	public
		$name               = 'org_groups',
		$general_name       = 'Organizational Groups',
		$singular_name      = 'Organizational Group',
		$search_items       = 'Search Organizational Groups',
		$popular_items      = 'Popular Organizational Groups',
		$all_times          = 'All Organizational Groups',
		$parent_item        = 'Parent Organizational Group',
		$parent_item_colon  = 'Parent Organizational Group:',
		$edit_item          = 'Edit Organizational Group',
		$update_item        = 'Update Organizational Group',
		$add_new_item       = 'Add New Organizational Group',
		$new_item_name      = 'New Tag Organizational Group',
		
		$hierarchical = True;
} // END class 


function register_custom_taxonomies() {
	$taxonomy_names = array('OrganizationalGroups', );
	$instantiate_class = function($class){ return new $class;};
	$custom_taxonomies = array_map($instantiate_class, $taxonomy_names);
	foreach ($custom_taxonomies as $custom_taxonomy) {
		$custom_taxonomy->register();
	}
}
add_action('init', 'register_custom_taxonomies');
