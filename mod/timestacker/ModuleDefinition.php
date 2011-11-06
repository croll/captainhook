<?php

namespace mod\timestacker;

class ModuleDefinition extends \core\ModuleDefinition {

	function __construct() {
		$this->name = 'timestacker';
		$this->description = 'Manage your time';
		$this->version = "0.1";
		$this->dependencies = array('user');
		parent::__construct();
	}

	function install() {
    parent::install();
	}

	function uninstall() {
		parent::uninstall();
	}
}
