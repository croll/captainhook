<?php  //  -*- mode:php; tab-width:2; c-basic-offset:2; -*-
/**
 * CaptainHook
 *
 * PHP Version 5
 *
 * @category  CaptainHook
 * @package   Core 
 * @author    Christophe Beveraggi (beve) and Nicolas Dimitrijevic (niclone)
 * @copyright 2011 CROLL (http://www.croll.fr)
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

/* the full path of CaptainHook on the filesystem */
define('CH_ROOTDIR', dirname(dirname(__FILE__)));

/* the full path of CaptainHook mod directory on the filesystem */
define('CH_MODDIR', CH_ROOTDIR.'/mod');

/**
 * Use adodb lowecase syntax
 */

define('ADODB_ASSOC_CASE', 0);

/**
 * Store number of class loaded, for performance benchmarking purpose.
 */

$class_autoload_count=0;

/**
 * @internal: Autoload core and module classes when needed.
 */

function __autoload_custom($className) {
  if (strncmp('core\\', $className, 5)
      && strncmp('mod\\', $className, 4)) return false;

	if (!preg_match('/^[a-zA-Z0-9\\\\_]+$/', $className)) throw new \Exception("Bad class name");

  $fileName=dirname(__FILE__).'/../'.str_replace('\\', '/', $className).'.php';

	if (is_file($fileName) && is_readable($fileName)) {
    $GLOBALS['class_autoload_count']++;
		require_once($fileName);
	} else {
		throw new \Exception("Unable to load class $className");
	}
}

spl_autoload_register('\core\__autoload_custom');

/**
 * This class provides a bootstrap for all modules who wish to interface 
 * with CaptainHook.
 *
 * The boostraper is responsible for setting up the configuration, the db
 * interface. It also provides convenience log function.
 */
class Core {

	/** @var object the db object, used in whole application and modules to perform database queries */
	public static $db;

	/**
	 * Convenience method that does the complete initialization for CaptainHook.
	 *
	 * This method will load and init AdoDB, set up timezone and trigger main hooks. 
	 *
	 * @api
	 *
	 * @return void
	 */
	public static function init() {
		if (!empty($_GET["page"]) && !preg_match("/^[a-zA-Z]+$/", $_GET["page"])) die("No way");
		if (!is_file(CH_ROOTDIR.'/conf/general.conf'))
			die('Config file '.CH_ROOTDIR.'/conf/general.conf'.' does not exist. Take a look at '.CH_ROOTDIR.'/conf/general.conf.dist');
		$ini = parse_ini_file(CH_ROOTDIR.'/conf/general.conf', true);

		/** Database */
		if (isset($ini['database'])) {
			require_once(CH_ROOTDIR.'/ext/pdoex/mysql.php');
			self::$db = new \MySQL($ini['database']);
		}

		/** Timezone */
		if (isset($ini['general']['timezone']))
			date_default_timezone_set($ini['general']['timezone']);

		/** Trigger hook */
		Hook::call('core_init');
    if (isset($_SERVER) && isset($_SERVER['REQUEST_URI']))
      Hook::call('core_init_http');
		else
      Hook::call('core_init_shell');
	}

	/**
	 * Logs the string passed as argument into apache error log (or stderr if executed from a shell).
	 *
	 * This method is useful to perform quick debug/trace. 
	 *
	 * @param string $msg
	 *
	 * @api
	 *
	 * @return void
	 */
	public static function log($msg) {
    if (is_string($msg)) error_log($msg);
    else error_log(var_export($msg, true));
	}

}
