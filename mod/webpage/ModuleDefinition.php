<?php

namespace mod\webpage;

class ModuleDefinition extends \core\ModuleDefinition {

	function __construct() {
		$this->description = 'Display a Web Page';
		$this->name = 'webpage';
		$this->version = '0.1';
		$this->dependencies = array('smarty');
		parent::__construct();
	}

	function install() {
		parent::install();
	}

	function uninstall() {
		parent::uninstall();
	}
}
