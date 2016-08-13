<?php
namespace Insight\Traits;

trait HelperCustomTaxonomy {

	protected $taxonomies = array();

	public function Init_Action_HelperCustomTaxonomy() {
		foreach ($this->taxonomies as $taxonomy) {
			register_taxonomy($taxonomy->name, $taxonomy->post_types, $taxonomy->args);
		}
	}

	public function create_taxonomy($taxonomy, $singular_name, $plural_name, $args = '', $additional_post_types = array()) {
		$labels = array(
			'name' => $plural_name,
			'singular_name' => $singular_name,
			'menu_name' => $singular_name,
			'all_items' => __('All', 'bca') . ' ' . $plural_name,
			'parent_item' => __('Parent', 'bca') . ' ' . $singular_name,
			'parent_item_colon' => __('Parent', 'bca') . ' ' . $singular_name,
			'new_item_name' => __('New', 'bca') . ' ' . $singular_name,
			'add_new_item' => __('Add New', 'bca') . ' ' . $singular_name,
			'edit_item' => __('Edit', 'bca') . ' ' . $singular_name,
			'update_item' => __('Update', 'bca') . ' ' . $singular_name,
			'view_item' => __('View', 'bca') . ' ' . $singular_name,
			'separate_items_with_commas' => __('Separate', 'bca') . strtolower($singular_name) . ' ' . __('with commas', 'bca'),
			'add_or_remove_items' => __('Add or remove', 'bca') . strtolower($singular_name),
			'choose_from_most_used' => __('Choose from the most used', 'bca'),
			'popular_items' => $plural_name,
			'search_items' => __('Search', 'bca') . ' ' . $singular_name,
			'not_found' => __('Not Found', 'bca'),
			'no_terms' => __('No', 'bca') . ' ' . $singular_name,
			'items_list' => $singular_name . ' ' . __('list', 'bca'),
			'items_list_navigation' => $singular_name . ' ' . __('list navigation', 'bca'),
		);
		$args = array(
			'labels' => $labels,
			'hierarchical' => true,
			'public' => true,
			'show_ui' => true,
			'show_admin_column' => true,
			'show_in_nav_menus' => true,
			'show_tagcloud' => true,
		);

		if (!empty($taxonomy)) {
			$this->taxonomy_name = $taxonomy;
		}

		$tx = new \stdClass();
		$tx->name = $taxonomy;
		$tx->args = $args;
		$tx->post_types = $additional_post_types;

		array_push($this->taxonomies, $tx);

	}

}
