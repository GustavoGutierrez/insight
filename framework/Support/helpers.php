<?php
if (!function_exists('env')) {
	/**
	 *  Obtiene el valor de una variable de entorno
	 * @param  string $varname Nombre de la variable de entorno
	 * @param  string $default Valor por defecto en caso de no exitir la variable de entoeno
	 * @return string Valor de la variable de entorno
	 */
	function env($varname, $default = 'production') {

		if (getenv($varname)) {
			return getenv($varname);
		}
		return $default;

	}
}

if (!function_exists('config')) {
	/**
	 * Get / value from key configuration file.
	 *
	 * @param  array  $key
	 * @param  mixed  $default
	 * @return mixed
	 */

	function config($key = null, $default = null) {
		if (is_null($key)) {
			return null;
		}
		return Insight\Config\Config::get($key, $default);
	}
}

if (!function_exists('view')) {
	/**
	 * Obtiene y renderiza una vista
	 * @param  string $view path
	 * @param  array  $args [description]
	 * @return Insight\View\View;
	 */
	function view($view, $args = array()) {
		return new Insight\View\View($view, $args);
	}

}
