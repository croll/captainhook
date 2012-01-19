<?php  //  -*- mode:php; tab-width:2; c-basic-offset:2; -*-

namespace mod\regroute;

class ModuleDefinition extends \core\ModuleDefinition {
  
	function __construct() {
		$this->description = 'Regular Expression Routing';
		$this->name = 'regroute';
		$this->version = 0.1;
		$this->dependencies = array('webpage');
		parent::__construct();
	}
  
	function install() {
		parent::install();
	}
  
	function uninstall() {
		parent::uninstall();
	}
}
