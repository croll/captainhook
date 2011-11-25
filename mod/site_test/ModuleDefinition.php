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
		\mod\regroute\Main::registerRoute($this->id, '#^/$#', 'mod_site_test');
		\mod\regroute\Main::registerRoute($this->id, '#^/tests/(.*)$#', 'mod_site_test_tests');
		//\mod\regroute\Main::registerRoute($this->id, '#/p/(.*)#', 'mod_site_test');


    \core\Core::$db->exec("CREATE TABLE `ch_sitetest_person` ("
                             ." `id` INT(11) NOT NULL auto_increment,"
                             ." `firstname` VARCHAR(255) NOT NULL,"
                             ." `lastname` VARCHAR(255) NOT NULL,"
                             ." `gender` enum('male','female','other') NOT NULL,"
                             ." KEY `kperson` (`id`)"
                             .") ENGINE=InnoDB DEFAULT CHARSET=utf8");

	}

	function uninstall() {
    \mod\regroute\Main::unregister($this->id);
    \core\Core::$db->exec("DROP TABLE IF EXISTS `ch_sitetest_person`");
		parent::uninstall();
	}
}
