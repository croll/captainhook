<?php  //  -*- mode:php; tab-width:2; c-basic-offset:2; -*-

namespace mod\#MODULENAME#;

class ModuleDefinition extends \core\ModuleDefinition {

	function __construct() {
		$this->description = '#MODULEDESCRIPTION#';
		$this->name = '#MODULENAME#';
		$this->version = '#MODULEVERSION#';
		$this->dependencies = array(#MODULEDEPENDENCIES#);
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
