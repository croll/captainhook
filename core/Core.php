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

/* the full path of CaptainHook on the filesystem */
define('CH_ROOTDIR', dirname(dirname(__FILE__)));

/* the full path of CaptainHook mod directory on the filesystem */
define('CH_MODDIR', CH_ROOTDIR.'/mod');

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
 * @category  CaptainHook
 * @package   Core
 * @author    Christophe Beveraggi (beve) and Nicolas Dimitrijevic (niclone)
 * @license   LGPLv3
 * @link      http://github.com/croll/captainhook
 *
 * The boostraper is responsible for setting up the configuration, the db
 * interface. It also provides convenience log function.
 */
class Core {

	/** @var object the db object, used in whole application and modules to perform database queries */
	public static $db;

	public static $ini;

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
    error_reporting(E_ALL ^ E_STRICT);
    //ini_set('session.gc_maxlifetime', 30*60); // half hour
    //ini_set('session.cookie_lifetime', 30*60); // half hour

		if (!empty($_GET["page"]) && !preg_match("/^[a-zA-Z]+$/", $_GET["page"])) die("No way");
		if (!is_file(CH_ROOTDIR.'/conf/general.conf'))
			die('Config file '.CH_ROOTDIR.'/conf/general.conf'.' does not exist. Take a look at '.CH_ROOTDIR.'/conf/general.conf.dist');
    self::$ini = parse_ini_file(CH_ROOTDIR.'/conf/general.conf', true);

		/** Database */
		if (isset(self::$ini['database'])) {
      if (self::$ini['database']['type'] == 'mysql') {
        require_once(CH_ROOTDIR.'/ext/pdoex/mysql.php');
        self::$db = new \MySQL(self::$ini['database']);
      } else if (self::$ini['database']['type'] == 'pgsql') {
        require_once(CH_ROOTDIR.'/ext/pdoex/pgsql.php');
        self::$db = new \PgSQL(self::$ini['database']['type'].':dbname='.self::$ini['database']['dbname']
                               .';host='.self::$ini['database']['host'],
                               self::$ini['database']['username'], self::$ini['database']['password']);
      } else {
        require_once(CH_ROOTDIR.'/ext/pdoex/pdoex.php');
        self::$db = new \PDOEX(self::$ini['database']['type'].':dbname='.self::$ini['database']['dbname']
                               .';host='.self::$ini['database']['host'],
                               self::$ini['database']['username'], self::$ini['database']['password']);
      }
		}

		/** Timezone */
		if (isset(self::$ini['general']['timezone']))
			date_default_timezone_set(self::$ini['general']['timezone']);

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
	 * Logs are stored in the standard website error log.
	 *
	 * @param string $msg
	 *
	 * @api
	 *
	 * @return void
	 */
	public static function log($msg) {
		$remote = (isset($_SERVER['REMOTE_ADDR'])) ? '[client '.$_SERVER['REMOTE_ADDR'].']' : '';
		$log = '['.date('D M d H:i:s Y').'] [debug] '.$remote.' ';
		$bt = debug_backtrace();
		$log .= $bt[1]['class'].$bt[1]['type'].$bt[1]['function'].' -- ';
    if (is_string($msg)) $log .= $msg;
    else {
			ob_start();
			print_r($msg);
			$log .= ob_get_contents();
			ob_end_clean();
		}
		if (!isset(self::$ini['general']) || !isset(self::$ini['general']['logfile']) || empty(self::$ini['general']['logfile'])) {
			$stderr = fopen('php://stderr', 'w'); 
		} else {
			$stderr = fopen(dirname(__FILE__).'/../logs/'.self::$ini['general']['logfile'], 'a+'); 
		} 
		fwrite($stderr, $log."\n"); 
   	fclose($stderr); 
	}

}
