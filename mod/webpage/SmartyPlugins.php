<?php  //  -*- mode:php; tab-width:2; c-basic-offset:2; -*-
namespace mod\webpage;

class SmartyPlugins {

  public static $csss = array();
  public static $scripts = array();

	public static function function_l($params, $template) {
		$mod = (isset($params['mod']) && !empty($params['mod'])) ? $params['mod'] : 'core';
		return $params['s'];
	}

	public static function function_css($params, $template) {
		$tplName = str_replace('.tpl', '', basename($template->template_resource));
		if (!isset($params['file'])) throw new \Exception("{css} function must have a 'file' parameter");
		if (!isset(self::$csss[$tplName])) self::$csss[$tplName] = array();
		if (!in_array($params['file'], self::$csss[$tplName]))
			self::$csss[$tplName][] = $params['file'];
		return (sizeof(self::$csss[$tplName]) < 2) ? 'CSSREPLACEME' : '';
	}

	public static function function_js($params, $template) {
		$tplName = str_replace('.tpl', '', basename($template->template_resource));
		if (!isset($params['file'])) throw new \Exception("{js} function must have a 'file' parameter");
		if (!isset(self::$scripts[$tplName])) self::$scripts[$tplName] = array();
		if (!in_array($params['file'], self::$scripts[$tplName]))
			self::$scripts[$tplName][] = $params['file'];
		return (sizeof(self::$scripts[$tplName]) < 2) ? 'JSREPLACEME' : '';
	}

	public static function processJsAndCss($output, $template) {
		$tplName = str_replace('.tpl', '', basename($template->template_resource));
		if (!isset(self::$csss[$tplName]) && !isset(self::$scripts[$tplName])) 
			return $output;
		$css = $js = '';
		foreach(self::$csss[$tplName] as $file) {
			$css .= '<link rel="stylesheet" href="'.$file.'" />'."\n";
		}
		foreach(self::$scripts[$tplName] as $file) {
			$js .= '<script type="text/javascript" src="'.$file.'"></script>'."\n";
		}
		return str_replace(array('CSSREPLACEME', 'JSREPLACEME'), array($css, $js), $output);
	}

}
