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
	}

	public static function get($config, $default = '') {
		$self = new self();

		if ($self->has($config)) {

			if (count($self->params) > 1) {
				$configuration = $self->requireFile($self->params[0]);
			} else {
				$configuration = $self->requireFile($self->params);
			}

			$self->data = $configuration;

			$params = $self->params;

			unset($params[0]);

			$self->key = implode('.', $params);

			if (Arrays::has($self->data, $self->key)) {
				return Arrays::from($self->data)->get($self->key);
			}

			return $default;

		} else {
			throw new Exception("File config notfound", 1);
			exit();
		}

	}

	private function requireFile($fileName) {
		return require $this->path . $fileName . '.php';
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
