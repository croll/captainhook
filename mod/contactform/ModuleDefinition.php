<?php  //  -*- mode:php; tab-width:2; c-basic-offset:2; -*-

namespace mod\contactform;

class ModuleDefinition extends \core\ModuleDefinition {

	function __construct() {
		$this->description = 'A simple contact form';
		$this->name = 'contactform';
		$this->version = '1.0';
		$this->dependencies = array('ajax', 'config', 'cssjs', 'form', 'webpage');
		parent::__construct();
	}

	function install() {
		parent::install();
		\mod\regroute\Main::registerRoute($this->id, '#^/contact/?$#', 'mod_contact_init');
		\mod\regroute\Main::registerRoute($this->id, '#^/contact/submit#', 'mod_contact_submit');
		\mod\regroute\Main::registerRoute($this->id, '#^/contact/admin#', 'mod_contact_admin');
		\mod\user\Main::addRight('Configure contactform module', 'Can change contact form options.');			
		\mod\user\Main::assignRight('Configure contactform module', 'Admin');
	}

	function uninstall() {
		\mod\user\Main::delRight('Configure contactform module');			
  	\mod\regroute\Main::unregister($this->id);
		parent::uninstall();
	}
}
