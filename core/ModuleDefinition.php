<?php  //  -*- mode:php; tab-width:2; c-basic-offset:2; -*-

namespace core;

abstract class ModuleDefinition {

		public $name;
		public $version;
		public $description;
		public $id;
		public $dependencies = array();
		private static $_cache = NULL;

		function __construct() {
			if (!isset(self::$_cache) || !isset(self::$_cache[$this->name]) || empty(self::$_cache[$this->name]['id'])) {
				if (!is_array(self::$_cache))
					self::$_cache = array();
				$modinfos = Core::$db->fetchRow('SELECT `mid` AS id, `active` FROM ch_module WHERE name=?',
																				array($this->name));
				if ($modinfos)
					self::$_cache[$this->name] = $modinfos;
			} 
			if (isset(self::$_cache[$this->name]) && is_array(self::$_cache[$this->name])) {
				$this->id = (isset(self::$_cache[$this->name]['id']) && (self::$_cache[$this->name]['id'])) ? self::$_cache[$this->name]['id'] : NULL;
				$this->active = (isset(self::$_cache[$this->name]['active']) && (self::$_cache[$this->name]['active'])) ? self::$_cache[$this->name]['active'] : NULL;
			} else {
				$this->id = NULL;
				$this->active = false;
			}
		}

		function install() {
			if ($this->id) throw new \Exception("install a module which already have an id ?");

			// Check if module is already loaded
			$exist = Core::$db->fetchOne('SELECT `mid` FROM ch_module WHERE name=?', 
																	 array($this->name));
			if ($exist) throw new \Exception("module already installed");

      $moddir = dirname(__FILE__).'/../mod/'.$this->name;
      $options=array();
      if (file_exists($moddir.'/smarty_plugins/')) $options[]='smarty_plugins';

			// Create module instance
			Core::$db->exec('INSERT INTO ch_module (`name`, `active`, `options`) VALUES (?,1,?)', 
                         array($this->name, implode(',', $options)));

			$this->id = Core::$db->lastInsertId();

      $this->install_hooks();
      \core\Hook::call('core_ModuleDefinition_install', $this);
		}

    private function install_hooks() {
      $moddir = dirname(__FILE__).'/../mod/'.$this->name;

      // Main.php
      if (is_file($moddir.'/Main.php')) {
        require_once($moddir.'/Main.php');
        $classname='\\mod\\'.$this->name.'\\Main';
        $methods = get_class_methods($classname);
        foreach($methods as $method) {
          if (!strncmp("hook_", $method, 5))
            \core\Hook::registerHookListener(substr($method, 5), '\\mod\\'.$this->name.'\\Main::'.$method, null, $this->id);
        }
      }

      // Hooks.php
      if (is_file($moddir.'/Hooks.php')) {
        require_once($moddir.'/Hooks.php');
        $classname='\\mod\\'.$this->name.'\\Hooks';
        $methods = get_class_methods($classname);
        foreach($methods as $method)
          \core\Hook::registerHookListener($method, '\\mod\\'.$this->name.'\\Hooks::'.$method, null, $this->id);
      }

    }

		function uninstall() {
			if (!$this->id) throw new \Exception("uninstall a module which don't have an id ?");

      \core\Hook::call('core_ModuleDefinition_uninstall', $this);

			// Delete hooks
			Hook::unregisterModuleListeners($this->id);

			$affected=Core::$db->exec('DELETE FROM ch_module WHERE `mid` = ? ', 
																array($this->id));

      if ($affected <= 0) throw new \Exception("Module was not installed in database");
      $this->id = null;
		}

		function enable() {
			if (!$this->id) throw new \Exception("enabling a module which don't have an id ?");

			// Enable module
			$affected=Core::$db->exec('UPDATE ch_module SET `active` = 1 WHERE mid=?', 
                         array($this->id));
      if ($affected <= 0) throw new \Exception("Module was not found in database");
		}

		function disable() {
			if (!$this->id) throw new \Exception("disable a module which don't have an id ?");

			// Disable module
			$affected=Core::$db->exec('UPDATE ch_module SET `active` = 0 WHERE mid=?', 
                         array($this->id));
      if ($affected <= 0) throw new \Exception("Module was not found in database");
		}

		function registerHookListener($name, $callback, $userdata, $position = 0) {
			if (substr($callback, 0, 1) != '\\') {
				$trace = debug_backtrace(false);
				$modNameDef = $trace[1]['class'];
				if(is_string($modNameDef)) {
					$callback = '\\'.str_replace(array('\\ModuleDefinition'), array(''), $modNameDef).'\\'.$callback;
				} else {
					throw new \Exception("Bad module name");
				}
			}
			return Hook::registerHookListener($name, $callback, $userdata, $this->id, $position);
		}

		function unregisterHookListener($name, $callback) {
			return Hook::unregisterHookListener($name, $callback, $this->id);
		}
}
