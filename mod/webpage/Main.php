<?php  //  -*- mode:php; tab-width:2; c-basic-offset:2; -*-

namespace mod\webpage;

class Main {
  public $title = "Captain Hook";
  public $favicon = "mod/webpage/images/favicon.ico";
	public $layout = 'mod/webpage/templates/webpage_main.tpl';

	function __construct() {
    $sm=\mod\smarty\Main::$smarty;
		$sm->assign('extends_webpage_main', 'mod/webpage/templates/webpage_html4.tpl');
		$sm->registerFilter('output', array('mod\webpage\SmartyPlugins', 'processJsAndCss'));
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
}
