<?php

namespace core;

class Hook {

	private static $callbacks = NULL;

  /*
   * Register a hook listener.
   * @name: the name of the hook we want to listen
   * @callback: the static method to call, must be of the form: classname::methodname
   * @id_module: if the listener is a module, the id of this module
   * @positon: position of the listener in de "call stack"
   */
  public static function registerHookListener($name, $callback, $userdata = NULL, $id_module = NULL, $position = 0) {
    Core::$db->Execute("INSERT INTO `ch_hook` (`name`, `callback`, `userdata`, `mid`, `position`) VALUES (?,?,?,?,?)",
                       array($name, $callback, $userdata, $id_module, $position));
    self::$callbacks = NULL;
		return (!Core::$db->ErrorMsg()) ? true : false;
  }

  /*
   * Unregister a hook listener.
   * @name: the name of the hook that we were listening
   * @callback: (must be like it was for registerHookListener)
   * @id_module: if the listener is a module, the id of this module
   */
  public static function unregisterHookListener($name, $callback, $id_module = NULL) {
    Core::$db->Execute("DELETE FROM `ch_hook` WHERE `name`=? AND `callback`=? AND `mid`=?",
                       array($name, $callback, $id_module));
    self::$callbacks = NULL;
		return (!Core::$db->ErrorMsg()) ? true : false;
  }
  /*
   * Unregister all hook listener for speficied module.
   * @id_module: if the listener is a module, the id of this module
   */
  public static function unregisterModuleListeners($id_module) {
    Core::$db->Execute("DELETE FROM `ch_hook` WHERE `mid`=?",
                       array($id_module));
    self::$callbacks = NULL;
		return (!Core::$db->ErrorMsg()) ? true : false;
  }

  /*
   * Check if hook exists.
   * @name: the name of the hook that we were listening
   * @id_module: if the listener is a module, the id of this module
   * @callback: (must be like it was for registerHookListener)
   * @id_module: if the listener is a module. the id of this module
   */
	public static function checkHookListener($name, $callback, $id_module = NULL) {
		$exist = Core::$db->GetOne("SELECT hid FROM `ch_hook` WHERE `name`=? AND `callback`=? AND `mid`=?",
												array($name, $callback, $id_module));	
		return ($exist) ? true : false;
	}

	/* Change the listener position in the "call stack"
   * @name: the name of the hook
   * @callback: the static method to call, must be of the form: classname::methodname
   * @id_module: if the listener is a module, the id of this module
   * @positon: position of the listener in de "call stack"
	 */
	public static function changeListenerPosition($name, $callback, $id_module = NULL, $position = 0) {
    Core::$db->Execute("UPDATE `ch_hook` SET `position` = ? WHERE `name`=? AND `callback`=? AND `mid`=?",
                       array($position, $name, $callback, $id_module));
		return (!Core::$db->ErrorMsg()) ? true : false;
	}

  /*
   * Call a hook. This function will propagate the hook to all the listeners.
	 * reference to self::result in passed to each listener function in addition to function args
	 * if listener return true, propagation is stopped
   * @name: the name of the hook that we were listening
   */
  public static function call($name) {
    Core::log("Hook: call: ".$name);

    $args=func_get_args();
    array_unshift(&$args, $name);

    if (!self::$callbacks) self::initCache();
    if (isset(self::$callbacks[$name])) {
      foreach(self::$callbacks[$name] as $row) {
				Core::log("Hook: calling: ".$row['callback']);
        $args[1]=$row['userdata'];
				if (call_user_func_array($row['callback'], $args) == 'stop')
					return;
      }
    }
  }

  /*
   * fill self::$callbacks
   */
  private static function initCache() {
		$callbacks = Core::$db->GetAll("SELECT `ch_hook`.*, `ch_module`.`name` AS `module_name` FROM `ch_hook` LEFT JOIN `ch_module` ON `ch_hook`.`mid` = `ch_module`.`mid` ORDER BY `position`,`hid`");
    self::$callbacks=array();
    foreach($callbacks as $callback) {
      if (!isset(self::$callbacks[$callback['name']])) self::$callbacks[$callback['name']]=array();
      self::$callbacks[$callback['name']][]=$callback;
    }
  }
}
