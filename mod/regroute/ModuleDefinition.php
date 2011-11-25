<?php  //  -*- mode:php; tab-width:2; c-basic-offset:2; -*-

namespace mod\regroute;

class ModuleDefinition extends \core\ModuleDefinition {
  
	function __construct() {
		$this->description = 'Regular Expression Routing';
		$this->name = 'regroute';
		$this->version = 0.1;
		$this->dependencies = array('webpage');
		parent::__construct();
	}
  
	function install() {
    \core\Core::$db->exec("CREATE TABLE `ch_regroute` ("
                             ." `id_module` INT(11) NULL,"
                             ." `regexp` VARCHAR(255) NOT NULL,"
                             ." `hook` VARCHAR(255) NOT NULL,"
                             ." `flags` INT(11) NOT NULL,"
                             ." KEY `kidmodule` (`id_module`)"
                             .") ENGINE=InnoDB DEFAULT CHARSET=utf8");
    
		parent::install();
	}
  
	function uninstall() {
		parent::uninstall();
    \core\Core::$db->exec("DROP TABLE `ch_regroute`");
	}
}
