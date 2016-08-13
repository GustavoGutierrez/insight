<?php
namespace Insight\Base;
use Insight\Contracts\Plugin\Plugin as IPlugin;

class PostType {

	private $post_type = '';

	private $description = '';

	private $singular = 'Item';

	private $plural = 'Items';

	private $arguments = array();

	public function __construct() {

	}

	public function create($post_type, $description) {
		$this->$post_type = $post_type;
		$this->$description = $description;

		return $this;
	}

	public function singular($singular) {
		$this->singular = $singular;
		return $this;
	}

	public function plural($plural) {
		$this->plural = $plural;
		return $this;
	}

	public function args($args) {
		$this->arguments = $args;
		return $this;
	}

	public function apply(IPlugin $plugin) {
		$class_name = end(explode('\\', get_class($plugin)));
		$GLOBALS['BCA_' . $class_name]->create_post_type($this->$post_type,
			$this->singular,
			$this->plural,
			$this->$description,
			$this->arguments);
		return $this;
	}

}
