<?php

namespace Insight\Foundation;

use Dotenv\Dotenv;
use \Insight\Config\Config;
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
		return $this->getBasePath() . 'app' . $ds;
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

		do_action('app_before_boot_complete');

		$plugins_files = scandir($this->get_dir_plugins());
		foreach ($plugins_files as $file) {
			if ($file != '.' && $file != '..' && $file != '.gitkeep') {
				$class_name = explode('.', $file)[0];

				if ($class_name != 'Plugin') {
					$class = '\\App\\Plugins\\' . $class_name;
					$PluginInstance = new $class();
					$PluginInstance->setClass($class);
					$ClassRef = &$PluginInstance;
					$this->create_global_plugin($ClassRef);

				}

			}
		}
		$this->appBootComplete();
	}

	private function appBootComplete() {

		do_action('app_after_boot_complete');

		add_filter('init', array($this, 'load_app_textdomain'));
	}

	public function load_app_textdomain() {
		$ds = $this->getSeparator();
		$appPath = $this->get_app_dir();
		$pathFileLang = $appPath . 'Lang' . $ds . get_locale() . '.mo';
		if (file_exists($pathFileLang)) {
			$textdomain = Config::get('app.textdomain', 'wba');
			load_textdomain($textdomain, $pathFileLang);
		}
	}

	/**
	 * Registra una clase Plugin de forma global
	 * @param  IPlugin $plugin Instance de clase Plugin
	 * @return void
	 */
	public function create_global_plugin(&$plugin) {
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
