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
    parent::install();
  }
  
  function uninstall() {
    parent::uninstall();
  }
}
