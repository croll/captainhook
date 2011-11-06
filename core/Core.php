<?php

namespace core;

define('ADODB_ASSOC_CASE', 0);

$class_autoload_count=0;
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

class Core {

	public static $db;
	private static $_rootDir;

	public static function init($initDb = true, $triggerHook = true) {
		if (!empty($_GET["page"]) && !preg_match("/^[a-zA-Z]+$/", $_GET["page"])) die("No way");
			self::$_rootDir = realpath(dirname(__FILE__).'/../');
		$ini = parse_ini_file(self::$_rootDir.'/conf/general.conf', true);
		/* Database */
		if (isset($ini['database']) && $initDb) {
			require_once(dirname(__FILE__).'/../ext/adodb5/adodb-exceptions.inc.php');
			require_once(dirname(__FILE__).'/../ext/adodb5/adodb.inc.php');
			$dbObj = ADONewConnection($ini['database']['type']);
			try {
				$dbObj->Connect($ini['database']['host'], $ini['database']['user'], $ini['database']['password'], $ini['database']['dbname']);
			} catch (\Exception $e) {
				Core::log($e->getMessage());
				return false;
			}
			$dbObj->SetFetchMode(ADODB_FETCH_ASSOC);
			self::$db = $dbObj;
		}

		/* Timezone */
		if (isset($ini['general']['timezone']))
			date_default_timezone_set($ini['general']['timezone']);

		/* Trigger hook */
		if ($triggerHook)
			Hook::call('core_init');
    if (isset($_SERVER) && isset($_SERVER['REQUEST_URI'])) {
      Hook::call('core_init_http');
      Hook::call('core_process_http');
		} else
      Hook::call('core_init_shell');

	}

	public static function log($msg) {
		if ( (!is_dir(self::$_rootDir.'/logs')) || (!is_writable(self::$_rootDir.'/logs')) ) die ('Log directory '.self::$_rootDir.'/logs does not exist or is not writable.');
		$logFile = fopen(self::$_rootDir.'/logs/trace.log', 'a+');
		if ($logFile) {
			if (!is_array($msg)) {
				fputs($logFile,'['.date('D m Y H:i:s').'] -- '.$msg."\n");
			} else {
				ob_start();
				print_r($msg);
				$buff = ob_get_contents();
				ob_end_clean();
				fputs($logFile,$buff."\n");
			}
		}
	}

	public static function getRootDir() {
		return self::$_rootDir;
	}

}
