<?php
namespace Insight\Abstracts;

abstract class Plugin {

	protected $class = null;

	abstract public function boot();

	protected function camelCase($input, $separator = '_') {
		return str_replace($separator, '', ucwords($input, $separator));
	}

	protected function uncamelCase($str) {
		$str = preg_replace('/([a-z])([A-Z])/', "\\1_\\2", $str);
		$str = strtolower($str);
		return $str;
	}

	/**
	 * Sets the value of class.
	 *
	 * @param mixed $class the class
	 *
	 * @return self
	 */
	public function setClass($class) {
		$this->class = $class;

		return $this;
	}
}
