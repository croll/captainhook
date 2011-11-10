<?php

namespace mod\form;

class ModuleDefinition extends \core\ModuleDefinition {

	function __construct() {
		$this->description = 'Build forms';
		$this->name = 'form';
		$this->version = '0.1';
		$this->dependencies = array('field');
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
