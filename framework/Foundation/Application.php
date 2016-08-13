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

	public function __construct($base_path) {
		$this->setBasePath($base_path);

		$this->loadEnv();
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
		$plugins_files = scandir(DIRECTORY_APP_PLUGINS);
		foreach ($plugins_files as $file) {
			if ($file != '.' && $file != '..' && $file != '.gitkeep') {
				$class_name = explode('.', $file)[0];

				if ($class_name != 'BasePlugin') {
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
	private function loadEnv($base_path) {
		/**
		 * You can then load .env in your application with:
		 * @var Dotenv
		 */
		$dotenv = new Dotenv($this->basePath);
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
}
