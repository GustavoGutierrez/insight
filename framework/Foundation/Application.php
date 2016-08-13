<?php

namespace Insight\Foundation;

use Dotenv\Dotenv;
use \Insight\Contracts\Plugin\Plugin as IPlugin;

class Application {

	/**
	 * Base path of el project
	 * @var string
	 */
	protected $basePath = '';

	private $Separator = '/';

	public function __construct($base_path) {

		$this->Separator = DIRECTORY_SEPARATOR;

		$this->setBasePath($base_path);

		$this->loadEnv();
	}

	public function get_app_dir() {
		$ds = $this->getSeparator();
		return $this->getBasePath() . $ds . 'app' . $ds;
	}

	public function get_dir_plugins() {
		return $this->get_app_dir() . 'Plugins' . $this->getSeparator();
	}

	public function get_config_dir() {
		return $this->get_app_dir() . 'Config' . $this->getSeparator();
	}

	/**
	 * Instance all plugins
	 * Genera una variable global para cada clase o plugin
	 * cargado accesibe como global
	 * ejs.:
	 * global $BCA_P2Projects;
	 * $BCA_P2Projects->widget();
	 * @return void
	 */
	public function boot() {

		$plugins_files = scandir($this->get_dir_plugins());
		foreach ($plugins_files as $file) {
			if ($file != '.' && $file != '..' && $file != '.gitkeep') {
				$class_name = explode('.', $file)[0];

				if ($class_name != 'Plugin') {
					$class = '\\App\\Plugins\\' . $class_name;
					$this->create_global_plugin(new $class());

				}

			}
		}
	}

	/**
	 * Registra una clase Plugin de forma global
	 * @param  IPlugin $plugin Instance de clase Plugin
	 * @return void
	 */
	public function create_global_plugin(IPlugin $plugin) {
		$GLOBALS['BCA_' . get_class($plugin)] = $plugin;
		$GLOBALS['BCA_' . get_class($plugin)]->boot();
	}

	/**
	 * Boot the application's service providers.
	 *
	 * @return void
	 */
	private function loadEnv() {
		/**
		 * You can then load .env in your application with:
		 * @var Dotenv
		 */
		$dotenv = new Dotenv($this->getBasePath());
		$dotenv->load();
	}

	/**
	 * Gets the Base path of el project.
	 *
	 * @return string
	 */
	public function getBasePath() {
		return $this->basePath;
	}

	/**
	 * Sets the Base path of el project.
	 *
	 * @param string $basePath the base path
	 *
	 * @return self
	 */
	protected function setBasePath($basePath) {
		$this->basePath = $basePath;

		return $this;
	}

	/**
	 * Gets the value of Separator.
	 *
	 * @return mixed
	 */
	public function getSeparator() {
		return $this->Separator;
	}
}
