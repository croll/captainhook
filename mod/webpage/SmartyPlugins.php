<?php  //  -*- mode:php; tab-width:2; c-basic-offset:2; -*-
namespace mod\webpage;

class SmartyPlugins {

	public static function function_l($params, $template) {
		$mod = (isset($params['mod']) && !empty($params['mod'])) ? $params['mod'] : 'core';
		return $params['s'];
	}

	public static function function_css($params, $template) {
		$tplName = str_replace('.tpl', '', basename($template->resource_name));
		if (!isset($params['file'])) throw new \Exception("{css} function must have a 'file' parameter");
		if (!isset(\mod\webpage\Main::$csss[$tplName])) \mod\webpage\Main::$csss[$tplName] = array();
		if (!in_array($params['file'], \mod\webpage\Main::$csss[$tplName]))
			\mod\webpage\Main::$csss[$tplName][] = $params['file'];
		return (sizeof(\mod\webpage\Main::$csss[$tplName]) < 2) ? 'CSSREPLACEME' : '';
	}

	public static function function_js($params, $template) {
		$tplName = str_replace('.tpl', '', basename($template->resource_name));
		if (!isset($params['file'])) throw new \Exception("{js} function must have a 'file' parameter");
		if (!isset(\mod\webpage\Main::$scripts[$tplName])) \mod\webpage\Main::$scripts[$tplName] = array();
		if (!in_array($params['file'], \mod\webpage\Main::$scripts[$tplName]))
			\mod\webpage\Main::$scripts[$tplName][] = $params['file'];
		return (sizeof(\mod\webpage\Main::$scripts[$tplName]) < 2) ? 'JSREPLACEME' : '';
	}

}
