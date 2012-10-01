<?php  //  -*- mode:php; tab-width:2; c-basic-offset:2; -*-

namespace mod\webpage;

class Main {
  public $title = "Captain Hook";
  public $favicon = "/mod/webpage/images/favicon.ico";
	public $layout = 'webpage/webpage_main';
  public $csss = array();
  public $scripts = array();
	public $smarty;
	public $embedded_js = array();

	function __construct() {
    $this->smarty =\mod\smarty\Main::newSmarty();
		$this->smarty->assign('extends_webpage_main', 'webpage/webpage_html4');
    $this->smarty->assign('title', $this->title);
    $this->smarty->assign('favicon', $this->favicon);
		$this->smarty->assignByRef('webpage', $this);
		\core\Hook::call("mod_webpage_construct", $this);
	}

	public function setLayout($name) {
		$this->layout = $name;
	}

	public function display($template=NULL) {
		$file = (!is_null($template) && is_file($template)) ? $template : $this->layout;
    $this->smarty->display($file);
	}

	public function toJSON($template=NULL) {
		$file = (!is_null($template) && is_file($template)) ? $template : $this->layout;
		$res = (object)NULL;
		$res->html = $this->smarty->fetch($file);
		$res->js = trim(implode('', $this->embedded_js));
		return json_encode($res);
	}
}
