<?php  //  -*- mode:php; tab-width:2; c-basic-offset:2; -*-

namespace mod\user;

class ModuleDefinition extends \core\ModuleDefinition {
  
  function __construct() {
    $this->description = 'Manage users, rights, permissions.';
    $this->name = 'user';
    $this->version = '0.1';
    $this->dependencies = array('regroute');
    parent::__construct();
  }
  
  function install() {    
    \mod\regroute\Main::registerRoute($this->id, '#^/login/?f?r?o?m?=?(.*)$#', 'mod_user_login');
    \mod\regroute\Main::registerRoute($this->id, '#^/logout/?$#', 'mod_user_logout');
    \mod\regroute\Main::registerRoute($this->id, '#^/user/([a-z]+)/?$#', 'mod_user_manage_users');
    \mod\regroute\Main::registerRoute($this->id, '#^/group/([a-z]+)/?$#', 'mod_user_manage_groups');
    
    parent::install();
  }
  
  function uninstall() {
    parent::uninstall();
  }
}
