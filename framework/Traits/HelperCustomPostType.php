<?php
namespace Insight\Traits;

trait HelperCustomPostType {

	protected $post_types = array();

	public function Init_Action_HelperCustomPostType() {

		foreach ($this->post_types as $post_type) {
			register_post_type($post_type->name, $post_type->args);
		}

		//enqueue scripts
		$this->add_scripts_action();

		if (method_exists($this, '_add_metaboxes')) {
			add_action('add_meta_boxes', array(
				$this,
				'_add_metaboxes',
			));
			add_action('save_post', array(
				$this,
				'_save_metabox',
			));
		}
	}

	public function _save_metabox() {
		global $post;

		if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
			return $post->ID;
		}

		if (count($_POST) > 0) {
			foreach ($_POST as $key => $value) {
				update_post_meta($post->ID, $key, sanitize_text_field($value));
			}
		}
	}

	public function create_post_type($post_type, $singular_name, $plural_name, $desc = '', $args = array()) {
		$labels = array(
			'name' => $plural_name,
			'singular_name' => $singular_name,
			'menu_name' => $plural_name,
			'name_admin_bar' => $singular_name,
			'archives' => $singular_name . ' ' . __('archive', 'bca'),
			'parent_item_colon' => __('Parent Project:', 'bca'),
			'all_items' => __('All', 'bca') . ' ' . $plural_name,
			'add_new_item' => __('Add New', 'bca') . ' ' . $singular_name,
			'add_new' => __('Add new', 'bca') . ' ' . $singular_name,
			'new_item' => __('New', 'bca') . ' ' . $singular_name,
			'edit_item' => __('Edit', 'bca') . ' ' . $singular_name,
			'update_item' => __('Update', 'bca') . ' ' . $singular_name,
			'view_item' => __('View', 'bca') . ' ' . $singular_name,
			'search_items' => __('Search', 'bca') . ' ' . $singular_name,
			'not_found' => __('Not found', 'bca'),
			'not_found_in_trash' => __('Not found in Trash', 'bca'),
			'featured_image' => __('Featured Image', 'bca'),
			'set_featured_image' => __('Set featured image', 'bca'),
			'remove_featured_image' => __('Remove featured image', 'bca'),
			'use_featured_image' => __('Use as featured image', 'bca'),
			'insert_into_item' => __('Insert into', 'bca') . ' ' . strtolower($singular_name),
			'uploaded_to_this_item' => __('Uploaded to this', 'bca') . ' ' . strtolower($singular_name),
			'items_list' => $plural_name . ' ' . __('list', 'bca'),
			'items_list_navigation' => $plural_name . ' ' . __('Projects list navigation', 'bca'),
			'filter_items_list' => __('Filter ', 'bca') . ' ' . strtolower($singular_name) . ' ' . __(' list', 'bca'),
		);

		$args_default = array(
			'label' => $singular_name,
			'description' => $desc,
			'labels' => $labels,
			'supports' => array(
				'title',
				'editor',
				'thumbnail',
			),
			'hierarchical' => false,
			'public' => true,
			'show_ui' => true,
			'show_in_menu' => true,
			'menu_position' => 5,
			'menu_icon' => 'dashicons-hammer',
			'show_in_admin_bar' => true,
			'show_admin_column' => true,
			'show_in_nav_menus' => true,
			'can_export' => true,
			'has_archive' => true,
			'exclude_from_search' => false,
			'publicly_queryable' => true,
			'capability_type' => 'page',
		);

		$args_default = array_merge($args_default, $args);

		$postype = new \stdClass();
		$postype->name = $post_type;
		$postype->args = $args_default;

		array_push($this->post_types, $postype);

	}

}
