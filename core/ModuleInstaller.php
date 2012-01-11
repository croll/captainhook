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
 * This class provides convenient functions to manage modules.
 *
 * @category  CaptainHook
 * @package   Core
 * @author    Christophe Beveraggi (beve) and Nicolas Dimitrijevic (niclone)
 * @license   LGPLv3
 * @link      http://github.com/croll/captainhook
 *
 * ModuleInstall is used by the moduleInstaller tool to init, check, list modules.
 */

class ModuleInstaller {

	/** @var array Information about modules stored to avoid multiple daabase interractions */
	private static $definitions_cache = array();

	/** 
	 * Initialize the mechanism
	 *
	 * @param string $name module name
	 * @param bool $init Init the ModuleDefinition class ok Main
	 *
	 * @return object ModuleManager object
	 */
	private static function init($name, $definition=false) {
    $moddir = dirname(__FILE__).'/../mod/'.$name;
		$classfilename = $moddir.'/'.($definition ? 'ModuleDefinition.php' : 'Main.php');
		$classname = '\\mod\\'.$name.'\\'.($definition ? 'ModuleDefinition' : 'Main');
    if (!is_file($classfilename) || !is_readable($classfilename)) throw new \Exception("can't open: $classfilename");
			require_once($classfilename);
		try {
			$modMan = new $classname;
		} catch (\Exception $e) {
			Core::log("ModuleInstaller::init -- Unable to init ${name}$def");
			Core::log("                      -- ".$e->getMessage());
      throw $e;
		}
		self::$definitions_cache[$name] = $modMan;
    return $modMan;
	}

	/** 
	 * Initialize the mechanism
	 *
	 * @param string $name module name
	 *
	 * @return void
	 */
	static function getModuleDefinition($name) {
		return isset(self::$definitions_cache[$name]) ? self::$definitions_cache[$name] : self::init($name, true);
	}

	/** 
	 * Get module dependencies.
	 *
	 * @param string $name module name
	 *
	 * @return array List of modules
	 */
	static function getModuleDependencies($name) {
		$dependencies = array();
    $definition = self::getModuleDefinition($name);
    foreach($definition->dependencies as $d) $dependencies[$d] = self::getModuleDefinition($d);
		return $dependencies;
	}

	/** 
	 * List modules present in the module directory.
	 *
	 * @param string $name module name
	 *
	 * @return array List of modules
	 *
	 * They can be installed or not. 
	 */
	static function listModulesOnFilesystem() {
		$moddir = dirname(__FILE__).'/../mod';
		$dirs = scandir($moddir);
		$mods = array();
		if (!is_array($dirs) || sizeof($dirs) < 1) return NULL;
		foreach($dirs as $d) {
			if (substr($d,0,1) != '.' &&  is_dir($moddir.'/'.$d) && is_file($moddir.'/'.$d.'/Main.php') && is_file($moddir.'/'.$d.'/ModuleDefinition.php')) {
				$mods[] = $d;
			}
		}
		return $mods;
	}

	/** 
	 * Check if the module exists of the filesystem.
	 *
	 * @param $name string $name module name
	 *
	 * @return array List of modules
	 *
	 * It only check if module is present. 
	 */
	static function moduleExists($name) {
		$moddir = dirname(__FILE__).'/../mod';
		$moduleName = '';
		foreach(self::listModulesOnFilesystem() as $mod) {
			if (strtolower($name) == strtolower($mod))
				$moduleName = $mod;
		}
		return (is_dir($moddir.'/'.$moduleName) && is_file($moddir.'/'.$moduleName.'/Main.php') && is_file($moddir.'/'.$moduleName.'/ModuleDefinition.php')) ? $moduleName : false;
	}

	/** 
	 * Get a module and call a method.
	 *
	 * @param string $name module name
	 * @param string 
	 *
	 * @return void
	 *
	 * No check are performed, it calls a method because you ask for it. Assume.
	 */
	static function process($name, $action) {
    $definition = self::getModuleDefinition($name);
    $definition->$action();
	}
}
