<?php
namespace Insight\Base;

class Script {

	public $id;
	public $src = '';
	public $deps = array();
	public $version = '0.0.1';
	public $in_footer = false;

	public $post_type = '';

	private $in_admin = false;
	private $priority = 1000;
	private $accepted_args = 1;

	public function __construct($src, $options = array(), $admin = false) {
		$this->src = $src;
		$this->in_admin = $admin;

		$this->options($options);
	}

	public function admin() {
		$this->in_admin = true;
		return $this;
	}

	public function front() {
		$this->in_admin = false;
		return $this;
	}

	public function options($ops = array()) {
		$defaults = array('id' => md5(uniqid(rand(), true)),
			'deps' => array(),
			'version' => '0.0.1',
			'in_footer' => false,
			'priority' => 1000,
			'accepted_args' => 1);

		$options = array_merge($defaults, $ops);

		$this->id = $options['id'];
		$this->deps = $options['deps'];
		$this->version = $options['version'];
		$this->in_footer = $options['in_footer'];
		$this->post_type = $options['post_type'];

		$this->priority = $options['priority'];
		$this->accepted_args = $options['accepted_args'];

		return $this;
	}

	public function enqueue() {
		wp_enqueue_script(
			$this->id,
			plugins_url('../../app/assets/js/' . $this->src, __FILE__),
			$this->deps, $this->version,
			$this->in_footer
		);
	}

	/**
	 * Sets the value of priority.
	 *
	 * @param mixed $priority the priority
	 *
	 * @return self
	 */
	private function _setPriority($priority) {
		$this->priority = $priority;

		return $this;
	}

	/**
	 * Sets the value of id.
	 *
	 * @param mixed $id the id
	 *
	 * @return self
	 */
	public function setId($id) {
		$this->id = $id;

		return $this;
	}

	/**
	 * Sets the value of src.
	 *
	 * @param mixed $src the src
	 *
	 * @return self
	 */
	public function setSrc($src) {
		$this->src = $src;

		return $this;
	}

	/**
	 * Sets the value of deps.
	 *
	 * @param mixed $deps the deps
	 *
	 * @return self
	 */
	public function setDeps($deps) {
		$this->deps = $deps;

		return $this;
	}

	/**
	 * Sets the value of version.
	 *
	 * @param mixed $version the version
	 *
	 * @return self
	 */
	public function setVersion($version) {
		$this->version = $version;

		return $this;
	}

	/**
	 * Sets the value of in_footer.
	 *
	 * @param mixed $in_footer the in footer
	 *
	 * @return self
	 */
	public function setInFooter($in_footer) {
		$this->in_footer = $in_footer;

		return $this;
	}

	/**
	 * Sets the value of in_admin.
	 *
	 * @param mixed $in_admin the in admin
	 *
	 * @return self
	 */
	private function _setInAdmin($in_admin) {
		$this->in_admin = $in_admin;

		return $this;
	}

	/**
	 * Sets the value of accepted_args.
	 *
	 * @param mixed $accepted_args the accepted args
	 *
	 * @return self
	 */
	private function _setAcceptedArgs($accepted_args) {
		$this->accepted_args = $accepted_args;

		return $this;
	}

	/**
	 * Gets the value of in_admin.
	 *
	 * @return mixed
	 */
	public function getInAdmin() {
		return $this->in_admin;
	}

	/**
	 * Gets the value of priority.
	 *
	 * @return mixed
	 */
	public function getPriority() {
		return $this->priority;
	}

	/**
	 * Gets the value of accepted_args.
	 *
	 * @return mixed
	 */
	public function getAcceptedArgs() {
		return $this->accepted_args;
	}

	/**
	 * Gets the value of post_type.
	 *
	 * @return mixed
	 */
	public function getPostType() {
		return $this->post_type;
	}

	/**
	 * Sets the value of post_type.
	 *
	 * @param mixed $post_type the post type
	 *
	 * @return self
	 */
	public function setPostType($post_type) {
		$this->post_type = $post_type;

		return $this;
	}
}
