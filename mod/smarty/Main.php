<?php  //  -*- mode:php; tab-width:2; c-basic-offset:2; -*-

namespace mod\smarty {

	class Main {
		public static $smarty;
		
		public static function hook_core_init_http($hookname, $userdata) {
			$moddir=dirname(__FILE__);
			define("SMARTY_DIR", $moddir.'/smarty/libs/');
			require_once(SMARTY_DIR.'/Smarty.class.php');
			self::$smarty = $sm = new \Smarty();
			$sm->template_dir = $moddir.'/../../';
			$sm->compile_dir = $moddir.'/templates_c/';
			$sm->config_dir = $moddir.'/conf/';
			$sm->cache_dir = $moddir.'/cache/';
			
			self::loadPlugins();    
		}
		
		/*
		 * HOOK
		 * when installing a module, we check if this module have smarty plugins, to install them
		 */
		public static function hook_core_ModuleDefinition_install($hookname, $userdata, $module_definition) {
			$moddir = dirname(__FILE__).'/../'.$module_definition->name;
			
			
			// Install smarty plugins of the Main.php of the installing module
			if (is_file($moddir.'/Main.php')) {
				require_once($moddir.'/Main.php');
				$classname='\\mod\\'.$module_definition->name.'\\Main';
				$methods = get_class_methods($classname);
				foreach($methods as $method) {
					if (!strncmp("smartyFunction_", $method, 15))
						self::registerPlugin($module_definition->id, substr($method, 15), 'function',
																 '\\mod\\'.$module_definition->name.'\\Main::'.$method);
					else if (!strncmp("smartyBlock_", $method, 12))
						self::registerPlugin($module_definition->id, substr($method, 12), 'block',
																 '\\mod\\'.$module_definition->name.'\\Main::'.$method);
					else if (!strncmp("smartyCompiler_", $method, 15))
						self::registerPlugin($module_definition->id, substr($method, 15), 'compiler',
																 '\\mod\\'.$module_definition->name.'\\Main::'.$method);
					else if (!strncmp("smartyModifier_", $method, 15))
						self::registerPlugin($module_definition->id, substr($method, 15), 'modifier',
																 '\\mod\\'.$module_definition->name.'\\Main::'.$method);
				}
			}
			
			// Install smarty plugins of SmartyPlugins.php of the installing module
			if (is_file($moddir.'/SmartyPlugins.php')) {
				require_once($moddir.'/SmartyPlugins.php');
				$classname='\\mod\\'.$module_definition->name.'\\SmartyPlugins';
				$methods = get_class_methods($classname);
				foreach($methods as $method)
					if (!strncmp("function_", $method, 9))
						self::registerPlugin($module_definition->id, substr($method, 9), 'function',
																 '\\mod\\'.$module_definition->name.'\\SmartyPlugins::'.$method);
					else if (!strncmp("block_", $method, 6))
						self::registerPlugin($module_definition->id, substr($method, 6), 'block',
																 '\\mod\\'.$module_definition->name.'\\SmartyPlugins::'.$method);
					else if (!strncmp("Compiler_", $method, 9))
						self::registerPlugin($module_definition->id, substr($method, 9), 'compiler',
																 '\\mod\\'.$module_definition->name.'\\SmartyPlugins::'.$method);
					else if (!strncmp("Modifier_", $method, 9))
						self::registerPlugin($module_definition->id, substr($method, 9), 'modifier',
																 '\\mod\\'.$module_definition->name.'\\SmartyPlugins::'.$method);
			}
			
			// Install smarty templates hooks
			$self_id_module = \core\Core::$db->GetOne("SELECT `mid` FROM `ch_module` WHERE `name` = ?", array('smarty'));
			if (is_dir($moddir.'/hooks_templates/')) {
				$tpls=scandir($moddir.'/hooks_templates/');
				foreach($tpls as $tpl) {
					$matches=array();
					if (preg_match('/^([^.].*)\.tpl$/', $tpl, $matches)) {
						\core\Hook::registerHookListener('mod_smarty_hook_mod_'.$module_definition->name.'_'.$matches[1], '\\mod\\smarty\\Main::_hook_template', 'mod/'.$module_definition->name.'/hooks_templates/'.$tpl, $self_id_module);
					}
				}
			}
		}
		
		/*
   * HOOK
   * when uninstalling a module, we check if this module have smarty plugins, to uninstall them
   */
		public static function hook_core_ModuleDefinition_uninstall($hookname, $userdata, $module_definition) {
			$moddir = dirname(__FILE__).'/../'.$module_definition->name;
			// uninstall smarty templates hooks
			if (is_dir($moddir.'/hooks_templates/')) {
				$tpls=scandir($moddir.'/hooks_templates/');
				foreach($tpls as $tpl) {
					$matches=array();
					if (preg_match('/^([^.].*)\.tpl$/', $tpl, $matches)) {
						\core\Hook::unregisterHookListener($matches[1], '\\mod\\smarty\\Main::_hook_template');
					}
				}
			}
			
			self::unregisterPlugin($module_definition->id);
		}
		
		private static function registerPlugin($id_module, $name, $type, $method) {
			\core\Core::$db->execute('INSERT INTO `ch_smarty_plugins` (`id_module`, `name`, `type`, `method`) VALUES (?,?,?,?)',
															 array($id_module, $name, $type, $method));
		}
		
		private static function unregisterPlugin($id_module, $name = null, $type = null, $method = null) {
			$query = 'DELETE FROM `ch_smarty_plugins` WHERE `id_module`=?';
			$vals = array($id_module);
			if ($name !== null) {
				$query.= ' AND `name`=?';
				$vals[]=$name;
			}
			if ($type !== null) {
				$query.= ' AND `type`=?';
				$vals[]=$type;
			}
			if ($method !== null) {
				$query.= ' AND `method`=?';
				$vals[]=$method;
			}
			
			\core\Core::$db->execute($query, $vals);
		}
		
		private static function loadPlugins() {
			$plugins=\core\Core::$db->getAll('SELECT `type`, `name`, `method` FROM `ch_smarty_plugins`');
			foreach($plugins as $plugin) {
				self::$smarty->registerPlugin($plugin['type'], $plugin['name'], $plugin['method']);
			}
		}
		
		
		public static function _hook_template($hookname, $userdata, $params, $template, $result) {
			$name=str_replace('mod_smarty_hook_', '', $hookname);
			error_log("_hook_template: $userdata ($hookname)");
			$result->display.='<span class="'.$name.'">'.self::$smarty->fetch($userdata).'</span>';
		}
		
		/* Smarty Plugins */
		public static function smartyFunction_hook($params, $template) {
			if (!isset($params['mod'])) throw new \Exception("smarty hook must have a 'mod' parameter");
			if (!isset($params['name'])) throw new \Exception("smarty hook must have a 'name' parameter");
			$result=new \stdClass();
			$result->display='';
			\core\Hook::call('mod_smarty_hook_mod_'.$params['mod'].'_'.$params['name'], $params, $template, $result);
			return $result->display;
		}
		
	}
}

// This function is outside of any namespace, because smarty seem to not like it
namespace {
	function tplextends($extended_module, $extended_tpl) {
		return 'mod/'.$extended_module.'/templates/'.$extended_tpl.'.tpl';
	}
}
