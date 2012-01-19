<?php  //  -*- mode:php; tab-width:2; c-basic-offset:2; -*-

namespace mod\regroute;

class Main {
  private static $regroutes;

	const flag_html = 1;
	const flag_xmlhttprequest = 2;

  private static function regroutes() {
    if (self::$regroutes === null) 
      self::$regroutes=\core\Core::$db->fetchAll('SELECT * FROM "ch_regroute"');
    return self::$regroutes;
  }

  public static function hook_core_init_http($hookname, $userdata) {
    session_start();

		$xmlhttprequest = (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest');
		$flags = $xmlhttprequest ? self::flag_xmlhttprequest : self::flag_html;


    foreach (self::regroutes() as $regroute) {
      $matches=array();
      if (preg_match($regroute['regexp'], $_SERVER['REQUEST_URI'], $matches)) {
				if (($xmlhttprequest && ($regroute['flags'] & self::flag_xmlhttprequest))
						|| (!$xmlhttprequest && ($regroute['flags'] & self::flag_html)))
					\core\Hook::call($regroute['hook'], $matches, $flags);
			}
    }
  }

	/*
	 * Register a new route
	 * @param int id_module the id of the module
	 * @param string regexp the regular expression that must match the url you want to catch
	 * @param string hook the name of the hook that will be call if the url match
	 * @param int flags some flags. Can contain flag_html or flag_xmlhttprequest. Use pipe to combine thems.
	 */
  public static function registerRoute($id_module, $regexp, $hook,
																			 $flags=self::flag_html) {
    \core\Core::$db->exec('INSERT INTO "ch_regroute" ("id_module", "regexp", "hook", "flags") VALUES (?,?,?,?)',
													array($id_module, $regexp, $hook, $flags));
    self::$regroutes=null;
  }

  public static function unregister($id_module, $regexp = NULL, $hook = NULL) {
    $query = 'DELETE FROM "ch_regroute" WHERE "id_module"=?';
    $vals = array($id_module);
    if ($regexp !== null) {
      $query.= ' AND "regexp"=?';
      $vals[]=$regexp;
    }
    if ($hook !== null) {
      $query.= ' AND "hook"=?';
      $vals[]=$hook;
    }
    \core\Core::$db->exec($query, $vals);
    self::$regroutes=null;
  }

}

