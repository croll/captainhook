<?php  //  -*- mode:php; tab-width:2; c-basic-offset:2; -*-

namespace mod\webpage;

class Main {
  public static $title = "Captain Hook";
  public static $favicon = "mod/webpage/images/favicon.ico";
  public static $csss = array();
  public static $scripts = array();
	public static $layout = 'mod/webpage/hooks_templates/layout.tpl';

  public static function hook_core_init_http($hookname, $userdata) {
    $sm=\mod\smarty\Main::$smarty;
		// Register post filter for JS and CSS processing
		$sm->registerFilter('output', array('mod\\webpage\\Main', 'processJsAndCss'));
    $sm->assign('title', self::$title);
    $sm->assign('favicon', self::$favicon);
  }

  public static function hook_core_process_http($hookname, $userdata) {
    \mod\smarty\Main::$smarty->display(self::$layout);
  }

	public static function setLayout($name) {
		self::$layout = $name;
	}

	public static function display($template=NULL) {
		$file = (!is_null($template) && is_file($template)) ? $template : self::$layout;
    $sm=\mod\smarty\Main::$smarty;
    $sm->display($file);
	}

	public static function processJsAndCss($output, $template) {
		$tplName = str_replace('.tpl', '', basename($template->resource_name));
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
