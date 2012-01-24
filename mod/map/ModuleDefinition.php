<?php  //  -*- mode:php; tab-width:2; c-basic-offset:2; -*-

namespace mod\map;

class ModuleDefinition extends \core\ModuleDefinition {

	function __construct() {
		$this->description = 'Embed maps inside your application';
		$this->name = 'map';
		$this->version = '1.0';
		$this->dependencies = array('cssjs');
		parent::__construct();
	}

	function install() {
		parent::install();
	}

	function uninstall() {
	}
}
