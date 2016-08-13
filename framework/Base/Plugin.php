<?php
namespace Insight\Base;

use Insight\Abstracts\Plugin as AbstractPlugin;
use Insight\Base\Script;
use Insight\Base\Style;
use Insight\Contracts\Plugin\Plugin as IPlugin;
use Insight\Repositories\QueryRepository;
//use Insight\Traits\HelperDebugMethods;
use League\Event\Emitter;

class Plugin extends AbstractPlugin implements IPlugin {

	//use HelperDebugMethods;

	protected $query;

	protected $scripts_admin = array();
	protected $scripts_frontend = array();

	protected $styles_admin = array();
	protected $styles_frontend = array();
	/**
	 * Object Emitter
	 * @var League\Event\Emitter $emitter
	 */
	protected $emitter;

	public function __construct() {
		// $this->query = $QueryRepository;
		// $this->register_actions();
	}

	/**
	 * Obtiene el Path absoluto hasta la carpeta del plugin builder_complex_app
	 * @return string Url hasta el plugin padre
	 */
	protected function get_path() {
		return dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR;
	}
	/**
	 * Obtiene el url absoluto hasta la carpeta del plugin builder_complex_app
	 * @return string Url hasta el plugin padre
	 */
	protected function get_uri() {
		//plugins_url('/', __FILE__);
		return WP_PLUGIN_URL . '/' . $this->get_plugin_name() . '/';
	}

	/**
	 * @param  string Ruta a la carpeta assets
	 * @return string Url hasta la carpeta ssets del app concatenandola con la ruta especificada
	 */
	protected function assetUrl($target = '') {
		return $this->get_uri() . 'assets/' . $target;
	}
	/**
	 * Permite agregar un js a la pila de scripts para ser cargado en el punto espesificado
	 * @param  string $src nombre o ruta del archivo js que se encuentra en assets/js
	 * @param  array   $opts     Opciones de configuracion necesarias para cargar el js
	 * @param  boolean $in_admin Indica si el js sera cargado en el administrador
	 *                           o en el frontend
	 * @return void
	 */
	public function embedJs($src, $opts = array(), $in_admin = false) {
		$js = new Script($src, $opts, $in_admin);

		if ($js->getInAdmin()) {
			array_push($this->scripts_admin, $js);
		} else {
			array_push($this->scripts_frontend, $js);
		}
	}

	/**
	 * Permite agregar un css a la pila de styles para ser cargado en el punto espesificado
	 * @param  string $src  nombre o ruta del archivo css que se encuentra en assets/css
	 * @param  array   $opts     Opciones de configuracion necesarias para cargar el css
	 * @param  boolean $in_admin Indica si el css sera cargado en el administrador
	 *                           o en el frontend
	 * @return void
	 */
	public function embedCss($src, $opts = array(), $in_admin = false) {
		$css = new Style($src, $opts, $in_admin);

		if ($css->getInAdmin()) {
			array_push($this->styles_admin, $css);
		} else {
			array_push($this->styles_frontend, $css);
		}
	}

	/**
	 * get folder name of plugin builder_complex_app
	 * @return string folder plugin parent name
	 */
	protected function get_plugin_name() {
		$path = $this->get_path();
		$array = explode('/', $path);
		unset($array[count($array) - 1]);
		unset($array[count($array) - 1]);
		return end($array) . '/' . DIRECTORY_APP_NAME;
	}

	/**
	 * Load all actions
	 * @return void
	 */
	protected function register_actions() {
		$helpers_uses = class_uses($this);

		if (count($helpers_uses) > 0) {

			if (method_exists($this, 'register_cpt')) {
				$this->register_cpt();
			}

			if (method_exists($this, 'register_tx')) {
				$this->register_tx();
			}

			foreach ($helpers_uses as $helper) {

				$function = new \ReflectionClass($helper);
				if ($function->isTrait()) {
					add_action('init', array(
						$this,
						'Init_Action_' . end(explode('\\', $helper)),
					), 0);
				}

			}

		}
	}

	protected function add_scripts_action() {

		//add css and js for backend
		add_action('admin_print_scripts-post.php', array($this, 'add_admin_assets'), 1000);
		add_action('admin_print_scripts-post-new.php', array($this, 'add_admin_assets'), 1000);

		//add css and js for frontend
		add_action('wp_enqueue_scripts', array($this, 'add_frontend_assets'), 1000);
	}

	public function add_admin_assets() {
		global $post_type;

		if (count($this->scripts_admin) > 0) {
			foreach ($this->scripts_admin as $js) {
				if ($js->post_type == $post_type) {
					$js->enqueue();
				} else {
				}
			}
		}

		if (count($this->styles_admin) > 0) {
			foreach ($this->styles_admin as $css) {
				if ($css->post_type == $post_type) {
					$css->enqueue();
				}
			}
		}
	}

	public function add_frontend_assets() {
		global $post_type;
		if (count($this->scripts_frontend) > 0) {
			foreach ($this->scripts_frontend as $js) {
				if ($js->post_type == $post_type) {
					$js->enqueue();
				}
			}
		}

		if (count($this->styles_frontend) > 0) {
			foreach ($this->styles_frontend as $css) {
				if ($css->post_type == $post_type) {
					$css->enqueue();
				}
			}
		}
	}

	public function boot() {
		$this->emitter = new Emitter;
		//Register all acction of the plugin
		//$this->__construct(new QueryRepository());
		//$this->container = $container;
		//
		$this->query = new QueryRepository();
		$this->register_actions();

	}

}
