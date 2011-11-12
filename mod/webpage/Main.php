<?php

namespace mod\webpage;

class Main {
  public $title = "Captain Hook";
  public $favicon = "mod/webpage/images/favicon.ico";
  public $csss = array();
  public $scripts = array();
	public $layout = 'mod/webpage/hooks_templates/layout.tpl';

	function __construct() {
    $sm=\mod\smarty\Main::$smarty;
		$sm->registerFilter('output', array($this, 'processJsAndCss'));
    $sm->assign('title', $this->title);
    $sm->assign('favicon', $this->favicon);
	}

	public function setLayout($name) {
		$this->layout = $name;
	}

	public function display($template=NULL) {
		$file = (!is_null($template) && is_file($template)) ? $template : $this->layout;
    $sm=\mod\smarty\Main::$smarty;
    $sm->display($file);
	}

	public function processJsAndCss($output, $template) {
		$tplName = str_replace('.tpl', '', basename($template->resource_name));
		if (!isset($this->csss[$tplName]) && !isset($this->scripts[$tplName])) 
			return $output;
		$css = $js = '';
		foreach($this->csss[$tplName] as $file) {
			$css .= '<link rel="stylesheet" href="'.$file.'" />'."\n";
		}
		foreach($this->scripts[$tplName] as $file) {
			$js .= '<script type="text/javascript" src="'.$file.'"></script>'."\n";
		}
		return str_replace(array('CSSREPLACEME', 'JSREPLACEME'), array($css, $js), $output);
	}
}
