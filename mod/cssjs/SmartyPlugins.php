<?php  //  -*- mode:php; tab-width:2; c-basic-offset:2; -*-
namespace mod\cssjs;

class SmartyPlugins {

	public static function function_l($params, $template) {
		$mod = (isset($params['mod']) && !empty($params['mod'])) ? $params['mod'] : 'core';
		return $params['s'];
	}

	public static function function_css($params, $template) {
		$csss = &$template->smarty->tpl_vars['webpage']->value->csss;
		$tplName = str_replace('.tpl', '', basename($template->template_resource));
		if (!isset($params['file'])) throw new \Exception("{css} function must have a 'file' parameter");
		if (!isset($csss[$tplName])) $csss[$tplName] = array();
		if (!in_array($params['file'], $csss[$tplName]))
			$csss[$tplName][] = $params['file'];
		return (sizeof($csss[$tplName]) < 2) ? 'CSSREPLACEME' : '';
	}

	public static function function_js($params, $template) {
		$scripts = &$template->smarty->tpl_vars['webpage']->value->scripts;
		$tplName = str_replace('.tpl', '', basename($template->template_resource));
		if (!isset($params['file'])) throw new \Exception("{js} function must have a 'file' parameter");
		if (!isset($scripts[$tplName])) $scripts[$tplName] = array();
		if (!in_array($params['file'], $scripts[$tplName]))
			$scripts[$tplName][] = $params['file'];
		return (sizeof($scripts[$tplName]) < 2) ? 'JSREPLACEME' : '';
	}

	public static function outputFilter_processJsAndCss($output, $template) {
		$scripts = &$template->smarty->tpl_vars['webpage']->value->scripts;
		$csss = &$template->smarty->tpl_vars['webpage']->value->csss;
		$tplName = str_replace('.tpl', '', basename($template->template_resource));
		if (!isset($csss[$tplName]) && !isset($scripts[$tplName])) 
			return $output;
		$css = $js = '';
		if (is_array($csss) && sizeof($csss) > 0)
			foreach($csss[$tplName] as $file) {
				$css .= '<link rel="stylesheet" href="'.$file.'" />'."\n";
			}
		if (is_array($scripts) && sizeof($scripts) > 0)
			foreach($scripts[$tplName] as $file) {
				$js .= '<script type="text/javascript" src="'.$file.'"></script>'."\n";
			}
		return str_replace(array('CSSREPLACEME', 'JSREPLACEME'), array($css, $js), $output);
	}

	public static function block_embedjs($params, $content, $template) {
		$js = &$template->smarty->tpl_vars['webpage']->value->embedded_js;
		$js[] = $content;
	}

}
