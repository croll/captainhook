<?php  //  -*- mode:php; tab-width:2; c-basic-offset:2; -*-
namespace mod\cssjs;

class SmartyPlugins {

	public static function function_l($params, $template) {
		$mod = (isset($params['mod']) && !empty($params['mod'])) ? $params['mod'] : 'core';
		return $params['s'];
	}

	public static function function_css($params, $template) {
		$csss = &$template->smarty->tpl_vars->webpage->value->csss;
		if (!isset($params['file'])) throw new \Exception("{css} function must have a 'file' parameter");
		if (!in_array($params['file'], $csss))
			$csss[] = $params['file'];
		return;
	}

	public static function function_js($params, $template) {
		$scripts = &$template->smarty->tpl_vars->webpage->value->scripts;
		if (!isset($params['file'])) throw new \Exception("{js} function must have a 'file' parameter");
		if (!in_array($params['file'], $scripts))
			$scripts[] = $params['file'];
		return;
	}

	public static function outputFilter_processJsAndCss($output, $template) {
		$scripts = &$template->smarty->tpl_vars->webpage->value->scripts;
		$csss = &$template->smarty->tpl_vars->webpage->value->csss;
		if (!isset($csss) && !isset($scripts)) 
			return $output;
		$css = $js = '';
		if (is_array($csss) && sizeof($csss) > 0)
			foreach($csss as $file) {
				$css .= '<link rel="stylesheet" href="'.$file.'" />'."\n";
			}
		if (is_array($scripts) && sizeof($scripts) > 0)
			foreach($scripts as $file) {
				$js .= '<script type="text/javascript" src="'.$file.'"></script>'."\n";
			}
		return str_replace('</head>', $css.$js.'</head>', $output);
	}

	public static function block_embedjs($params, $content, $template) {
		$js = &$template->smarty->tpl_vars->webpage->value->embedded_js;
		$js[] = $content;
	}

}
