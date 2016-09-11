<?php
namespace Insight\Base;

use Insight\Abstracts\Plugin as AbstractPlugin;
use Insight\Base\Script;
use Insight\Base\Style;
use Insight\Contracts\Plugin\Plugin as IPlugin;
use Insight\Repositories\QueryRepository;
use League\Event\Emitter;

class Plugin extends AbstractPlugin implements IPlugin {

	protected $query;
	protected $scripts_admin = array();
	protected $scripts_frontend = array();
	protected $styles_admin = array();
	protected $styles_frontend = array();

	/**
	 * Arrya with posttypes enables for metabox autosave
	 * @var array
	 */
	private $posttypes_metaboxes = array();

	/**
	 * Object Emitter
	 * @var League\Event\Emitter $emitter
	 */
	protected $emitter;

    /**
     * Plugin constructor.
     */
    public function __construct() {
        
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

	protected function add_scripts_action() {
		//add css and js for backend
		add_action('admin_print_scripts-post.php', array($this, 'add_admin_assets'), 1000);
		add_action('admin_print_scripts-post-new.php', array($this, 'add_admin_assets'), 1000);

		//add css and js for frontend
		add_action('wp_enqueue_scripts', array($this, 'add_frontend_assets'), 1000);
	}

	public function add_admin_assets() {
		global $post_type;
        do_action('app_before_admin_assets');
		if (count($this->scripts_admin) > 0) {
			foreach ($this->scripts_admin as $js) {
				if ($js->post_type == $post_type) {
					$js->enqueue();
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
        do_action('app_after_admin_assets');
	}

	public function add_frontend_assets() {
		global $post_type;
        do_action('app_before_frontend_assets');
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
        do_action('app_after_frontend_assets');
	}

	/**
	 * Boot class Inicializa el plugin
	 * @return void
	 */
	public function boot() {
		$this->emitter = new Emitter;
		$this->query = new QueryRepository();
		//Busca las acciones definidas en el plugin y las agrega como acciones de Wordpress
		$this->add_actions();
		add_action('cmb2_admin_init', array($this, 'add_metaboxes'), 1000);
	}

	/**
	 * Registra y encola todos los metabox para los metodos definidos
	 */
	public function add_metaboxes() {
		$methos = $this->getMethods("/^metabox_/");
		sort($methos); //Se ordenas los metodos por prioridad
		if ($methos) {
			foreach ($methos as $metabox) {
                call_user_func(array($this, $metabox));
			}
		}
	}

	/**
	 * Registra las acciones para los metodos definidos en el plugin
	 */
	protected function add_actions() {
        do_action('app_before_actions');
		$actionMethos = $this->getMethods("/^action_/");
		sort($actionMethos); //Se ordenas las acciones por prioridad
		$actions = array_map(array($this, 'describeActions'), $actionMethos);
		if ($actions) {
            foreach ($actions as $action) {
                add_action($action->tag, array($this, $action->method), $act->priority, 1);
            }
            do_action('app_after_textdomain');
		}
	}

	/**
	 * Obtiene un array con las acciones que se encuentras definidas en la clase del plugin
	 * @return array Array de string con los nombres de los metodos definidos en la clase del plugin
	 */
	private function getMethods($pattern = "/^action_/") {
		$methos = get_class_methods($this->class);
		$actions = array();

		foreach ($methos as $methodName) {
			if (preg_match($pattern, $methodName)) {
				array_push($actions, $methodName);
			}
		}
		return $actions;
	}

	/**
	 * Descrive una accion para el nombre del metodo de la accion resibido
	 * @param  string $action Nombre o metodo de la accion
	 * @return stdClass
	 * {
	 *  "method": "action_postypes_init_10"
	 *  "name": "postypes"
	 *  "tag": "init"
	 *  "priority": 10
	 * }
	 */
	private function describeActions($action = "") {

		if (!empty($action)) {
			$parts = explode('_', $action);
			unset($parts[0]);
			$parts = array_values($parts);

			$act = new \stdClass();

			$act->method = $action;

			switch (count($parts)) {
			case 1:
			case 2:
			case 3:
				$act->name = $parts[0];
			case 2:
			case 3:
				$act->tag = $parts[1];
			case 3:
				$act->priority = intval($parts[2]);
				break;
			}
			if (count($parts) == 1) {
				$act->tag = 'initi';
				$act->priority = 10;
			}

			return $act;

		}
		return null;
	}

	/**
	 * Descrive una los metodos que implementan metabox
	 * @param  string $method Nombre o metodo de la que renderiza un metabox
	 * @return stdClass
	 *  {
	 *   "id": "recommendedLinks"
	 *   "title": "Recommended Links"
	 *   "callback": "metabox_recommendedLinks_dummy_otroTest_high"
	 *   "priority": "high"
	 *   "screen": array:2 [
	 *        0 => "dummy"
	 *        1 => "otro_test"
	 *    ]
	 *  }
	 */

	public static function __callStatic($name, $arguments) {
		$self = new self();

		if (method_exists($self->class, $name)) {
			return call_user_func_array(array($self, $name), $arguments);
		}
		return null;
	}

	public function __get($name) {
		if (isset($this->{$name})) {
			return $this->{$name};
		}
		return null;
	}

	public function __toString() {
		return $this->class;
	}

}
