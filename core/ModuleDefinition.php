<?php  //  -*- mode:php; tab-width:2; c-basic-offset:2; -*-
/**
 * CaptainHook
 *
 * PHP Version 5
 *
 * @category  CaptainHook
 * @package   Core 
 * @author    Christophe Beveraggi (beve) and Nicolas Dimitrijevic (niclone)
 * @copyright 2011-2012 CROLL (http://www.croll.fr)
 * @link      http://github.com/croll/captainhook
 * @license   LGPLv3
 *
 * CaptainHook is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * 
 * CaptainHook is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with CaptainHook.  If not, see <http://www.gnu.org/licenses/>.
 */

namespace core;

/**
 * This class provides an interface to all modules.
 *
 * @category  CaptainHook
 * @package   Core
 * @author    Christophe Beveraggi (beve) and Nicolas Dimitrijevic (niclone)
 * @license   LGPLv3
 * @link      http://github.com/croll/captainhook
 *
 * Modules are based on this interface. It allows to define important informations about
 * module and provides essential functions as install/uninstall etc.
 */
abstract class ModuleDefinition {

		/** @var string Module name */
		public $name;
		/** @var float Module version */
		public $version;
		/** @var string Module descrption */
		public $description;
		/** @var int Internal module id */
		public $id;
		/** @var array List of modules needed to be installed */
		public $dependencies = array();
		/** @var array Informations about modules */
		private static $_cache = NULL;

		/**
		 * Initialize the class
		 */
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


		/**
		 * Install the module.
		 * Register the informations into the database
		 */
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


		/**
		 * Check and register hooks provided by the module.
		 * They can be functions in the Main.php with name like hook_[a-z]+
		 * They also can be declared into a Hooks.php inside the module main folder.
		 *
		 * @return void
		 */
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

		/**
		 * Unregister module hooks from CaptainHook.
		 * All informations about module are deleted from database, so use it carefully. 
		 * If you want disable the module for possible later use, use 'disable' function instead.
		 *
		 * @return void
		 */
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

		/**
		 * Enable the module.
		 * A module can be installed but not activated.
		 *
		 * @return void
		 */
		function enable() {
			if (!$this->id) throw new \Exception("enabling a module which don't have an id ?");

			// Enable module
			$affected=Core::$db->exec('UPDATE ch_module SET `active` = 1 WHERE mid=?', 
                         array($this->id));
      if ($affected <= 0) throw new \Exception("Module was not found in database");
		}

		/**
		 * Disable the module.
		 *
		 * @return void
		 */
		function disable() {
			if (!$this->id) throw new \Exception("disable a module which don't have an id ?");

			// Disable module
			$affected=Core::$db->exec('UPDATE ch_module SET `active` = 0 WHERE mid=?', 
                         array($this->id));
      if ($affected <= 0) throw new \Exception("Module was not found in database");
		}

}
