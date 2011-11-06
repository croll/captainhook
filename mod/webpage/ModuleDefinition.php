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

    // do things here by default (after parent::install)
	}

	function uninstall() {
    // do things here by default (before parent::uninstall)

		parent::uninstall();
	}
}
