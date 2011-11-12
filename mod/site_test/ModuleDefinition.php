<?php  //  -*- mode:php; tab-width:2; c-basic-offset:2; -*-

namespace mod\site_test;

class ModuleDefinition extends \core\ModuleDefinition {

	function __construct() {
		$this->description = 'Simple site';
		$this->name = 'site_test';
		$this->version = 0.1;
		$this->dependencies = array('smarty', 'regroute', 'webpage');
		parent::__construct();
	}

	function install() {
		parent::install();
		\mod\regroute\Main::registerRoute($this->id, '#/$#', 'mod_site_test');
		\mod\regroute\Main::registerRoute($this->id, '#/p/(.*)#', 'mod_site_test');
	}

	function uninstall() {
    \mod\regroute\Main::unregister($this->id);
		parent::uninstall();
	}
}
