<?php  //  -*- mode:php; tab-width:2; c-basic-offset:2; -*-

namespace mod\page;

class ModuleDefinition extends \core\ModuleDefinition {

	function __construct() {
		$this->description = 'edit web page content';
		$this->name = 'page';
		$this->version = '0.1';
		$this->dependencies = array('ajax', 'cssjs', 'smarty', 'regroute', 'lang');
		parent::__construct();
	}

	function install() {
		parent::install();
		
    		// do things here by default (after parent::install)
		// set default route
		\mod\regroute\Main::registerRoute($this->id, '#^/page/([a-z0-9_-]+)$#', 'mod_page_render', \mod\regroute\Main::flag_html | \mod\regroute\Main::flag_xmlhttprequest);
		\mod\regroute\Main::registerRoute($this->id, '#^/page/edit/([0-9]+)$#', 'mod_page_edit', \mod\regroute\Main::flag_html | \mod\regroute\Main::flag_xmlhttprequest);
		\mod\regroute\Main::registerRoute($this->id, '#^/page/list/([a-z0-9/_:@]+)?$#', 'mod_page_list', \mod\regroute\Main::flag_html | \mod\regroute\Main::flag_xmlhttprequest);
		
		// create rights 

    \mod\user\Main::addRight('View page', 'Allow user to see pages');
    \mod\user\Main::addRight('Manage page', 'Allow user to add/edit/delete pages');
		//assign rights to default groups 
		\mod\user\Main::assignRight('View page', 'Admin');
		\mod\user\Main::assignRight('Manage page', 'Admin');

	}

	function uninstall() {
		\mod\user\Main::delRight('View page');
    \mod\user\Main::delRight('Manage page');
		
		// do things here by default (before parent::uninstall)
		// delete route
		\mod\regroute\Main::unregister($this->id);
 		parent::uninstall();
	}
}
