<?php

namespace mod\page;

class ModuleDefinition extends \core\ModuleDefinition {

	function __construct() {
		$this->description = 'Display web pages.';
		$this->name = 'page';
		$this->version = 0.1;
		$this->dependencies = array('website', 'user');
		parent::__construct();
	}

	function install() {
		parent::install();
	}

	function uninstall() {
		parent::uninstall();
	}
}
