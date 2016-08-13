<?php
namespace Insight\View;

use Insight\Contracts\View\IView;

class View implements IView {

	public $args = array();

	public $view = null;
	public $path = null;

	protected $enabled_cache = false;

	protected $enabled_partials = true;

	protected $extension = '.html';

	protected $charset;

	public function __construct($view = null, $args = array()) {

		if (!is_null($view)) {
			$this->view = $this->viewFilter($view);
		}

		$this->args = $args;

		$this->path = config('view.path');

		$this->enabled_cache = config('view.cache');

		$this->enabled_partials = config('view.partials');

		$this->charset = config('view.charset');

		$this->extension = config('view.extension');

		//dump($this);
		//exit();
	}

	private function viewFilter($str_view) {
		return str_replace('.', '/', $str_view);
	}

	public function exists($view) {

		$view = $this->viewFilter($view);

		$file = $this->path . $view . $this->extension;

		if (!file_exists($file)) {
			return false;
		}

		return true;
	}

	public function with($key, $value) {
		$this->args[$key] = $value;
		return $this;
	}

	/**
	 * @param  string $view file name template
	 * @param  array $args argumentos pasados a la vista
	 * @return string View rendered
	 */
	public function render($view, $args = array()) {

		if (!$this->exists($view)) {
			throw new Exception("Error template view no found: " . $this->path . $view . $this->extension, 1);
		}

		$options_template = array(
			'extension' => $this->extension,
		);

		$options = array(
			'loader' => new \Mustache_Loader_FilesystemLoader($this->path, $options_template),
			'charset' => $this->charset,
			'strict_callables' => true,
			'pragmas' => [\Mustache_Engine::PRAGMA_FILTERS],
		);

		if ($this->enabled_partials) {
			$options['partials_loader'] = new \Mustache_Loader_FilesystemLoader($this->path . 'partials', $options_template);
		}

		if ($this->enabled_cache) {
			$options['cache'] = config('view.cache_path', DIRECTORY_APP_CACHE_VIEWS);

			if (!file_exists($options['cache'])) {
				mkdir($options['cache'], 0776);
			}
			$options['cache_file_mode'] = 0666;
			$options['cache_lambda_templates'] = true;
		}

		$mustache = new \Mustache_Engine($options);
		$tpl = $mustache->loadTemplate($view);

		return $tpl->render($args);
	}

	public function __toString() {
		return $this->render($this->view, $this->args);
	}

}
