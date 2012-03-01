<?php  //  -*- mode:php; tab-width:2; c-basic-offset:2; -*-

namespace mod\langeditor;

class ModuleDefinition extends \core\ModuleDefinition {

	function __construct() {
		$this->description = 'Allow editing of traductions files (used by lang mod)';
		$this->name = 'langeditor';
		$this->version = '1.0';
		$this->dependencies = array('lang', 'regroute', 'webpage', 'cssjs', 'ajax', 'smarty');
		parent::__construct();
	}

	function install() {
		parent::install();
		\mod\regroute\Main::registerRoute($this->id, '#^/langeditor$#', 'mod_langeditor_index');

    // do things here by default (after parent::install)
	}

	function uninstall() {
    // do things here by default (before parent::uninstall)

		parent::uninstall();
	}
}
