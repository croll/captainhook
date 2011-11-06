<?php

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
    \core\Core::$db->execute("CREATE TABLE `ch_regroute` ("
                             ." `id_module` INT(11) NULL,"
                             ." `regexp` VARCHAR(255) NOT NULL,"
                             ." `hook` VARCHAR(255) NOT NULL,"
                             ." KEY `kidmodule` (`id_module`)"
                             .") ENGINE=InnoDB DEFAULT CHARSET=utf8");
    
		parent::install();
	}
  
	function uninstall() {
		parent::uninstall();
    \core\Core::$db->execute("DROP TABLE `ch_regroute`");
	}
}
