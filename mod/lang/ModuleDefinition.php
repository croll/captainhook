<?php  //  -*- mode:php; tab-width:2; c-basic-offset:2; -*-

namespace mod\lang;

class ModuleDefinition extends \core\ModuleDefinition {

	function __construct() {
		$this->description = 'traductions';
		$this->name = 'lang';
		$this->version = '1.0';
		$this->dependencies = array('smarty');
		parent::__construct();
	}

	function install() {
		parent::install();
    // do things here by default (after parent::install)

		\mod\regroute\Main::registerRoute($this->id, '#^/mod/lang/set_lang/([^/]*)/(.*)$#', 'mod_lang_set_lang');
	}

	function uninstall() {
    // do things here by default (before parent::uninstall)

		parent::uninstall();
	}
}
