<?php  //  -*- mode:php; tab-width:2; c-basic-offset:2; -*-

namespace mod\user;

class ModuleDefinition extends \core\ModuleDefinition {

	function __construct() {
		$this->description = 'Manage users, rights, permissions.';
		$this->name = 'user';
		$this->version = '0.1';
		$this->dependencies = array();
		parent::__construct();
	}

	function install() {

    \core\Core::$db->Execute("CREATE TABLE `ch_user` ("
                             ." `uid` int(11) NOT NULL AUTO_INCREMENT,"
                             ." `full_name` varchar(255) NOT NULL,"
                             ." `login` varchar(32) NOT NULL,"
                             ." `pass` varchar(32) DEFAULT NULL,"
                             ." `hash` varchar(64) DEFAULT NULL,"
                             ." `status` int(1) NOT NULL DEFAULT '1',"
                             ." `last_connexion` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP,"
                             ." PRIMARY KEY (`uid`)"
                             .") ENGINE=InnoDB DEFAULT CHARSET=utf8");

    \core\Core::$db->Execute("INSERT INTO `ch_user` (`uid`, `full_name`, `login`, `pass`, `status`) VALUES (1,'The Admin', 'admin', MD5('admin'), 1)");
    
    \core\Core::$db->Execute("CREATE TABLE `ch_group` ("
                             ." `gid` int(11) NOT NULL AUTO_INCREMENT,"
                             ." `name` varchar(255) NOT NULL,"
                             ." `status` int(1) NOT NULL DEFAULT '1',"
                             ." PRIMARY KEY (`gid`)"
                             .") ENGINE=InnoDB DEFAULT CHARSET=utf8");

    \core\Core::$db->Execute("INSERT INTO `ch_group` VALUES (1,'Admin',1)");
    \core\Core::$db->Execute("INSERT INTO `ch_group` VALUES (2,'Registered',1)");
    \core\Core::$db->Execute("INSERT INTO `ch_group` VALUES (3,'Anonymous',1)");

    \core\Core::$db->Execute("CREATE TABLE `ch_user_group` ("
                             ." `ugid` int(11) NOT NULL AUTO_INCREMENT,"
                             ." `uid` int(11) NOT NULL,"
                             ." `gid` int(11) DEFAULT NULL,"
                             ." PRIMARY KEY (`ugid`),"
                             ." KEY `kuid` (`uid`),"
                             ." KEY `kgid` (`gid`)"
                             .") ENGINE=InnoDB DEFAULT CHARSET=utf8");

    \core\Core::$db->Execute("INSERT INTO `ch_user_group` (`uid`, `gid`) VALUES (1, 1)");

    \core\Core::$db->Execute("CREATE TABLE `ch_right` ("
                             ." `rid` int(11) NOT NULL AUTO_INCREMENT,"
                             ." `name` varchar(50) NOT NULL,"
                             ." `description` varchar(100) DEFAULT NULL,"
                             ." PRIMARY KEY (`rid`),"
                             ." KEY `kname` (`name`)"
                             .") ENGINE=InnoDB DEFAULT CHARSET=utf8");
		
		\core\Core::$db->Execute("INSERT INTO `ch_right` (`rid`, `name`, `description`) VALUES (1, 'View rights', 'Allow user to see rights in admin panel.')");
		\core\Core::$db->Execute("INSERT INTO `ch_right` (`rid`, `name`, `description`) VALUES (2, 'Manage rights', 'User can add/edit/delete rights.')");

    \core\Core::$db->Execute("CREATE TABLE `ch_group_right` ("
                             ." `grid` int(11) NOT NULL AUTO_INCREMENT,"
                             ." `gid` int(11) NOT NULL,"
                             ." `rid` int(11) DEFAULT NULL,"
                             ." PRIMARY KEY (`grid`),"
                             ." KEY `krid` (`rid`),"
                             ." KEY `kgid` (`gid`)"
                             .") ENGINE=InnoDB DEFAULT CHARSET=utf8");

    \core\Core::$db->Execute("INSERT INTO `ch_group_right` (`gid`, `rid`) VALUES (1, 1)");
    \core\Core::$db->Execute("INSERT INTO `ch_group_right` (`gid`, `rid`) VALUES (1, 2)");

		\mod\regroute\Main::registerRoute($this->id, '#/login/?u?r?l?=?(.*)$#', 'mod_user_login');
		\mod\regroute\Main::registerRoute($this->id, '#/logout/?$#', 'mod_user_logout');
		parent::install();
	}

	function uninstall() {
		parent::uninstall();
		\core\Core::$db->Execute("DROP TABLE `ch_user`");
		\core\Core::$db->Execute("DROP TABLE `ch_group`");
    \core\Core::$db->Execute("DROP TABLE `ch_user_group`");
    \core\Core::$db->Execute("DROP TABLE `ch_right`");
    \core\Core::$db->Execute("DROP TABLE `ch_group_right`");
	}
}
