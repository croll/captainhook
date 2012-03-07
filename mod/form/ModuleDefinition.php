<?php  //  -*- mode:php; tab-width:2; c-basic-offset:2; -*-

namespace mod\form;

class ModuleDefinition extends \core\ModuleDefinition {

	function __construct() {
		$this->description = 'Allow to create html forms and validators for both PHP and JS';
		$this->name = 'form';
		$this->version = '1.0';
		$this->dependencies = array('smarty', 'regroute');
		parent::__construct();
	}

	function install() {
		parent::install();
		\mod\regroute\Main::registerRoute($this->id, '#/form/submit/(.*)$#', 'mod_form_submit');
	}

	function uninstall() {
		parent::uninstall();
	}
}
