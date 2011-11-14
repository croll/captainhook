<?php  //  -*- mode:php; tab-width:2; c-basic-offset:2; -*-

namespace mod\webpage;

class Main {
  public $title = "Captain Hook";
  public $favicon = "mod/webpage/images/favicon.ico";
	public $layout = 'mod/webpage/templates/webpage_main.tpl';
  public $csss = array();
  public $scripts = array();
	public $smarty;
	public $embedded_js = array();

	function __construct() {
    $this->smarty =\mod\smarty\Main::newSmarty();
		$this->smarty->assign('extends_webpage_main', 'mod/webpage/templates/webpage_html4.tpl');
    $this->smarty->assign('title', $this->title);
    $this->smarty->assign('favicon', $this->favicon);
		$this->smarty->assign('webpage', $this);
		$this->smarty->registerFilter('output', array($this, 'processJsAndCss'));
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

	public function processJsAndCss($output, $template) {
		$tplName = str_replace('.tpl', '', basename($template->template_resource));
		if (!isset($this->csss[$tplName]) && !isset($this->scripts[$tplName])) 
			return $output;
		$css = $js = '';
		if (is_array($this->csss) && sizeof($this->csss) > 0)
			foreach($this->csss[$tplName] as $file) {
				$css .= '<link rel="stylesheet" href="'.$file.'" />'."\n";
			}
		if (is_array($this->scripts) && sizeof($this->scripts) > 0)
			foreach($this->scripts[$tplName] as $file) {
				$js .= '<script type="text/javascript" src="'.$file.'"></script>'."\n";
			}
		return str_replace(array('CSSREPLACEME', 'JSREPLACEME'), array($css, $js), $output);
	}
}
