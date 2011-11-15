<?php  //  -*- mode:php; tab-width:2; c-basic-offset:2; -*-

namespace mod\cssjs;

class ModuleDefinition extends \core\ModuleDefinition {

	function __construct() {
		$this->description = 'Handle css and js files';
		$this->name = 'cssjs';
		$this->version = '0.1';
		$this->dependencies = array('smarty', 'webpage');
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
