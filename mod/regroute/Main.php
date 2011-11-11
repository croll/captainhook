<?php  //  -*- mode:php; tab-width:2; c-basic-offset:2; -*-

namespace mod\regroute;

class Main {
  private static $regroutes;

  private static function regroutes() {
    if (self::$regroutes === null) 
      self::$regroutes=\core\Core::$db->getAll('SELECT * FROM `ch_regroute`');
    return self::$regroutes;
  }

  public static function hook_core_init_http($hookname, $userdata) {
    foreach (self::regroutes() as $regroute) {
      $matches=array();
      if (preg_match($regroute['regexp'], $_SERVER['REQUEST_URI'], $matches))
        \core\Hook::call($regroute['hook'], $matches);
    }
  }

  public static function registerRoute($id_module, $regexp, $hook) {
    \core\Core::$db->execute('INSERT INTO `ch_regroute` (`id_module`, `regexp`, `hook`) VALUES (?,?,?)',
                             array($id_module, $regexp, $hook));
    self::$regroutes=null;
  }

  public static function unregister($id_module, $regexp = NULL, $hook = NULL) {
    $query = 'DELETE FROM `ch_regroute` WHERE `id_module`=?';
    $vals = array($id_module);
    if ($regexp !== null) {
      $query.= ' AND `regexp`=?';
      $vals[]=$regexp;
    }
    if ($hook !== null) {
      $query.= ' AND `hook`=?';
      $vals[]=$hook;
    }
    \core\Core::$db->execute($query, $vals);
    self::$regroutes=null;
  }

}

