<?php

namespace mod\ajax;

class Main {

  public static function hook_mod_ajax($hookname, $userdata, $params) {
		$args = preg_split('#/#', $params[1]);
		switch($args[0]) {
			case 'call':
				if (!isset($args[1]) || !is_string($args[1]) || !isset($args[2]) || !is_string($args[1])) {
					echo -1;
					return false;
				}
				echo json_encode(self::call($args[1], $args[2], $_REQUEST['params']));
				break;
			default:
				echo null;
		}
  }

	public static function call($mod, $method, $params=null) {
		if (!is_array($params)) $params = array($params);
		try {
			return call_user_func_array('\\mod\\'.$mod.'\\Ajax::'.$method, array($params));
		} catch (\Exception $e) {
      throw $e;
		}
	}

}
