<?php
namespace Insight\Config;
use Underscore\Types\Arrays;

class Config {

	private $params = array();

	/**
	 * Data
	 *
	 * @var array
	 * @access private
	 */
	private $data = [];

	private $key = '';

	public function __construct() {

		$this->path = DIRECTORY_APP_CONFIG;

		//$this->path = '/var/www/html/pro-profile/wp-content/plugins/wp-builder-app/app/Config/';
	}

	public static function get($config, $default = '') {
		$self = new self();

		if ($self->has($config)) {

			if (count($self->params) > 1) {
				$configuration = $self->requireFile($self->params[0]);
			} else {
				$configuration = $self->requireFile($self->params);
			}

			if ($configuration) {

				$self->data = $configuration;

				$params = $self->params;

				unset($params[0]);

				$self->key = implode('.', $params);

				return Arrays::from($self->data)->get($self->key);

			}

			return $default;

		} else {
			throw new Exception("File config notfound in " . DIRECTORY_APP_CONFIG, 1);
		}

	}

	private function requireFile($fileName) {
		if (file_exists($this->path . $fileName . '.php')) {
			return require $this->path . $fileName . '.php';
		} else {
			return false;
		}
	}

	public function has($config) {

		$this->filter($config);

		$fileName = $this->params[0];
		if (file_exists($this->path . $fileName . '.php')) {
			return true;
		}
		return false;

	}

	private function filter($param) {
		$this->params = explode('.', $param);
	}

}
