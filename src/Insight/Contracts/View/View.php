<?php
namespace Insight\Contracts\View;

interface View {
	/**
	 * @param  string $view file name template
	 * @param  array $args argumentos pasados a la vista
	 * @return string View rendered
	 */
	public function render($view, $args = array());
}
