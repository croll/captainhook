<?php  //  -*- mode:php; tab-width:2; c-basic-offset:2; -*-
/**
 * CaptainHook
 *
 * PHP Version 5
 *
 * @category  CaptainHook
 * @package   mod 
 * @author    Christophe Beveraggi (beve)
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

namespace mod\config;

class Main {

	private static $_config = null;

	public static function get($module, $name, $defaultValue=null, $user=null) {

		$noDefaultVaule = false;

    if (self::$_config === null) 
			self::_cacheConfiguration();

		extract(self::_checkParams($module, $user));

		if (func_num_args() == 2 || (func_num_args == 3 && $user != null)) {
			$noDefaultValue = true;
		}

		if ($noDefaultValue && ((!is_null($user) && !isset(self::$_config[$module][$user][$name])) || (is_null($user) && !isset(self::$_config[$module]['general'][$name])))) {
				throw new \Exception('No match for config entry');
				return;
		} else if (!is_null($user)) {
			return (isset(self::$_config[$module][$user][$name])) ? self::$_config[$module][$user][$name] : $defaultValue;
		} else {
			return (isset(self::$_config[$module]['general'][$name])) ? self::$_config[$module]['general'][$name] : $defaultValue;
		} 
				
	}

	public static function set($module, $name, $value, $user=null) {

    if (self::$_config === null) 
			self::_cacheConfiguration();

		extract(self::_checkParams($module, $user));

		if (!is_null($user)) {
			if (!isset(self::$_config[$module][$user][$name])) {
			  \core\Core::$db->exec('INSERT INTO "ch_config" ("id_module", "name", "value", "id_user") VALUES (?,?,?,?)',
															array($module, $name, $value, $user));
			} else {
			  \core\Core::$db->exec('UPDATE "ch_config" SET "value" = ? WHERE "id_module" = ? AND "name" = ? AND "id_user" = ?',
															array($value, $module, $name, $user));
			}
		} else {
			if (!isset(self::$_config[$module]['general'][$name])) {
				 \core\Core::$db->exec('INSERT INTO "ch_config" ("id_module", "name", "value") VALUES (?,?,?)',
																array($module, $name, $value));
			} else {
			  \core\Core::$db->exec('UPDATE "ch_config" SET "value" = ? WHERE "id_module" = ? AND "name" = ? AND "id_user" IS NULL ',
															array($value, $module, $name));
			}
		}

		self::$_config = null;
	}

	public static function delete($module, $name, $user=null) {

    if (self::$_config === null) 
			self::_cacheConfiguration();

		extract(self::_checkParams($module, $user));

		if (!is_null($user)) {
			 \core\Core::$db->exec('DELETE FROM "ch_config" WHERE "id_module" = ? AND "name" = ? AND "id_user" = ?',
															array($module, $name, $user));
		} else {
			 \core\Core::$db->exec('DELETE FROM "ch_config" WHERE "id_module" = ? AND "name" = ? AND "id_user" IS NULL',
															array($module, $name));
		}

		self::$_config = null;
	}

	private static function _cacheConfiguration() {
		foreach (\core\Core::$db->fetchAll('SELECT * FROM "ch_config"') as $conf) {
			if(!is_null($conf['id_user'])) {
				self::$_config[$conf['id_module']][$conf['id_user']][$conf['name']] = $conf['value'];
			} else {
				self::$_config[$conf['id_module']]['general'][$conf['name']] = $conf['value'];
			}
		}
	}

	private static function _checkParams($module, $user) {

		if (!is_int($module)) {
			$module = \core\Core::$db->fetchOne('SELECT "mid" FROM "ch_module" WHERE "name"=?', array($module));
			if (!$module) {
				throw new \Exception('No matching module found');
			}
		}

		if (!is_int($user) && !is_null($user)) {
			$user = \core\Core::$db->fetchOne('SELECT "uid" FROM "ch_user" WHERE "login"=?', array($user));
			if (!$user) {
				throw new \Exception('No matching user found');
			}
		}

		return array('module' => $module, 'user' => $user);
	}

}
