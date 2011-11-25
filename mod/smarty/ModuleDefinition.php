<?php  //  -*- mode:php; tab-width:2; c-basic-offset:2; -*-

namespace mod\smarty;

class ModuleDefinition extends \core\ModuleDefinition {

	function __construct() {
		$this->description = 'Smarty';
		$this->name = 'smarty';
		$this->version = '0.1';
		$this->dependencies = array();
		parent::__construct();
	}

	function install() {
    \core\Core::$db->exec("CREATE TABLE `ch_smarty_plugins` ("
                             ." `id_module` INT(11) NULL,"
                             ." `name` VARCHAR(255) NOT NULL,"
                             ." `type` ENUM('function','block','compiler','modifier','preFilter','postFilter','outputFilter') NOT NULL,"
                             ." `method` VARCHAR(255) NOT NULL,"
                             ." KEY `kidmodule` (`id_module`),"
                             ." KEY `kname` (`name`)"
                             .") ENGINE=InnoDB DEFAULT CHARSET=utf8");

    \core\Core::$db->exec("CREATE TABLE `ch_smarty_override` ("
                             ." `id_module` INT(11) NULL,"
                             ." `orig` VARCHAR(255) NOT NULL,"
                             ." `replace` VARCHAR(255) NOT NULL,"
                             ." KEY `kidmodule` (`id_module`),"
                             ." KEY `korig` (`orig`)"
                             .") ENGINE=InnoDB DEFAULT CHARSET=utf8");
		parent::install();
	}

	function uninstall() {
		parent::uninstall();
    \core\Core::$db->exec("DROP TABLE IF EXISTS `ch_smarty_plugins`");
    \core\Core::$db->exec("DROP TABLE IF EXISTS `ch_smarty_override`");
	}
}
