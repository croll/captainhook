<?php  //  -*- mode:php; tab-width:2; c-basic-offset:2; -*-

namespace core;

class ModuleInstaller {

	private static $definitions_cache = array();

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

	static function getModuleDefinition($name) {
		return isset(self::$definitions_cache[$name]) ? self::$definitions_cache[$name] : self::init($name, true);
	}

	static function getModuleDependencies($name) {
		$dependencies = array();
    $definition = self::getModuleDefinition($name);
    foreach($definition->dependencies as $d) $dependencies[$d] = self::getModuleDefinition($d);
		return $dependencies;
	}

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

	static function moduleExists($name) {
		$moddir = dirname(__FILE__).'/../mod';
		$moduleName = '';
		foreach(self::listModulesOnFilesystem() as $mod) {
			if (strtolower($name) == strtolower($mod))
				$moduleName = $mod;
		}
		return (is_dir($moddir.'/'.$moduleName) && is_file($moddir.'/'.$moduleName.'/Main.php') && is_file($moddir.'/'.$moduleName.'/ModuleDefinition.php')) ? $moduleName : false;
	}

	static function process($name, $action) {
    $definition = self::getModuleDefinition($name);
    $definition->$action();
	}
}
