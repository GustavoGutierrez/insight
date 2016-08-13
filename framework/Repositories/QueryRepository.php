<?php
namespace Insight\Repositories;

use \Insight\Contracts\Repository\Repository;

/**
 * Repository by query common plugins
 */
class QueryRepository implements Repository {
	/**
	 * Gel current post by current post type
	 * @return Post
	 */
	public function get() {
		global $post;
		return $post;
	}

	/**
	 * Get post by id
	 * @param  integer $id ID of post
	 * @return Post
	 */
	public function getById($id) {
		return "Post by id:" . $id;
	}

	/**
	 * Get all post by current post type
	 * @return Array[Post]
	 */
	public function getAll() {
		return array();
	}
}
