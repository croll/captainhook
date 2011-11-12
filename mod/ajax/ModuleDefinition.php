<?php

namespace mod\ajax;

class ModuleDefinition extends \core\ModuleDefinition {

	function __construct() {
		$this->description = 'Ajaxify your module.';
		$this->name = 'ajax';
		$this->version = '0.1';
		$this->dependencies = array('regroute');
		parent::__construct();
	}

	function install() {
		parent::install();
		\mod\regroute\Main::registerRoute($this->id, '#/ajax/(.*)#', 'mod_ajax');
	}

	function uninstall() {
    \mod\regroute\Main::unregister($this->id);
		parent::uninstall();
	}

}
