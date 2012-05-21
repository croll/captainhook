<?php  //  -*- mode:php; tab-width:2; c-basic-offset:2; -*-

namespace mod\googlecharttools;

class ModuleDefinition extends \core\ModuleDefinition {

	function __construct() {
		$this->description = 'Google chart tools';
		$this->name = 'googlecharttools';
		$this->version = '1.0';
		$this->dependencies = array('webpage');
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
