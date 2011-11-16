<?php  //  -*- mode:php; tab-width:2; c-basic-offset:2; -*-
namespace mod\webpage;

class SmartyPlugins {

	public static function function_l($params, $template) {
		$mod = (isset($params['mod']) && !empty($params['mod'])) ? $params['mod'] : 'core';
		return $params['s'];
	}

	public static function function_css($params, $template) {
		$csss = &$template->smarty->tpl_vars->webpage->value->csss;
		$tplName = str_replace('.tpl', '', basename($template->template_resource));
		if (!isset($params['file'])) throw new \Exception("{css} function must have a 'file' parameter");
		if (!isset($csss[$tplName])) $csss[$tplName] = array();
		if (!in_array($params['file'], $csss[$tplName]))
			$csss[$tplName][] = $params['file'];
		return (sizeof($csss[$tplName]) < 2) ? 'CSSREPLACEME' : '';
	}

	public static function function_js($params, $template) {
		$scripts = &$template->smarty->tpl_vars->webpage->value->scripts;
		$tplName = str_replace('.tpl', '', basename($template->template_resource));
		if (!isset($params['file'])) throw new \Exception("{js} function must have a 'file' parameter");
		if (!isset($scripts[$tplName])) $scripts[$tplName] = array();
		if (!in_array($params['file'], $scripts[$tplName]))
			$scripts[$tplName][] = $params['file'];
		return (sizeof($scripts[$tplName]) < 2) ? 'JSREPLACEME' : '';
	}

}
